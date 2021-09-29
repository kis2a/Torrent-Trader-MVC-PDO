<?php
class Adminmessages
{

    public function __construct()
    {
        $this->session = Auth::user(_MODERATOR, 2);
    }


    public function index()
    {
        $count = DB::run("SELECT COUNT(*) FROM messages WHERE location in ('in', 'both')")->fetchColumn();
        list($pagertop, $pagerbottom, $limit) = pager(40, $count, "/adminmessages?;");
        $res = DB::run("SELECT * FROM messages WHERE location in ('in', 'both') ORDER BY id DESC $limit");
        $data = [
            'title' => Lang::T("Message Spy"),
            'res' => $res,
        ];
        View::render('message/admin/spypm', $data, 'admin');
    }

    public function delete()
    {
        if ($_POST["delall"]) {
            DB::run("DELETE FROM `messages`");
        } else {
            if (!@count($_POST["del"])) {
                Redirect::autolink(URLROOT . '/adminmessages', Lang::T("NOTHING_SELECTED"));
            }
            $ids = array_map("intval", $_POST["del"]);
            $ids = implode(", ", $ids);
            DB::run("DELETE FROM `messages` WHERE `id` IN ($ids)");
        }
        Redirect::autolink(URLROOT . '/adminmessages', Lang::T("CP_DELETED_ENTRIES"));
    }

    public function masspm()
    {
        $res = DB::run("SELECT group_id, level FROM `groups`");
        $data = [
            'title' => Lang::T("Mass Private Message"),
            'res' => $res,
        ];
        View::render('message/admin/masspm', $data, 'admin');
    }
    
    public function send()
    {
        $sender_id = ($_POST['sender'] == 'system' ? 0 : $_SESSION['id']);
        $msg = $_POST['msg'];
        $subject = $_POST["subject"];
        if (!$msg) {
            Redirect::autolink(URLROOT . '/adminmessages/masspm', "Please Enter Something!");
        }
        $updateset = array_map("intval", $_POST['clases']);
        $query = DB::run("SELECT id FROM users WHERE class IN (" . implode(",", $updateset) . ") AND enabled = 'yes' AND status = 'confirmed'");
        while ($dat = $query->fetch(PDO::FETCH_ASSOC)) {
            DB::run("INSERT INTO messages (sender, receiver, added, msg, subject) VALUES (?,?,?,?,?)", [$sender_id, $dat['id'], TimeDate::get_date_time(), $msg, $subject]);
        }
        Logs::write("A Mass PM was sent by ($_SESSION[username])");
        Redirect::autolink(URLROOT . "/adminmessages/masspm", Lang::T("SUCCESS"), "Mass PM Sent!");
    }
}