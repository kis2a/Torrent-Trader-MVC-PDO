<?php
class Adminbans extends Controller
{

    public function __construct()
    {
        Auth::user();
        Auth::isStaff();
        // $this->userModel = $this->model('User');
        $this->logsModel = $this->model('Logs');
        $this->valid = new Validation();
    }

    public function index()
    {
        Redirect::to(URLROOT."/admincp");
    }

    public function ip()
    {
        $do = $_GET['do'];
        if ($do == "del") {
            if (!@count($_POST["delids"])) {
                show_error_msg(Lang::T("ERROR"), Lang::T("NONE_SELECTED"), 1);
            }
            $delids = array_map('intval', $_POST["delids"]);
            $delids = implode(', ', $delids);
            $res = DB::run("SELECT * FROM bans WHERE id IN ($delids)");
            while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
                DB::run("DELETE FROM bans WHERE id=$row[id]");
                # Needs to be tested...
                if (Ip::is_ipv6($row["first"]) && Ip::is_ipv6($row["last"])) {
                    $first = Ip::long2ip6($row["first"]);
                    $last = Ip::long2ip6($row["last"]);
                } else {
                    $first = long2ip($row["first"]);
                    $last = long2ip($row["last"]);
                }
                Logs::write("IP Ban ($first - $last) was removed by $_SESSION[id] ($_SESSION[username])");
            }
            Session::flash('info', "Ban(s) deleted.", URLROOT."/adminbans/ip");
        }

        if ($do == "add") {
            $first = trim($_POST["first"]);
            $last = trim($_POST["last"]);
            $comment = trim($_POST["comment"]);
            if ($first == "" || $last == "" || $comment == "") {
                Session::flash('info', Lang::T("MISSING_FORM_DATA") . ". Go back and try again",  URLROOT."/adminbans/ip");
            }
            $comment = $comment;
            $added = Helper::get_date_time();
            $bins = DB::run("INSERT INTO bans (added, addedby, first, last, comment) VALUES(?,?,?,?,?)", [$added, $_SESSION['id'], $first, $last, $comment]);
            $err = $bins->errorCode();
            switch ($err) {
                case 1062:
                    Session::flash('info', "Duplicate ban.",  URLROOT."/adminbans/ip");
                    break;
                case 0:
                    Session::flash('info', "Ban added.",  URLROOT."/adminbans/ip");
                    break;
                default:
                    Session::flash('info', Lang::T("THEME_DATEBASE_ERROR") . " " . htmlspecialchars($bins->errorInfo()),  URLROOT."/adminbans/ip");
            }
        }

        $count = get_row_count("bans");
        list($pagertop, $pagerbottom, $limit) = pager(50, $count, "/adminbans/ip?"); // 50 per page
        $res = DB::run("SELECT bans.*, users.username FROM bans LEFT JOIN users ON bans.addedby=users.id ORDER BY added $limit");

        $data = [
            'title' => Lang::T("BANNED_IPS"),
            'count' => $count,
            'pagerbottom' => $pagerbottom,
            'pagertop' => $pagertop,
            'res' => $res,
        ];
        $this->view('bans/admin/ip', $data, 'admin');
    }

    public function email()
    {
        $remove = (int) $_GET['remove'];
        $valid = new Validation();
        if ($valid->validId($remove)) {
            DB::run("DELETE FROM email_bans WHERE id=$remove");
            Logs::write(sprintf(Lang::T("EMAIL_BANS_REM"), $remove, $_SESSION["username"]));
        }
        if ($_GET["add"] == '1') {
            $mail_domain = trim($_POST["mail_domain"]);
            $comment = trim($_POST["comment"]);
            if (!$mail_domain || !$comment) {
                show_error_msg(Lang::T("ERROR"), Lang::T("MISSING_FORM_DATA") . ".", 0);
                require APPROOT . '/views/admin/footer.php';
                die;
            }
            $mail_domain = $mail_domain;
            $comment = $comment;
            $added = TimeDate::get_date_time();
            $ins = DB::run("INSERT INTO email_bans (added, addedby, mail_domain, comment) VALUES(?,?,?,?)", [$added, $_SESSION['id'], $mail_domain, $comment]);
            Logs::write(sprintf(Lang::T("EMAIL_BANS_ADD"), $mail_domain, $_SESSION["username"]));
            show_error_msg(Lang::T("COMPLETE"), Lang::T("EMAIL_BAN_ADDED"), 0);
            require APPROOT . '/views/admin/footer.php';
            die;
        }

        $count = DB::run("SELECT count(id) FROM email_bans")->fetchColumn();
        $perpage = 40;
        list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, URLROOT . "/admin/emailbans?");
        $title = Lang::T("EMAIL_BANS");
        $res = DB::run("SELECT * FROM email_bans ORDER BY added DESC $data[limit]");
        
        $data = [
            'title' => $title,
            'count' => $count,
            'pagertop' => $pagertop,
            'res' => $res,
            'pagerbottom' => $pagerbottom,
            'limit' => $limit,
        ];
        $this->view('bans/admin/email', $data, 'admin');
    }

    public function torrent()
    {
        $res2 = DB::run("SELECT COUNT(*) FROM torrents WHERE banned=?", ['yes']);
        $row = $res2->fetch(PDO::FETCH_LAZY);
        $count = $row[0];
        $perpage = 50;
        list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, "/adminbans/torrent?");

        $resqq = DB::run("SELECT id, name, seeders, leechers, visible, banned, external FROM torrents WHERE banned=? ORDER BY name", ['yes']);
        $title = "Banned " . Lang::T("TORRENT_MANAGEMENT");
        $data = [
            'title' => $title,
            'pagerbottom' => $pagerbottom,
            'count' => $count,
            'pagertop' => $pagertop,
            'resqq' => $resqq,
        ];
        $this->view('bans/admin/torrents', $data, 'admin');
    }
}