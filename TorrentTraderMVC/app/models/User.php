<?php
class User
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    // Get User by Username
    public function getUserByUsername($username)
    {
        $row = $this->db->run("SELECT id, password, secret, status, enabled FROM users WHERE username =? ", [$username])->fetch();
        return $row;
    }

    public function updateset($updateset = [], $id)
    {
    DB::run("UPDATE `users` SET " . implode(', ', $updateset) . " WHERE `id` =?", [$id]);
}

    public function warnUserWithId($id)
    {
        $this->db->run("UPDATE users SET warned=? WHERE id=?", ['yes', $id]);
    }

    public function updateUserPasswordSecret($chpassword, $secret, $id)
    {
        $row = $this->db->run("UPDATE users SET password =?, secret =?
            WHERE id =?", [$chpassword, $secret, $id]);

    }

    public function updateUserBits($wantusername, $wantpassword, $secret, $status, $added, $id)
    {
        $row = $this->db->run("
        UPDATE users
        SET username=?, password=?, secret=?, status=?, added=?
        WHERE id=?",
        [$wantusername, $wantpassword, $secret, $status, $added, $id]);
    }

    public function updateUserEditSecret($sec, $id)
    {
        $row = $this->db->run("UPDATE users SET editsecret =? WHERE id =?", [$sec, $id]);

    }
    public function updateUserAvatar($avatar, $id)
    {
        $row = $this->db->run("UPDATE users SET avatar=? WHERE id =?", [$avatar, $id]);

    }
    public function selectUserEmail($id)
    {
        $row = $this->db->run("SELECT email FROM users WHERE id=?", [$id])->fetch(PDO::FETCH_ASSOC);
        return $row;
    }

    public function selectInviteIdBySecret($invite, $secret)
    {
        $row = $this->db->run("SELECT id FROM users WHERE id = ? AND secret = ?", [$invite, $secret])->fetch(PDO::FETCH_ASSOC);
        return $row;
    }

    // Update User pass & secret
    public function recoverUpdate($wantpassword, $newsec, $pid, $psecret)
    {
        $row = $this->db->run("UPDATE `users` SET `password` =?, `secret` =? WHERE `id`=? AND `secret` =?", [$wantpassword, $newsec, $pid, $psecret]);
    }

    // Set User secret
    public function setSecret($sec, $email)
    {
        $row = $this->db->run("UPDATE `users` SET `secret` =? WHERE `email`=? LIMIT 1", [$sec, $email]);
    }

    // Get Email&Id by Email
    public function getIdEmailByEmail($email)
    {
        $row = $this->db->run("SELECT id, username, email FROM users WHERE email=? LIMIT 1", [$email])->fetch();
        return $row;
    }

    public function updatelogin($token, $id)
    {
        $this->db->run("UPDATE users SET last_login=?, token=? WHERE id=?", [TimeDate::get_date_time(), $token, $id]);
            
       //$this->db->run("UPDATE users SET last_login = ? WHERE id = ? ", [Helper::get_date_time(), $id]);
    }

    // Get Email&Id by Email
    public static function getUserById($id)
    {
        $user = DB::run("SELECT * FROM users WHERE id=?", [$id])->fetch();
        return $user;
    }
    // Get All User Array
    public function getAll($id)
    {
        $row = DB::run("SELECT * FROM users WHERE id =? ", [$id]);
        $user1 = $row->fetchAll();
        return $user1;
    }

    // Get Email&Id by Email
    public function checkinvite()
    {
        $stmt = $this->db->run("SELECT id FROM users WHERE id = $_REQUEST[invite] AND secret = " . sqlesc($_REQUEST["secret"]));
        $invite_row = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function selectAvatar($id)
    {
        $stmt = $this->db->run("SELECT avatar FROM users WHERE id=?", [$id])->fetch(PDO::FETCH_ASSOC);
        return $stmt;
    }

    // Function That Removes All From An Account
    public function deleteuser($userid)
    {
        $this->db->run("DELETE FROM users WHERE id = $userid");
        $this->db->run("DELETE FROM warnings WHERE userid = $userid");
        $this->db->run("DELETE FROM ratings WHERE user = $userid");
        $this->db->run("DELETE FROM peers WHERE userid = $userid");
        $this->db->run("DELETE FROM completed WHERE userid = $userid");
        $this->db->run("DELETE FROM reports WHERE addedby = $userid");
        $this->db->run("DELETE FROM reports WHERE votedfor = $userid AND type = 'user'");
        $this->db->run("DELETE FROM forum_readposts WHERE userid = $userid");
        $this->db->run("DELETE FROM pollanswers WHERE userid = $userid");
        // snatch
        $this->db->run("DELETE FROM `snatched` WHERE `uid` = '$userid'");
    }

    public static function coloredname($name)
    {
        $db = new Database();
        $classy = $db->run("SELECT u.class, u.donated, u.warned, u.enabled, g.Color, g.level, u.uploaded, u.downloaded FROM `users` `u` INNER JOIN `groups` `g` ON g.group_id=u.class WHERE username ='" . $name . "'")->fetch();
        $gcolor = $classy->Color;
        if ($classy->donated > 0) {
            $star = "<img src='" . URLROOT . "/assets/images/donor.png' alt='donated' border='0' width='15' height='15'>";
        } else {
            $star = "";
        }
        if ($classy->warned == "yes") {
            $warn = "<img src='" . URLROOT . "/assets/images/warn.png' alt='Warn' border='0'>";
        } else {
            $warn = "";
        }
        if ($classy->enabled == "no") {
            $disabled = "<img src='" . URLROOT . "/assets/images/disabled.png' title='Disabled' border='0'>";
        } else {
            $disabled = "";
        }
        return stripslashes("<font color='" . $gcolor . "'>" . $name . "" . $star . "" . $warn . "" . $disabled . "</font>");
    }

    public static function where($where, $userid, $update = 1)
    {
        $db = new Database();
        if (!Validate::ID($userid)) {
            die;
        }
        if (empty($where)) {
            $where = "Unknown Location...";
        }
        if ($update) {
            $db->run("UPDATE users SET page=? WHERE id=?", [$where, $userid]);
        }
        if (!$update) {
            return $where;
        } else {
            return;
        }
    }

    public static function echouser($id)
    {
        if ($id != '') {
            $username = DB::run("SELECT username FROM users WHERE id=$id")->fetchColumn();
            $user = "<option value=\"$id\">$username</option>\n";
        } else {
            $user = "<option value=\"0\">---- " . Lang::T("NONE_SELECTED") . " ----</option>\n";
        }
        $stmt = DB::run("SELECT * FROM users")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($stmt as $arr) {
            $user .= "<option value=\"$arr[id]\">$arr[username]</option>\n";
        }
        echo $user;
    }

}
