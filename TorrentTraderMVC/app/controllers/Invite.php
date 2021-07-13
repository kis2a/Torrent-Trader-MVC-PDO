<?php
class Invite extends Controller
{

    public function __construct()
    {
        $this->session = Auth::user(0, 2);
    }

    public function index()
    {
        if (!INVITEONLY && !ENABLEINVITES) {
            Redirect::autolink(URLROOT, Lang::T("INVITES_DISABLED_MSG"));
        }
        $users = get_row_count("users", "WHERE enabled = 'yes'");
        if ($users >= MAXUSERSINVITE) {
            Redirect::autolink(URLROOT, "Sorry, The current user account limit (" . number_format(MAXUSERSINVITE) . ") has been reached. Inactive accounts are pruned all the time, please check back again later...");
        }
        if ($_SESSION["invites"] == 0) {
            Redirect::autolink(URLROOT, Lang::T("YOU_HAVE_NO_INVITES_MSG"));
        }
        $data = [
            'title' => 'Invite User',
        ];
        View::render('invite/index', $data, 'user');
    }

    public function submit()
    {
        if (Input::get("take")) {
            $email = Input::get("email");
            if (!Validate::Email($email)) {
                Redirect::autolink(URLROOT, Lang::T("INVALID_EMAIL_ADDRESS"));
            }
            //check email isnt banned
            $maildomain = (substr($email, strpos($email, "@") + 1));
            $a = DB::run("select count(*) from email_bans where mail_domain=?", [$email])->fetch();
            if ($a[0] != 0) {
                $message = sprintf(Lang::T("EMAIL_ADDRESS_BANNED"), $email);
            }
            $a = DB::run("select count(*) from email_bans where mail_domain=?", [$maildomain])->fetch();
            if ($a[0] != 0) {
                $message = sprintf(Lang::T("EMAIL_ADDRESS_BANNED"), $email);
            }
            // check if email addy is already in use
            if (get_row_count("users", "WHERE email='$email'")) {
                $message = sprintf(Lang::T("EMAIL_ADDRESS_INUSE"), $email);
            }
            if ($message) {
                Redirect::autolink(URLROOT, $message);
            }

            $secret = Helper::mksecret();
            $username = "invite_" . Helper::mksecret(20);
            $ret = DB::run("INSERT INTO users (username, secret, email, status, invited_by, added, stylesheet, language) VALUES (?,?,?,?,?,?,?,?)",
                [$username, $secret, $email, 'pending', $_SESSION["id"], TimeDate::get_date_time(), DEFAULTTHEME, DEFAULTLANG]);
            $id = DB::lastInsertId();
            $invitees = "$id $_SESSION[invitees]";
            DB::run("UPDATE users SET invites = invites - 1, invitees='$invitees' WHERE id = $_SESSION[id]");
            $mess = strip_tags($_POST["mess"]);
            $names = SITENAME;
            $links = URLROOT;
            $emailmain = SITEEMAIL;

            $body = file_get_contents(APPROOT . "/views/emails/inviteuser.php");
            $body = str_replace("%sitename%", $names, $body);
            $body = str_replace("%username%", $_SESSION['username'], $body);
            $body = str_replace("%email%", $email, $body);
            $body = str_replace("%mess%", $mess, $body);
            $body = str_replace("%links%", $links, $body);
            $body = str_replace("%id%", $id, $body);
            $body = str_replace("%secret%", $secret, $body);

            $TTMail = new TTMail();
            $TTMail->Send($email, "$names user registration confirmation", $body, "", "-f$emailmain");
            Redirect::autolink(URLROOT, Lang::T("A_CONFIRMATION_EMAIL_HAS_BEEN_SENT") . " (" . htmlspecialchars($email) . "). " . Lang::T("ACCOUNT_CONFIRM_SENT_TO_ADDY_REST") . " <br/ >");
            die;
        }

    }

    public function invitetree()
    {
        $id = Input::get("id");
        if (!Validate::Id($id)) {
            $id = $_SESSION["id"];
        }
        $res = DB::run("SELECT * FROM users WHERE status = ? AND invited_by = ? ORDER BY username", ['confirmed', $id]);
        $num = $res->rowCount();
        $invitees = number_format(get_row_count("users", "WHERE status = 'confirmed' && invited_by = $id"));
        if ($invitees == 0) {
            Redirect::autolink(URLROOT . "/profile?id=$id", "This member has no invitees");
        }
        if ($id != $_SESSION["id"]) {
            $title = "Invite Tree for [<a href=" . URLROOT . "/profile?id=$id>" . $id . "</a>]";
        } else {
            $title = "You have $invitees invitees " . Users::coloredname($_SESSION["username"]) . "";
        }
        $data = [
            'title' => $title,
            'id' => $id,
            'invitees' => $invitees,
            'res' => $res,
            'num' => $num,
        ];
        View::render('invite/tree', $data, 'user');
    }
    
}