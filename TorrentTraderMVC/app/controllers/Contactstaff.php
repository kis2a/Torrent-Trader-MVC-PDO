<?php
class Contactstaff extends Controller
{
    public function __construct()
    {
        $this->session = Auth::user(0, 2);
    }

    public function index()
    {
        $data = [
            'title' => 'Contact Staff',
        ];
        View::render('contactstaff/index', $data, 'user');
    }

    public function submit()
    {
        if (Input::get("msg") && Input::get("sub")) {
            $msg = Input::get("msg");
            $sub = Input::get("sub");
            $error_msg = "";
            if (!$msg || !$sub) {
                $error_msg = Lang::T("NO_EMPTY_FIELDS");
            }
            if ($error_msg != "") {
                Redirect::autolink(URLROOT, $error_msg);
            } else {
                $added = TimeDate::get_date_time();
                $userid = $_SESSION['id'];
                $req = Staffmessage::insertStaffMessage($userid, $added, $msg, $sub);
                if ($req == 1) {
                    Redirect::autolink(URLROOT, Lang::T("CONTACT_SENT"));
                } else {
                    Redirect::autolink(URLROOT, Lang::T("TRYLATER"));
                }
            }
        }
    }

}