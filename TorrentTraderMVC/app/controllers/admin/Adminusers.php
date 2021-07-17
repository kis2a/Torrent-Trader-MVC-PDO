<?php
class Adminusers extends Controller
{

    public function __construct()
    {
        $this->session = Auth::user(_MODERATOR, 2);
    }

    public function index()
    {
        Redirect::to(URLROOT . '/admincp');
    }

    public function add()
    {
        $data = [
            'title' => 'Add User',
        ];
        View::render('user/admin/adduser', $data, 'admin');
    }

    public function addeduserok()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if ($_POST["username"] == "" || $_POST["password"] == "" || $_POST["email"] == "") {
                Redirect::autolink(URLROOT . "/adminusers", "Missing form data.");
            }
            if ($_POST["password"] != $_POST["password2"]) {
                Redirect::autolink(URLROOT . "/adminusers", "Passwords mismatch.");
            }
            $username = $_POST["username"];
            $password = $_POST["password"];
            $email = $_POST["email"];
            $secret = Helper::mksecret();
            $passhash = md5($password);
            $secret = $secret;
            /*
            $count = get_row_count("users", "WHERE username=$username");
            if (!$count !=0) {
            Redirect::autolink(URLROOT . "/adminusers/add", "Unable to create the account. The user name is possibly already taken.");
            die;
            }
             */
            DB::run("INSERT INTO users (added, last_access, secret, username, password, status, email) VALUES (?,?,?,?,?,?,?)", [TimeDate::get_date_time(), TimeDate::get_date_time(), $secret, $username, $passhash, 'confirmed', $email]);
            Redirect::autolink(URLROOT . "/admincp", Lang::T("COMPLETE"));
        }
    }

    public function whoswhere()
    {
        $res = DB::run("SELECT `id`, `username`, `page`, `last_access`
                        FROM `users`
                        WHERE `enabled` = 'yes' AND `status` = 'confirmed' AND `page` != ''
                        ORDER BY `last_access`
                        DESC LIMIT 100");
        $data = [
            'title' => 'Where are members',
            'res' => $res,
        ];
        View::render('user/admin/whoswhere', $data, 'admin');
    }

    public function privacy()
    {
        $where = array();
        switch ($_GET['type']) {
            case 'low':
                $where[] = "privacy = 'low'";
                break;
            case 'normal':
                $where[] = "privacy = 'normal'";
                break;
            case 'strong':
                $where[] = "privacy = 'strong'";
                break;
            default:
                break;
        }
        $where[] = "enabled = 'yes'";
        $where[] = "status = 'confirmed'";
        $where = implode(' AND ', $where);
        $count = get_row_count("users", "WHERE $where");
        list($pagertop, $pagerbottom, $limit) = pager(25, $count, htmlspecialchars($_SERVER['REQUEST_URI'] . '&'));
        $res = DB::run("SELECT id, username, class, email, ip, added, last_access FROM users WHERE $where ORDER BY username DESC $limit");

        $data = [
            'title' => Lang::T("PRIVACY_LEVEL"),
            'count' => $count,
            'res' => $res,
            'pagerbottom' => $pagerbottom,
        ];
        View::render('user/admin/privacylevel', $data, 'admin');
    }

    public function duplicateip()
    {
        $res = DB::run("SELECT ip FROM users GROUP BY ip HAVING count(*) > 1");
        $num = $res->rowCount();
        list($pagertop, $pagerbottom, $limit) = pager(25, $num, 'Adminusers/duplicateip?');
        $res = DB::run("SELECT id, username, class, email, ip, added, last_access, COUNT(*) as count FROM users GROUP BY ip HAVING count(*) > 1 ORDER BY id ASC $limit");
        $data = [
            'title' => Lang::T("DUPLICATEIP"),
            'num' => $num,
            'res' => $res,
        ];
        View::render('user/admin/duplicuteip', $data, 'admin');
    }

    public function confirm()
    {
        $do = $_GET['do']; // todo
        if ($do == "confirm") {
            if ($_POST["confirmall"]) {
                DB::run("UPDATE `users` SET `status` = 'confirmed' WHERE `status` = 'pending' AND `invited_by` = '0'");
            } else {
                if (!@count($_POST["users"])) {
                    Redirect::autolink(URLROOT."/adminusers/duplicateip", Lang::T("NOTHING_SELECTED"));
                }
                $ids = array_map("intval", $_POST["users"]);
                $ids = implode(", ", $ids);
                DB::run("UPDATE `users` SET `status` = 'confirmed' WHERE `status` = 'pending' AND `invited_by` = '0' AND `id` IN ($ids)");
            }
            Redirect::autolink(URLROOT . "/Adminusers/confirm", "Entries Confirmed");
        }
        $count = get_row_count("users", "WHERE status = 'pending' AND invited_by = '0'");
        list($pagertop, $pagerbottom, $limit) = pager(25, $count, '' . URLROOT . '/Adminusers/confirm?');
        $res = DB::run("SELECT `id`, `username`, `email`, `added`, `ip` FROM `users` WHERE `status` = 'pending' AND `invited_by` = '0' ORDER BY `added` DESC $limit");

        $data = [
            'title' => Lang::T("Manual Registration Confirm"),
            'count' => $count,
            'pagerbottom' => $pagerbottom,
            'res' => $res,
        ];
        View::render('user/admin/confirmreg', $data, 'admin');
    }

    public function cheats()
    {
        $megabts = (int) $_POST['megabts'];
        $daysago = (int) $_POST['daysago'];
        if ($daysago && $megabts) {
            $timeago = 84600 * $daysago; //last 7 days
            $bytesover = 1048576 * $megabts; //over 500MB Upped
            $result = DB::run("select * FROM users WHERE UNIX_TIMESTAMP('" . TimeDate::get_date_time() . "') - UNIX_TIMESTAMP(added) < '$timeago' AND status='confirmed' AND uploaded > '$bytesover' ORDER BY uploaded DESC ");
            $num = $result->rowCount(); // how many uploaders
            $message = "<p>" . $num . " Users with found over last " . $daysago . " days with more than " . $megabts . " MB (" . $bytesover . ") Bytes Uploaded.</p>";
            $zerofix = $num - 1; // remove one row because mysql starts at zero
            if ($num > 0) {
                $data = [
                    'title' => Lang::T("Possible Cheater Detection"),
                    'result' => $result,
                    'zerofix' => $zerofix,
                    'message' => $message
                    ];
                    View::render('user/admin/cheatresult', $data, 'admin');
            } else {
                    Redirect::autolink(URLROOT . '/adminusers/cheats', $message);
                die;
            } 

        } else {
            $data = [
            'title' => Lang::T("Possible Cheater Detection"),
            ];
            View::render('user/admin/cheatform', $data, 'admin');
        }
    }

}