<?php
class Recover extends Controller
{

    public function __construct()
    {
        $this->session = Auth::user(0, 0);
    }

    public function index()
    {
        $data = [
            'title' => 'Recover Account',
        ];
        View::render('user/recover', $data, 'user');
    }

    public function submit()
    {
        // check if using google captcha
        (new Captcha)->response($_POST['g-recaptcha-response']);
        if (Input::exist()) {
            $email = Input::get("email");
            if (!Validate::Email($email)) {
                Redirect::autolink(URLROOT . "/home", Lang::T("EMAIL_ADDRESS_NOT_VAILD"));
            } else {
                $arr = Users::getIdEmailByEmail($email);
                if (!$arr) {
                    Redirect::autolink(URLROOT . "/home", Lang::T("EMAIL_ADDRESS_NOT_FOUND"));
                }
                if ($arr) {
                    $sec = Helper::mksecret();
                    $id = $arr['id'];
                    $username = $arr['username']; // 06/01
                    $emailmain = SITEEMAIL;
                    $url = URLROOT;
                    $body = Lang::T("SOMEONE_FROM") . " " . $_SERVER["REMOTE_ADDR"] . " " . Lang::T("MAILED_BACK") . " ($email) " . Lang::T("BE_MAILED_BACK") . " \r\n\r\n " . Lang::T("ACCOUNT_INFO") . " \r\n\r\n " . Lang::T("USERNAME") . ": " . $username . " \r\n " . Lang::T("CHANGE_PSW") . "\n\n$url/recover/confirm?id=$id&secret=$sec\n\n\n" . $url . "\r\n";
                    $TTMail = new TTMail();
                    $TTMail->Send($email, Lang::T("ACCOUNT_DETAILS"), $body, "", "-f$emailmain");
                    Users::setSecret($sec, $email);
                    Redirect::autolink(URLROOT . "/home", sprintf(Lang::T('MAIL_RECOVER'), htmlspecialchars($email)));
                }
            }
        }
    }

    public function confirm()
    {
        $data = [
            'title' => 'Recover Account'];
        View::render('user/confirm', $data, 'user');
    }

    public function ok()
    {
        $id = Input::get("id");
        $secret = Input::get("secret");
        if (Validate::Id(Input::get("id")) && strlen(Input::get("secret")) == 20) {
            $password = Input::get("password");
            $password1 = Input::get("password1");
            if (empty($password) || empty($password1)) {
                Redirect::autolink(URLROOT . "/home", Lang::T("NO_EMPTY_FIELDS"));
            } elseif ($password != $password1) {
                Redirect::autolink(URLROOT . "/home", Lang::T("PASSWORD_NO_MATCH"));
            } else {
                $count = DB::run("SELECT COUNT(*) FROM users WHERE id=? AND secret=?", [$id, $secret])->fetchColumn();
                if ($count != 1) {
                    Redirect::autolink(URLROOT . "/home", Lang::T("NO_SUCH_USER"));
                }
                $newsec = Helper::mksecret();
                $wantpassword = password_hash($password, PASSWORD_BCRYPT);
                $stmt = Users::recoverUpdate($wantpassword, $newsec, $id, $secret);
                Redirect::autolink(URLROOT . "/home", Lang::T("PASSWORD_CHANGED_OK"));
            }
        } else {
            Redirect::autolink(URLROOT . "/home", Lang::T("Wrong Imput"));
        }
    }
    
}