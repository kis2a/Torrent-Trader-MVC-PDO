<?php
class Contactstaff extends Controller
{
    public function __construct()
    {
        Auth::user();
        // $this->userModel = $this->model('User');
        $this->valid = new Validation();
    }

    public function index()
    {
        if ((isset($_POST["msg"])) & (isset($_POST["sub"]))) {
            $msg = trim($_POST["msg"]);
            $sub = trim($_POST["sub"]);
            $error_msg = "";
            if (!$msg) {
                $error_msg = $error_msg . "You did not put message.</br>";
            }
            if (!$sub) {
                $error_msg = $error_msg . "You did not put subject.</br>";
            }
            if ($error_msg != "") {
                Session::flash('info', "Your message can not be sent:$error_msg</br>", 1);
            } else {
                $added = TimeDate::get_date_time();
                $userid = $_SESSION['id'];
                $req = DB::run("INSERT INTO staffmessages (sender, added, msg, subject) VALUES(?,?,?,?)", [$userid, $added, $msg, $sub]);
                if ($req) {
                    Session::flash('info', 'Your message has been sent. We will reply as soon as possible.', 1);
                } else {
                    Session::flash('info', 'We are busy. try again later', 1);
                }
            }
        } else {
            $data = [
            ];
            $this->view('contact/index', $data, 'user');
        }
    }

}