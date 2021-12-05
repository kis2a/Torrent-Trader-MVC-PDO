<?php
class Adminmassemail
{

    public function __construct()
    {
        $this->session = Auth::user(_ADMINISTRATOR, 2);
    }

    public function index()
    {
        $res = DB::run("SELECT `group_id`, `level` FROM `groups` ORDER BY `group_id` ASC");
        $data = [
            'title' => Lang::T("Mass Email"),
            'res' => $res,
        ];
        View::render('message/massemail', $data, 'admin');
    }

    public function send()
    {
        $msg_log = "Sent to classes: ";
        @set_time_limit(0);
        $subject = $_POST["subject"];
        $body = format_comment($_POST["body"]);
        if (!$subject || !$body) {
            Redirect::autolink(URLROOT . "/adminmassemail", "No subject or body specified.");
        }
        if (!@count($_POST["groups"])) {
            Redirect::autolink(URLROOT . "/adminmassemail", "No groups Selected.");
        }
        $ids = array_map("intval", $_POST["groups"]);
        $ids = implode(", ", $ids);
        $res_log = DB::run("SELECT DISTINCT level FROM groups WHERE group_id IN ($ids)");
        while ($row_log = $res_log->fetch(PDO::FETCH_ASSOC)) {
            $msg_log .= $row_log["level"] . ", ";
        }
        $res = DB::run("SELECT u.email FROM users u LEFT JOIN groups g ON u.class = g.group_id WHERE u.enabled = 'yes' AND u.status = 'confirmed' AND u.class IN ($ids)");
        $siteemail = Config::TT()['SITEEMAIL'];
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $TTMail = new TTMail();
            $TTMail->Send($row["email"], $subject, $body, "Content-type: text/html; charset=utf-8", "-f$siteemail");
        }
        Logs::write("<b><font color='Magenta'>A Mass E-Mail</font> was sent by (<font color='Navy'>$_SESSION[username]</font>) $msg_log<b>");
        Redirect::autolink(URLROOT . "/adminmassemail", "<b><font color='#ff0000'>Mass mail sent....</font></b>");
    }

}