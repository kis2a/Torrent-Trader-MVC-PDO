<?php
class Adminmessages extends Controller
{

    public function __construct()
    {
        $this->user = (new Auth)->user(_MODERATOR, 2);
        // $this->userModel = $this->model('User');
        $this->logsModel = $this->model('Logs');
        $this->valid = new Validation();
    }


    public function index()
    {
        $row = DB::run("SELECT COUNT(*) FROM messages WHERE location in ('in', 'both')")->fetch(PDO::FETCH_LAZY);
        $count = $row[0];
        $perpage = 50;
        list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, "/adminmessages?;");
        $res = DB::run("SELECT * FROM messages WHERE location in ('in', 'both') ORDER BY id DESC $limit");
        $data = [
            'title' => Lang::T("Message Spy"),
            'res' => $res,
        ];
        $this->view('message/admin/spypm', $data, 'admin');
    }

    public function delete()
    {
        if ($_POST["delall"]) {
            DB::run("DELETE FROM `messages`");
        } else {
            if (!@count($_POST["del"])) {
                show_error_msg(Lang::T("ERROR"), Lang::T("NOTHING_SELECTED"), 1);
            }
            $ids = array_map("intval", $_POST["del"]);
            $ids = implode(", ", $ids);
            DB::run("DELETE FROM `messages` WHERE `id` IN ($ids)");
        }
        Session::flash('info', Lang::T("CP_DELETED_ENTRIES"), URLROOT . "/adminmessages");
    }

    public function mass()
    {
        if ($_GET["send"] == '1') {
            $sender_id = ($_POST['sender'] == 'system' ? 0 : $_SESSION['id']);
            $dt = TimeDate::get_date_time();
            $msg = $_POST['msg'];
            $subject = $_POST["subject"];
            if (!$msg) {
                show_error_msg(Lang::T("ERROR"), "Please Enter Something!", 1);
            }
            $updateset = array_map("intval", $_POST['clases']);
            $query = DB::run("SELECT id FROM users WHERE class IN (" . implode(",", $updateset) . ") AND enabled = 'yes' AND status = 'confirmed'");
            while ($dat = $query->fetch(PDO::FETCH_ASSOC)) {
                DB::run("INSERT INTO messages (sender, receiver, added, msg, subject) VALUES (?,?,?,?,?)", [$sender_id, $dat['id'], TimeDate::get_date_time(), $msg, $subject]);
            }
            Logs::write("A Mass PM was sent by ($_SESSION[username])");
            Redirect::autolink(URLROOT . "/adminmessages/mass", Lang::T("SUCCESS"), "Mass PM Sent!");
            die;
        }
        $res = DB::run("SELECT group_id, level FROM `groups`");
        $data = [
            'title' => Lang::T("Mass Private Message"),
            'res' => $res,
        ];
        $this->view('message/admin/masspm', $data, 'admin');
    }
}