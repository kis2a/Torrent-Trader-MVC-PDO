<?php

class Auth
{

    public function __construct()
    {
        $this->db = new Database();
    }

    public function user($class = 0, $force = 0, $autoclean = false)
    {
        self::ipBanned();
        
        if ($autoclean) {
            autoclean();
        }

Cookie::csrf_token();

        if (strlen($_COOKIE["password"]) != 60 || !is_numeric($_COOKIE["id"]) || $_COOKIE["key_token"] != self::loginString()) {
            self::isClosed();
            $this->isLoggedIn($force);
            return;

        } else {

            try {
                $res = $this->db->run("SELECT * FROM `users` LEFT OUTER JOIN `groups` ON users.class=groups.group_id WHERE id = $_COOKIE[id] AND users.enabled='yes' AND users.status ='confirmed'");
            } catch (Exception $e) {
                Cookie::destroyAll();
                Redirect::autolink(URLROOT . "/logout", 'Issue With User Auth');
            }
            $row = $res->fetch(PDO::FETCH_ASSOC);

            if ($row['password'] != $_COOKIE['password']) {
                Redirect::to(URLROOT . "/logout");
            }
            if ($row['id'] != $_COOKIE['id']) {
                Redirect::to(URLROOT . "/logout");
            }
            if($class != 0 && $class > $row['class']) {
                Redirect::autolink(URLROOT . "/index", Lang::T("SORRY_NO_RIGHTS_TO_ACCESS"));
            }
            if ($row) {
                $user = $row;
                $message = null;
                if (Session::get('message')) {
                    $message = $_SESSION['message'];
                }
                
                $where = Helper::where($_SERVER['REQUEST_URI'], $row["id"], 0);
                $this->db->run("UPDATE users SET last_access=?,ip=?,page=? WHERE id=?", [Helper::get_date_time(), Helper::getIP(), $where, $row["id"]]);
                $_SESSION = $row;
                $_SESSION["loggedin"] = true;
                $_SESSION['message'] = $message;
                unset($row);
                self::isClosed();
                return $user;
            }
        }

    }

    private static function loginString()
    {
        $ip = Helper::getIP();
        $browser = Helper::browser();
        return md5($browser.$browser);
    }

    public static function ipBanned()
    {
        $ip = Helper::getIP();
        if ($ip == '') {
            return;
        }
        Ip::checkipban($ip);
    }

    public function isLoggedIn($force = 0)
    {
        // If force 0 guest view, force 1 use config membersonly, force 2 always hidden from guest
        if ($force == 1 && MEMBERSONLY) {
            if (!$_SESSION['loggedin']) {
                Redirect::to(URLROOT . "/logout");
            }
        } elseif ($force == 2) {
            if (!$_SESSION['loggedin']) {
                Redirect::to(URLROOT . "/login");
            }
        }
    }

    public static function isStaff()
    {
        if (!$_SESSION['class'] > 5 || $_SESSION["control_panel"] != "yes") {
            Session::flash('info', Lang::T("SORRY_NO_RIGHTS_TO_ACCESS"), URLROOT);
        }
    }

    public static function isClosed($wrapper = 1)
    {
        if (!SITE_ONLINE) {
            if ($_SESSION["control_panel"] != "yes") {
                if ($wrapper) {
                    ob_start();
                    ob_clean();
                }
                require_once "../app/views/inc/darktheme/header.php";
                echo '<div class="alert alert-warning"><center>' . stripslashes(OFFLINEMSG) . '</center></div>';
                require_once "../app/views/inc/default/footer.php";
                if ($wrapper) {
                    die();
                }
            }
        }
    }
}