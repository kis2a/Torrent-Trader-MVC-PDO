<?php
class Signup extends Controller
{
    public function __construct()
    {
        $this->session = Auth::user(0, 0);
    }

    public function index()
    {
        //check if IP is already a peer
        if (IPCHECK) {
            $ip = $_SERVER['REMOTE_ADDR'];
            $ipq = get_row_count("users", "WHERE ip = '$ip'");
            if ($ipq >= ACCOUNTMAX) {
                Redirect::autolink(URLROOT . '/login', "This IP is already in use !");
            }
        }
        // Check if we're signing up with an invite
        $invite = Input::get("invite");
        $secret = Input::get("secret");
        if (!Validate::Id($invite) || strlen($secret) != 20) {
            if (INVITEONLY) {
                Redirect::autolink(URLROOT . '/home', "<center>" . Lang::T("INVITE_ONLY_MSG") . "<br></center>");
            }
        } else {
            $invite_row = Users::selectInviteIdBySecret($invite, $secret);
            if (!$invite_row) {
                Redirect::autolink(URLROOT . '/home', Lang::T("INVITE_ONLY_NOT_FOUND") . "" . (SIGNUPTIMEOUT / 86400) . "days.");
            }
        }
        $title = Lang::T("SIGNUP");
        // Template
        $data = [
            'title' => $title,
            'invite' => $invite_row,
        ];
        View::render('user/signup', $data, 'user');
    }

    public function submit()
    {
        if (Input::exist()) {
            $wantusername = Input::get("wantusername");
            $email = Input::get("email");
            $wantpassword = Input::get("wantpassword");
            $passagain = Input::get("passagain");
            $country = Input::get("country");
            $gender = Input::get("gender");
            $client = Input::get("client");
            $age = Input::get("age");
            // Is It A Invite
            $secret = Input::get("secret");
            $invite = Input::get("invite");
            if (strlen($secret) == 20 || !is_numeric($invite)) {
                $invite_row = Users::selectInviteIdBySecret($invite, $secret);
            }

            $message = $this->validSign($wantusername, $email, $wantpassword, $passagain, $invite_row);
            if ($message == "") {
                // If NOT Invite Check
                if (!$invite_row) {
                    // get max members, and check how many users there is
                    $numsitemembers = get_row_count("users");
                    if ($numsitemembers >= MAXUSERS) {
                        $msg = Lang::T("SITE_FULL_LIMIT_MSG") . number_format(MAXUSERS) . " " . Lang::T("SITE_FULL_LIMIT_REACHED_MSG") . " " . number_format($numsitemembers) . " members";
                        Redirect::autolink(URLROOT . '/home', $msg);
                    }
                    // check email isnt banned
                    $maildomain = (substr($email, strpos($email, "@") + 1));
                    $a = DB::run("SELECT count(*) FROM email_bans where mail_domain=?", [$email])->fetchColumn();
                    if ($a != 0) {
                        $message = sprintf(Lang::T("EMAIL_ADDRESS_BANNED_S"), $email);
                    }
                    $a = DB::run("SELECT count(*) FROM email_bans where mail_domain LIKE '%$maildomain%'")->fetchColumn();
                    if ($a != 0) {
                        $message = sprintf(Lang::T("EMAIL_ADDRESS_BANNED_S"), $email);
                    }
                    // check if email addy is already in use
                    $a = DB::run("SELECT count(*) FROM users where email=?", [$email])->fetchColumn();
                    if ($a != 0) {
                        $message = sprintf(Lang::T("EMAIL_ADDRESS_INUSE_S"), $email);
                    }
                }

                //check username isnt in use
                $count = DB::run("SELECT count(*) FROM users WHERE  username=?", [$wantusername])->fetchColumn();
                if ($count != 0) {
                    $message = sprintf(Lang::T("USERNAME_INUSE_S"), $wantusername);
                }

                $secret = Helper::mksecret(); //generate secret field
                $wantpassword = password_hash($wantpassword, PASSWORD_BCRYPT); // hash the password
            }
            // Checks Returns Message
            if ($message != "") {
                Redirect::autolink(URLROOT . '/login', $message);
            }

            if ($message == "") {
                // Invited User
                if ($invite_row) {
                    Users::updateUserBits($wantusername, $wantpassword, $secret, 'confirmed', TimeDate::get_date_time(), $invite_row['id']);

                    //send pm to new user
                    if (WELCOMEPM_ON) {
                        $dt = TimeDate::get_date_time();
                        $msg = WELCOMEPM_MSG;
                        Message::insertmessage(0, $invite_row['id'], $dt, 'Welcome', $msg, 'yes', 'in');
                    }
                    Redirect::autolink(URLROOT . '/login', Lang::T("ACCOUNT_ACTIVATED"));
                    die;
                }

                if (CONFIRMEMAIL) {
                    $status = "pending";
                } else {
                    $status = "confirmed";
                }
                // Make first member admin
                if ($numsitemembers == '0') {
                    $signupclass = '7';
                } else {
                    $signupclass = '1';
                    // Shout new user
                    $msg_shout = "New User: " . $wantusername . " has joined.";
                    Shoutboxs::insertShout(0, TimeDate::get_date_time(), 'System', $msg_shout);
                }

                DB::run("
                    INSERT INTO users
                    (username, password, secret, email, status, added, last_login,
                    last_access, age, country, gender, client, stylesheet, language, class, ip)
                    VALUES
                    (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
                    [$wantusername, $wantpassword, $secret, $email, $status, TimeDate::get_date_time(),
                        TimeDate::get_date_time(), TimeDate::get_date_time(), $age, $country, $gender,
                        $client, DEFAULTTHEME, DEFAULTLANG, $signupclass, Ip::getIP()]);
                $id = DB::lastInsertId();

                //send pm to new user
                if (WELCOMEPM_ON) {
                    $dt = TimeDate::get_date_time();
                    $mess = WELCOMEPM_MSG;
                    Message::insertmessage(0, $id, $dt, 'Welcome', $mess, 'yes', 'in');
                }

                if (ACONFIRM) {
                    $body = Lang::T("YOUR_ACCOUNT_AT") . " " . SITENAME . " " . Lang::T("HAS_BEEN_CREATED_YOU_WILL_HAVE_TO_WAIT") . "\n\n" . SITENAME . " " . Lang::T("ADMIN");
                } else { //NO ADMIN CONFIRM, BUT EMAIL CONFIRM
                    $body = Lang::T("YOUR_ACCOUNT_AT") . " " . SITENAME . " " . Lang::T("HAS_BEEN_APPROVED_EMAIL") . "\n\n	" . URLROOT . "/confirmemail/signup?id=$id&secret=$secret\n\n" . Lang::T("HAS_BEEN_APPROVED_EMAIL_AFTER") . "\n\n	" . Lang::T("HAS_BEEN_APPROVED_EMAIL_DELETED") . "\n\n" . URLROOT . " " . Lang::T("ADMIN");
                }
                if (CONFIRMEMAIL) {
                    $TTMail = new TTMail();
                    $TTMail->Send($email, "Your " . SITENAME . " User Account", $body, "", "-f" . SITEEMAIL . "");
                    Redirect::autolink(URLROOT . '/login', Lang::T("A_CONFIRMATION_EMAIL_HAS_BEEN_SENT") . " (" . htmlspecialchars($email) . "). " . Lang::T("ACCOUNT_CONFIRM_SENT_TO_ADDY_REST") . " <br/ >");
                } else {
                    Redirect::autolink(URLROOT . '/login', Lang::T("ACCOUNT_ACTIVATED"));
                }
                die;
            }
        } else {
            Redirect::to(URLROOT . "/signup");
        }
    }

    public function validSign($wantusername, $email, $wantpassword, $passagain, $invite_row)
    {
        if (Validate::isEmpty($wantpassword) || (Validate::isEmpty($email) && !$invite_row) || Validate::isEmpty($wantusername)) {
            $message = Lang::T("DONT_LEAVE_ANY_FIELD_BLANK");
        } elseif (strlen($wantusername) > 50) {
            $message = sprintf(Lang::T("USERNAME_TOO_LONG"), 16);
        } elseif ($wantpassword != $passagain) {
            $message = Lang::T("PASSWORDS_NOT_MATCH");
        } elseif (strlen($wantpassword) < 6) {
            $message = sprintf(Lang::T("PASS_TOO_SHORT_2"), 6);
        } elseif (strlen($wantpassword) > 16) {
            $message = sprintf(Lang::T("PASS_TOO_LONG_2"), 16);
        } elseif ($wantpassword == $wantusername) {
            $message = Lang::T("PASS_CANT_MATCH_USERNAME");
        } elseif (!$invite_row && !Validate::Email($email)) {
            $message = "That doesn't look like a valid email address.";
        }
        return $message;
    }

}