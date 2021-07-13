<?php
class Confirmemail extends Controller
{

    public function __construct()
    {
        $this->session = Auth::user(0, 0);
    }

    // Confirm by email (siteconfig - first contact)
    public function signup()
    {
        $id = (int) Input::get("id");
        $md5 = Input::get("secret");
        if (!$id || !$md5) {
            Redirect::autolink(URLROOT, Lang::T("INVALID_ID"));
        }
        $row = Users::getPasswordSecretStatus($id);
        if (!$row) {
            $mgs = sprintf(Lang::T("CONFIRM_EXPIRE"), SIGNUPTIMEOUT / 86400);
            Redirect::autolink(URLROOT, $mgs);
        }
        if ($row['status'] != "pending") {
            Redirect::autolink(URLROOT, Lang::T("ACCOUNT_ACTIVATED"));
            die;
        }
        if ($md5 != $row['secret']) {
            Redirect::autolink(URLROOT, Lang::T("SIGNUP_ACTIVATE_LINK"));
        }
        $secret = Helper::mksecret();
        $upd = Users::updatesecret($secret, $id, $row['secret']);
        if ($upd == 0) {
            Redirect::autolink(URLROOT, Lang::T("SIGNUP_UNABLE"));
        }
        Redirect::autolink(URLROOT . '/login', Lang::T("ACCOUNT_ACTIVATED"));
    }

    // user confirm email reset (reset own email)
    public function index()
    {
        $id = (int) Input::get("id");
        $md5 = Input::get("secret");
        $email = Input::get("email");
        if (!$id || !$md5 || !$email) {
            Redirect::autolink(URLROOT . "/home",  Lang::T("MISSING_FORM_DATA"));
        }
        $row = Users::getEditsecret($id);
        if (!$row) {
            Redirect::autolink(URLROOT . "/home",  Lang::T("NOTHING_FOUND"));
        }
        $sec = $row['editsecret'];
        if ($md5 != $sec) {
            Redirect::autolink(URLROOT . "/home",  Lang::T("NOTHING_FOUND"));
        }
        Users::updateUserEmailResetEditsecret($email, $id, $row['editsecret']);
        Redirect::autolink(URLROOT . "/home", Lang::T("SUCCESS"));
    }

}