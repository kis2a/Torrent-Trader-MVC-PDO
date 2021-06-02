<?php

class Controller
{
    public function __construct()
    {
        //$this->loggedIn();
        //$this->ipBanned();
    }

    public function __clone()
    {
        
    }
    
    public function model($model)
    {
        require_once '../app/models/' . $model . '.php';
        return new $model();
    }

    public function view($file, $data = [], $page = false)
    {
        if (file_exists('../app/views/' . $file . '.php')) {
            if ($page == 'admin') {
                Style::adminheader('Staff Panel');
                Style::adminnavmenu();
                Style::begin($data['title']);
                require_once "../app/views/" . $file . ".php";
                Style::end();
                Style::adminfooter();
            } elseif ($page == 'user') {
                Style::header($data['title']);
                Style::begin($data['title']);
                require_once "../app/views/" . $file . ".php";
                Style::end();
                Style::footer();
            } else {
                require_once "../app/views/" . $file . ".php";
            }
        } else {
            die('View does not exist');
        }
    }
    /*
public function loggedIn()
{
if ((Session::get("id") || Session::get("password")) == null) {
return false;
}
if (LOGINFINGERPRINT == true) {
$loginString = $this->loginString();
$stringNow = Session::get("login_fingerprint");
if ($stringNow != null && $stringNow == $loginString) {
return true;
} else {
$this->logout();
return false;
}
}
//if you got to this point, user is logged in
return true;
}

private function loginString()
{
$ip = Helper::getIP();
$browser = Helper::browser();
return hash("sha512", $ip, $browser);
}

private function logout()
{
Session::destroySession();
Redirect::to("login");
}

public function ipBanned()
{
$ip = Helper::getIP();
if ($ip == '') {
return;
}
Ip::checkipban($ip);
}
 */
}
