<?php
class Login
{

    public function __construct()
    {
        $this->session = Auth::user(0, 0);
    }

    public function index()
    {
        Cookie::destroyAll();
        $token = Cookie::csrf_token();
        $data = [
            'token' => $token,
            'title' => Lang::T("LOGIN"),
        ];
        View::render('login/index', $data, 'user');
    }

    public function submit()
    {
        // check if using google captcha
        (new Captcha)->response(Input::get('g-recaptcha-response'));
        if (Input::exist() && Cookie::csrf_check()) {
            $username = Input::get("username");
            $password = Input::get("password");
            // User By Name
            $sql = Users::getUserByUsername($username);
            // Do Some Checks
            if (!$sql || !password_verify($password, $sql['password'])) {
                Redirect::autolink(URLROOT . "/logout", Lang::T("LOGIN_INCORRECT"));
            } elseif ($sql['status'] == "pending") {
                Redirect::autolink(URLROOT . "/logout", Lang::T("ACCOUNT_PENDING"));
            } elseif ($sql['enabled'] == "no") {
                Redirect::autolink(URLROOT . "/logout", Lang::T("ACCOUNT_DISABLED"));
            }
            // Set Cookies and token
            Cookie::setAll($sql['id'], $sql['password'], $this->loginString());
            Users::updatelogin($this->loginString(), $sql['id']);
            Redirect::to(URLROOT);
        } else {
            Redirect::to(URLROOT . "/logout");
        }
    }

    private function loginString()
    {
        $ip = Ip::getIP();
        $browser = Ip::agent();
        return md5($browser . $browser);
    }

}