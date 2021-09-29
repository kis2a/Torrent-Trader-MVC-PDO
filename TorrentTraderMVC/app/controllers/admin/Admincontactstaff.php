<?php
class Admincontactstaff
{
    public function __construct()
    {
        $this->session = Auth::user(_MODERATOR, 2);
    }

    public function index()
    {
        $res = Staffmessage::getAll();
        $data = [
            'title' => 'Staff PMs',
            'res' => $res,
        ];
        View::render('contactstaff/admin/staff', $data, 'admin');
    }

    public function viewpm()
    {
        $pmid = (int) $_GET["pmid"];
        $ress4 = DB::run("SELECT id, subject, sender, added, msg, answeredby, answered FROM staffmessages WHERE id=$pmid");
        $arr4 = $ress4->fetch(PDO::FETCH_ASSOC);
        $answeredby = $arr4["answeredby"];
        $rast = DB::run("SELECT username FROM users WHERE id=$answeredby");
        $arr5 = $rast->fetch(PDO::FETCH_ASSOC);
        $senderr = "" . $arr4["sender"] . "";
        if (Validate::Id($arr4["sender"])) {
            $res2 = DB::run("SELECT username FROM users WHERE id=" . $arr4["sender"]);
            $arr2 = $res2->fetch(PDO::FETCH_ASSOC);
            $sender = "<a href='" . URLROOT . "/profile/read?id=$senderr'>" . ($arr2["username"] ? $arr2["username"] : "[Deleted]") . "</a>";
        } else {
            $sender = "System";
        }
        $subject = $arr4["subject"];
        if ($arr4["answered"] == '0') {
            $answered = "<font color=red><b>No</b></font>";
        } else {
            $answered = "<font color=blue><b>Yes</b></font> by <a href='" . URLROOT . "/profile/read?id=$answeredby>" . Users::coloredname($arr5['username']) . "</a> (<a href=" . URLROOT . "/admincontactstaff/viewanswer?pmid=$pmid>Show Answer</a>)";
        }
        if ($arr4["answered"] == '0') {
            $setanswered = "[<a href=" . URLROOT . "/admincontactstaff/setanswered?id=$arr4[id]>Mark Answered</a>]";
        } else {
            $setanswered = "";
        }
        $iidee = $arr4["id"];
        $elapsed = TimeDate::get_elapsed_time(TimeDate::sql_timestamp_to_unix_timestamp($arr4["added"]));
        $data = [
            'title' => 'Staff PMs',
            'elapsed' => $elapsed,
            'sender' => $sender,
            'added' => $arr4["added"],
            'subject' => $subject,
            'answeredby' => $answeredby,
            'answered' => $answered,
            'setanswered' => $setanswered,
            'msg' => $arr4["msg"],
            'sender1' => $arr4["sender"],
            'iidee' => $iidee,
            'id' => $arr4["id"],
        ];
        View::render('contactstaff/admin/viewpm', $data, 'admin');
    }

    public function reply()
    {
        $answeringto = $_GET["answeringto"];
        $receiver = (int) $_GET["receiver"];
        if (!Validate::Id($receiver)) {
            Redirect::autolink(URLROOT . '/admincontactstaff', "Invalid id.");
        }
        $res = DB::run("SELECT * FROM users WHERE id=$receiver");
        $res2 = DB::run("SELECT * FROM staffmessages WHERE id=$answeringto");
        $data = [
            'title' => 'Staff PMs',
            'res' => $res,
            'res2' => $res2,
            'answeringto' => $answeringto,
            'receiver' => $receiver,
        ];
        View::render('contactstaff/admin/reply', $data, 'admin');
    }

    public function takeanswer()
    {
        $receiver = (int) $_POST["receiver"];
        $answeringto = $_POST["answeringto"];
        if (!Validate::Id($receiver)) {
            Redirect::autolink(URLROOT . '/admincontactstaff', "Invalid ID");
        }
        $userid = $_SESSION["id"];
        $msg = trim($_POST["msg"]);
        $message = $msg;
        $added = TimeDate::get_date_time();
        if (!$msg) {
            Redirect::autolink(URLROOT . '/admincontactstaff', "Please enter something!");
        }
        DB::run("UPDATE staffmessages SET answer=? WHERE id=?", [$message, $answeringto]);
        DB::run("UPDATE staffmessages SET answered=?, answeredby=? WHERE id=?", [1, $userid, $answeringto]);
        $smsg = "Staff Message $answeringto has been answered.";
        Redirect::autolink(URLROOT . '/admincontactstaff', $smsg);
        die;
    }

    public function setanswered()
    {
        $id = (int) $_GET["id"];
        DB::run("UPDATE staffmessages SET answered=1, answeredby = $_SESSION[id] WHERE id = $id");
        $smsg = "Staff Message $id has been set as answered.";
        Redirect::autolink(URLROOT . "/admincontactstaff/viewpm?pmid=$id", $smsg);
        die;
    }

    public function viewanswer()
    {
        $pmid = (int) $_GET["pmid"];
        $ress4 = DB::run("SELECT id, subject, sender, added, msg, answeredby, answered, answer FROM staffmessages WHERE id=$pmid");
        $arr4 = $ress4->fetch(PDO::FETCH_ASSOC);
        $answeredby = $arr4["answeredby"];
        $rast = DB::run("SELECT username FROM users WHERE id=$answeredby");
        $arr5 = $rast->fetch(PDO::FETCH_ASSOC);
        if (Validate::Id($arr4["sender"])) {
            $res2 = DB::run("SELECT username FROM users WHERE id=" . $arr4["sender"]);
            $arr2 = $res2->fetch(PDO::FETCH_ASSOC);
            $sender = "<a href=" . URLROOT . "/profile?id=" . $arr4["sender"] . ">" . ($arr2["username"] ? $arr2["username"] : "[Deleted]") . "</a>";
        } else {
            $sender = "System";
        }
        if ($arr4['subject'] == "") {
            $subject = "No subject";
        } else {
            $subject = "<a style='color: darkred' href=".URLROOT."/admincontactstaff/viewpm&pmid=$pmid>$arr4[subject]</a>";
        }
        $iidee = $arr4["id"];
        if ($arr4['answer'] == "") {
            $answer = "This message has not been answered yet!";
        } else {
            $answer = $arr4["answer"];
        }
        $data = [
            'title' => 'Staff PMs',
            'answer' => $answer,
            'added' =>  $arr4["added"],
            'subject' => $subject,
            'iidee' => $iidee,
            'sender' => $sender,
            'answeredby' => $answeredby,
        ];
        View::render('contactstaff/admin/viewanswer', $data, 'admin');
    }

    public function deletestaffmessage()
    {
        $id = (int) $_GET["id"];
        if (!is_numeric($id) || $id < 1 || floor($id) != $id) {
            die;
        }
        DB::run("DELETE FROM staffmessages WHERE id=?", [$id]);
        $smsg = "Staff Message $id has been deleted.";
        Redirect::autolink(URLROOT . "/admincontactstaff", $smsg);
        die;
    }

    public function takecontactanswered()
    {
        $res = DB::run("SELECT id FROM staffmessages WHERE answered=0 AND id IN (" . implode(", ", $_POST['setanswered']) . ")");
        while ($arr = $res->fetch(PDO::FETCH_ASSOC)) {
            DB::run("UPDATE staffmessages SET answered=?, answeredby =?  WHERE id =?", [1, $_SESSION['id'], $arr['id']]);
        }
        $smsg = "Staff Messages have been marked as answered.";
        Redirect::autolink(URLROOT."/admincontactstaff", $smsg);
        die;
    }

}