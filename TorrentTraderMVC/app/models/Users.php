<?php
class Users
{

    // Get User by Username
    public static function getUserByUsername($username)
    {
        $row = DB::run("SELECT id, password, secret, status, enabled FROM users WHERE username =? ", [$username])->fetch();
        return $row;
    }

    public static function updateset($updateset = [], $id)
    {
    DB::run("UPDATE `users` SET " . implode(', ', $updateset) . " WHERE `id` =?", [$id]);
}

    public static function warnUserWithId($id)
    {
        DB::run("UPDATE users SET warned=? WHERE id=?", ['yes', $id]);
    }

    public static function updateUserPasswordSecret($chpassword, $secret, $id)
    {
        $row = DB::run("UPDATE users SET password =?, secret =?
            WHERE id =?", [$chpassword, $secret, $id]);

    }

    public static function updateUserBits($wantusername, $wantpassword, $secret, $status, $added, $id)
    {
        $row = DB::run("
        UPDATE users
        SET username=?, password=?, secret=?, status=?, added=?
        WHERE id=?",
        [$wantusername, $wantpassword, $secret, $status, $added, $id]);
    }

    public static function updateUserEditSecret($sec, $id)
    {
        $row = DB::run("UPDATE users SET editsecret =? WHERE id =?", [$sec, $id]);

    }
    public static function updateUserAvatar($avatar, $id)
    {
        $row = DB::run("UPDATE users SET avatar=? WHERE id =?", [$avatar, $id]);

    }
    public static function selectUserEmail($id)
    {
        $row = DB::run("SELECT email FROM users WHERE id=?", [$id])->fetch(PDO::FETCH_ASSOC);
        return $row;
    }

    public static function selectInviteIdBySecret($invite, $secret)
    {
        $row = DB::run("SELECT id FROM users WHERE id = ? AND secret = ?", [$invite, $secret])->fetch(PDO::FETCH_ASSOC);
        return $row;
    }

    // Update User pass & secret
    public static function recoverUpdate($wantpassword, $newsec, $pid, $psecret)
    {
        $row = DB::run("UPDATE `users` SET `password` =?, `secret` =? WHERE `id`=? AND `secret` =?", [$wantpassword, $newsec, $pid, $psecret]);
    }

    // Set User secret
    public static function setSecret($sec, $email)
    {
        $row = DB::run("UPDATE `users` SET `secret` =? WHERE `email`=? LIMIT 1", [$sec, $email]);
    }

    // Get Email&Id by Email
    public static function getIdEmailByEmail($email)
    {
        $row = DB::run("SELECT id, username, email FROM users WHERE email=? LIMIT 1", [$email])->fetch();
        return $row;
    }

    public static function updatelogin($token, $id)
    {
        DB::run("UPDATE users SET last_login=?, token=? WHERE id=?", [TimeDate::get_date_time(), $token, $id]);
            
       //$this->db->run("UPDATE users SET last_login = ? WHERE id = ? ", [Helper::get_date_time(), $id]);
    }

    // Get Email&Id by Email
    public static function getUserById($id)
    {
        $user = DB::run("SELECT * FROM users WHERE id=?", [$id])->fetch();
        return $user;
    }
    // Get All User Array
    public static function getAll($id)
    {
        $row = DB::run("SELECT * FROM users WHERE id =? ", [$id]);
        $user1 = $row->fetchAll();
        return $user1;
    }

    // Get Email&Id by Email
    public static function checkinvite()
    {
        $stmt = DB::run("SELECT id FROM users WHERE id = $_REQUEST[invite] AND secret = " . sqlesc($_REQUEST["secret"]));
        $invite_row = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function selectAvatar($id)
    {
        $stmt = DB::run("SELECT avatar FROM users WHERE id=?", [$id])->fetch(PDO::FETCH_ASSOC);
        return $stmt;
    }

    // Function That Removes All From An Account
    public static function deleteuser($userid)
    {
        DB::run("DELETE FROM users WHERE id = $userid");
        DB::run("DELETE FROM warnings WHERE userid = $userid");
        DB::run("DELETE FROM ratings WHERE user = $userid");
        DB::run("DELETE FROM peers WHERE userid = $userid");
        DB::run("DELETE FROM completed WHERE userid = $userid");
        DB::run("DELETE FROM reports WHERE addedby = $userid");
        DB::run("DELETE FROM reports WHERE votedfor = $userid AND type = 'user'");
        DB::run("DELETE FROM forum_readposts WHERE userid = $userid");
        DB::run("DELETE FROM pollanswers WHERE userid = $userid");
        // snatch
        DB::run("DELETE FROM `snatched` WHERE `uid` = '$userid'");
    }

    public static function coloredname($name)
    {
        $classy = DB::run("SELECT u.class, u.donated, u.warned, u.enabled, g.Color, g.level, u.uploaded, u.downloaded FROM `users` `u` INNER JOIN `groups` `g` ON g.group_id=u.class WHERE username ='" . $name . "'")->fetch();
        $gcolor = $classy['Color'];
        if ($classy['donated'] > 0) {
            $star = "<img src='" . URLROOT . "/assets/images/donor.png' alt='donated' border='0' width='15' height='15'>";
        } else {
            $star = "";
        }
        if ($classy['warned'] == "yes") {
            $warn = "<img src='" . URLROOT . "/assets/images/warn.png' alt='Warn' border='0'>";
        } else {
            $warn = "";
        }
        if ($classy['enabled'] == "no") {
            $disabled = "<img src='" . URLROOT . "/assets/images/disabled.png' title='Disabled' border='0'>";
        } else {
            $disabled = "";
        }
        return stripslashes("<font color='" . $gcolor . "'>" . $name . "" . $star . "" . $warn . "" . $disabled . "</font>");
    }

    public static function where($where, $userid, $update = 1)
    {
        if (!Validate::ID($userid)) {
            die;
        }
        if (empty($where)) {
            $where = "Unknown Location...";
        }
        if ($update) {
            DB::run("UPDATE users SET page=? WHERE id=?", [$where, $userid]);
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
