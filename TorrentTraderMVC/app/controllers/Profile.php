<?php
class Profile
{
    public function __construct()
    {
        $this->session = Auth::user(0, 2);
    }

    public function index()
    {
        $id = (int) Input::get("id");
        if (!Validate::Id($id)) {
            Redirect::autolink(URLROOT, Lang::T("INVALID_USER_ID"));
        }
        // can view own but not others
        if ($_SESSION["view_users"] == "no" && $_SESSION["id"] != $id) {
            Redirect::autolink(URLROOT, Lang::T("NO_USER_VIEW"));
        }
        $user = Users::getUserById($id);
        if (!$user) {
            Redirect::autolink(URLROOT, Lang::T("NO_USER_WITH_ID") . " $id.");
        }
        // user not ready to be seen yet
        if (($user["enabled"] == "no" || ($user["status"] == "pending")) && $_SESSION["edit_users"] == "no") {
            Redirect::autolink(URLROOT, Lang::T("NO_ACCESS_ACCOUNT_DISABLED"));
        }
        // Start Blocked Users
        $blocked = DB::run("SELECT id FROM friends WHERE userid=$user[id] AND friend='enemy' AND friendid=$_SESSION[id]");
        $show = $blocked->rowCount();
        if ($show != 0 && $_SESSION["control_panel"] != "yes") {
            Redirect::autolink(URLROOT, "You're blocked by this member and you can not see his profile!");
        }
        // country
        $country = Countries::getCountryName($user['country']);

        // ratio
        if ($user["downloaded"] > 0) {
            $ratio = $user["uploaded"] / $user["downloaded"];
        } else {
            $ratio = "---";
        }

        $numtorrents = get_row_count("torrents", "WHERE owner = $id");
        $numcomments = get_row_count("comments", "WHERE user = $id");
        $numforumposts = get_row_count("forum_posts", "WHERE userid = $id");
        $qry = DB::run("SELECT COUNT(`hnr`) FROM `snatched` WHERE `uid` = '$id' AND `hnr` = 'yes'");
        $res = $qry->fetch(PDO::FETCH_ASSOC);
        $numhnr = $res[0];

        $avatar = htmlspecialchars($user["avatar"]);
        if (!$avatar) {
            $avatar = URLROOT . "/assets/images/default_avatar.png";
        }

        $usersignature = stripslashes($user["signature"]); // todo

        $arr = Friend::countFriendAndEnemy($_SESSION['id'], $id);
        $friend = $arr['friend'];
        $block = $arr['enemy'];

        $cardheader = sprintf(Lang::T("USER_DETAILS_FOR"), Users::coloredname($user["username"]));
        $user1 = Users::getAll($id);
        $data = [
            'title' => $cardheader,
            'id' => $id,
            'friend' => $friend,
            'block' => $block,
            'country' => $country,
            'ratio' => $ratio,
            'numhnr' => $numhnr,
            'avatar' => $avatar,
            'numtorrents' => $numtorrents,
            'numcomments' => $numcomments,
            'numforumposts' => $numforumposts,
            'usersignature' => $usersignature,
            'selectuser' => $user1,
        ];
        View::render('user/profile', $data, 'user');
    }

    public function edit()
    {
        global $tzs;
        $id = (int) Input::get("id");

        if ($_SESSION['class'] < _MODERATOR && $id != $_SESSION['id']) {
            Redirect::autolink(URLROOT, Lang::T("You dont have permission"));
        }
        $user = Users::getUserById($id);

        // Stylesheet
        $stylesheets = Stylesheets::getStyleDropDown($user['stylesheet']);
        // Country
        $countries = Countries::pickCountry($user['country']);
        // Timezone
        $tz = TimeDate::timeZoneDropDown($user['tzoffset']);
        //Teams
        $teams = Team::dropDownTeams($user['team']);
        $gender = "<option value='Male'" . ($user['gender'] == "Male" ? " selected='selected'" : "") . ">" . Lang::T("MALE") . "</option>\n"
        . "<option value='Female'" . ($user['gender'] == "Female" ? " selected='selected'" : "") . ">" . Lang::T("FEMALE") . "</option>\n";

        $user1 = Users::getAll($id);
        $cardheader = sprintf(Lang::T("USER_DETAILS_FOR"), Users::coloredname($user["username"]));
        $data = [
            'title' => $cardheader,
            'stylesheets' => $stylesheets,
            'countries' => $countries,
            'teams' => $teams,
            'tz' => $tz,
            'gender' => $gender,
            'id' => $id,
            'selectuser' => $user1,
        ];
        View::render('user/edit', $data, 'user');
    }

    public function submit()
    {
        $id = (int) Input::get("id");
        if ($_SESSION['class'] < _MODERATOR && $id != $_SESSION['id']) {
            Redirect::autolink(URLROOT, Lang::T("You dont have permission"));
        }
        if (Input::exist()) {
            $avatar = strip_tags($_POST["avatar"]);
            $title = strip_tags($_POST["title"]);
            $signature = $_POST["signature"];
            $stylesheet = $_POST["stylesheet"];
            $client = strip_tags($_POST["client"]);
            $age = $_POST["age"];
            $gender = $_POST["gender"];
            $country = $_POST["country"];
            $teams = $_POST["teams"];
            $acceptpms = $_POST["acceptpms"];
            $pmnotif = $_POST["pmnotif"];
            $privacy = $_POST["privacy"];
            $notifs = ($pmnotif == 'yes' ? "[pm]" : "");
            if ($_POST['resetpasskey']) {
                $passkey = '';
                DB::run("UPDATE users
                       SET passkey=?
                       WHERE id =?", [$passkey, $id]);
            }
            $timezone = (int) $_POST['tzoffset'];
            if ($acceptpms == "yes") {
                $acceptpms = 'yes';
            } else {
                $acceptpms = 'no';
            }
            $hideshoutbox = ($_POST["hideshoutbox"] == "yes") ? "yes" : "no";
            // Save New details. todo removed passkey
            DB::run("UPDATE users
                       SET avatar=?, title=?, signature=?, stylesheet=?, client=?, age=?, gender=?, country=?, team=?, hideshoutbox=?, acceptpms=?, privacy=?, notifs=?, tzoffset=?
                       WHERE id =?", [$avatar, $title, $signature, $stylesheet, $client, $age, $gender, $country, $teams, $hideshoutbox, $acceptpms, $privacy, $notifs, $timezone, $id]);
            Redirect::autolink(URLROOT . "/profile/edit?id=$id", Lang::T("User Edited"));
        }
    }

    public function admin()
    {
        $id = (int) Input::get("id");
        if ($_SESSION['class'] < _MODERATOR && $id != $_SESSION['id']) {
            Redirect::autolink(URLROOT . "/profile?id=$id", Lang::T("You dont have permission"));
        }
        $user1 = Users::getUserById($id);
        $user = Users::getAll($id);
        $cardheader = sprintf(Lang::T("USER_DETAILS_FOR"), Users::coloredname($user1["username"]));
        $data = [
            'id' => $id,
            'title' => $cardheader,
            'selectuser' => $user,
        ];
        View::render('user/admin', $data, 'user');
    }

    public function submited()
    {
        $id = (int) $_GET["id"];
        if ($_SESSION['class'] < 5 && $id != $_SESSION['id']) {
            Redirect::autolink(URLROOT . "/profile?id=$id", Lang::T("You dont have permission"));
        }
        if (Input::exist()) {
            $downloaded = strtobytes(Input::get("downloaded"));
            $uploaded = strtobytes(Input::get("uploaded"));
            $ip = Input::get("ip");
            $class = (int) Input::get("class");
            $donated = (float) Input::get("donated");
            $password = Input::get("password");
            $warned = Input::get("warned");
            $forumbanned = Input::get("forumbanned");
            $downloadbanned = Input::get("downloadbanned");
            $shoutboxpos = Input::get("shoutboxpos");
            $modcomment = Input::get("modcomment");
            $enabled = Input::get("enabled");
            $invites = (int) Input::get("invites");
            $email = Input::get("email");
            $bonus = Input::get("bonus");

            if (!Validate::Email($email)) {
                Redirect::autolink(URLROOT . "/profile?id=$id", Lang::T("EMAIL_ADDRESS_NOT_VALID"));
            }

            //change user class
            $arr = DB::run("SELECT class FROM users WHERE id=?", [$id])->fetch();
            $uc = $arr['class'];
            // skip if class is same as current
            if ($uc != $class && $uc > $_SESSION['class']) {
                Redirect::autolink(URLROOT . "/admin?id=$id", Lang::T("YOU_CANT_DEMOTE_YOURSELF"));
            } elseif ($uc == $_SESSION['class']) {
                Redirect::autolink(URLROOT . "/admin?id=$id", Lang::T("YOU_CANT_DEMOTE_SOMEONE_SAME_LVL"));
            } else {
                DB::run("UPDATE users SET class=? WHERE id=?", [$class, $id]);
                // Notify user
                $prodemoted = ($class > $uc ? "promoted" : "demoted");
                $msg = "You have been $prodemoted to " . Groups::get_user_class_name($class) . " by " . $_SESSION["username"] . "";
                $added = TimeDate::get_date_time();
                DB::run("INSERT INTO messages (sender, receiver, msg, added) VALUES(?,?,?,?)", [0, $_SESSION['id'], $msg, $added]);
            }

            //continue updates
            DB::run("UPDATE users
            SET email=?, downloaded=?, uploaded=?, ip=?, donated=?, forumbanned=?, warned=?,
             modcomment=?, enabled=?, invites=? , downloadbanned=?, shoutboxpos=?, seedbonus=?
            WHERE id=?", [$email, $downloaded, $uploaded, $ip, $donated, $forumbanned, $warned, $modcomment,
                $enabled, $invites, $downloadbanned, $shoutboxpos, $bonus, $id]);

            Logs::write($_SESSION['username'] . " has edited user: $id details");

            if (Input::get('resetpasskey') == 'yes') {
                DB::run("UPDATE users SET passkey=? WHERE id=?", ['', $uploaded]);
            }

            $chgpasswd = Input::get('chgpasswd') == 'yes' ? true : false;
            if ($chgpasswd) {
                $passres = DB::run("SELECT password FROM users WHERE id=?", [$id])->fetch();
                if ($password != $passres['password']) {
                    $password = password_hash($password, PASSWORD_BCRYPT);
                    DB::run("UPDATE users SET password=? WHERE id=?", [$password, $id]);
                    Logs::write($_SESSION['username'] . " has changed password for user: $id");
                }
            }
            Redirect::autolink(URLROOT . "/profile?id=$id", Lang::T("User Edited"));
            die;
        }
    }

    public function delete()
    {
        $userid = (int) Input::get("userid");
        $username = Input::get("username");
        $delreason = Input::get("delreason");
        if ($_SESSION["delete_users"] != "yes") {
            Redirect::autolink(URLROOT . "/profile?id=$userid", Lang::T("TASK_ADMIN"));
        }
        if (!Validate::Id($userid)) {
            Redirect::autolink(URLROOT . "/profile?id=$userid", Lang::T("INVALID_USERID"));
        }
        if ($_SESSION["id"] == $userid) {
            Redirect::autolink(URLROOT . "/profile?id=$userid", "You cannot delete yourself.");
        }
        if (!$delreason) {
            Redirect::autolink(URLROOT . "/profile?id=$userid", Lang::T("MISSING_FORM_DATA"));
        }
        Users::deleteuser($userid);
        Logs::write($_SESSION['username'] . " has deleted account: $username");
        Redirect::autolink(URLROOT . "/profile?id=$userid", Lang::T("USER_DELETE"));
        die;
    }

}