<?php
class Login extends Controller
{

    public function __construct()
    {
        $this->session = Auth::user(0, 0);
		$this->userModel = $this->model('User');
    }

    public function index()
    {
        $token = Cookie::csrf_token();
        $data = [
            'token' =>$token,
            'title' => Lang::T("LOGIN")
        ];
        View::render('user/login', $data, 'user');
    }

    public function submit()
    {
        // check if using google captcha
        (new Captcha)->response($_POST['g-recaptcha-response']);
        if (Input::exist() && Cookie::csrf_check()) {
            $username = Input::get("username");
            $password = Input::get("password");
            $sql = $this->userModel->getUserByUsername($username);
            if (!$sql || !password_verify($password, $sql->password)) {
                Redirect::autolink(URLROOT . "/logout", Lang::T("LOGIN_INCORRECT"));
            } elseif ($sql->status == "pending") {
                Redirect::autolink(URLROOT . "/logout", Lang::T("ACCOUNT_PENDING"));
            } elseif ($sql->enabled == "no") {
                Redirect::autolink(URLROOT . "/logout", Lang::T("ACCOUNT_DISABLED"));
            }

            Cookie::set('id', $sql->id, 5485858, "/");
            Cookie::set('password', $sql->password, 5485858, "/");
            Cookie::set("key_token", $this->loginString(), 5485858, "/");
            $this->userModel->updatelogin($this->loginString(), $sql->id);
            Redirect::to(URLROOT."/home");
        } else {
            Redirect::to(URLROOT."/logout");
        }
    }

    private function loginString()
    {
        $ip = Ip::getIP();
        $browser = Ip::agent();
        return md5($browser.$browser);
    }
}