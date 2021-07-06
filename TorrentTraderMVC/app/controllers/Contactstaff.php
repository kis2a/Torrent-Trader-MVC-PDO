<?php
class Contactstaff extends Controller
{
    public function __construct()
    {
        $this->session = (new Auth)->user(0, 2);
        // $this->userModel = $this->model('User');
        $this->valid = new Validation();
    }

    public function index()
    {
        $data = [
            'title' => 'Contact Staff',
        ];
        $this->view('contactstaff/index', $data, 'user');
    }

    public function submit()
    {
        if (Input::get("msg") && Input::get("sub")) {
            $msg = Input::get("msg");
            $sub = Input::get("sub");
            $error_msg = "";
            if (!$msg) {
                $error_msg = $error_msg . "You did not put message.</br>";
            }
            if (!$sub) {
                $error_msg = $error_msg . "You did not put subject.</br>";
            }
            if ($error_msg != "") {
                Redirect::autolink(URLROOT, "Your message can not be sent:$error_msg</br>");
            } else {
                $added = TimeDate::get_date_time();
                $userid = $_SESSION['id'];
                $req = DB::run("INSERT INTO staffmessages (sender, added, msg, subject) VALUES(?,?,?,?)", [$userid, $added, $msg, $sub]);
                if ($req) {
                    Redirect::autolink(URLROOT, 'Your message has been sent. We will reply as soon as possible.');
                } else {
                    Redirect::autolink(URLROOT, 'We are busy. try again later');
                }
            }
        }
    }
}
