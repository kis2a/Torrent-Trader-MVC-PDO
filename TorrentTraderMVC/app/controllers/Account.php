<?php
class Account extends Controller
{
    public function __construct()
    {
        $this->session = Auth::user(0, 2);
    }

    public function index()
    {
        Redirect::to(URLROOT);
    }

    public function changepw()
    {
        $id = (int) Input::get("id");
        if ($this->session['class'] < _MODERATOR && $id != $_SESSION['id']) {
            Redirect::autolink(URLROOT . "/index", Lang::T("Sorry Staff only"));
        }

        if ($_POST['do'] == "newpassword") {
            $chpassword = Input::get('chpassword');
            $passagain = Input::get('passagain');
            if ($chpassword != "") {
                if (strlen($chpassword) < 6) {
                    $message = Lang::T("PASS_TOO_SHORT");
                }
                if ($chpassword != $passagain) {
                    $message = Lang::T("PASSWORDS_NOT_MATCH");
                }
                $chpassword = password_hash($chpassword, PASSWORD_BCRYPT);
                $secret = Helper::mksecret();
            }
            if ((!$chpassword) || (!$passagain)) {
                $message = "You must enter something!";
            }
            
            Users::updateUserPasswordSecret($chpassword, $secret, $id);

            if (!$message) {
                Redirect::autolink(URLROOT . "/logout", Lang::T("PASSWORD_CHANGED_OK"));
            } else {
                Redirect::autolink(URLROOT . "/account/changepw?id=$id", $message);
            }
        }

        $data = [
            'id' => $id,
        ];
        View::render('user/changepass', $data, 'user');
    }

    public function email()
    {
        $id = (int) Input::get("id");
        if ($id != $this->session['id']) {
            Redirect::autolink(URLROOT . "/index", Lang::T("You dont have permission"));
        }

        if (Input::exist()) {
            $email = $_POST["email"];
            $sec = Helper::mksecret();
            $obemail = rawurlencode($email);
            $sitename = URLROOT;
            $body = <<<EOD
            You have requested that your user profile (username {$this->session["username"]})
            on {$sitename} should be updated with this email address ($email) as
            user contact.
            If you did not do this, please ignore this email. The person who entered your
            email address had the IP address {$_SERVER["REMOTE_ADDR"]}. Please do not reply.
            To complete the update of your user profile, please follow this link:
            {$sitename}/confirmemail?id={$this->session["id"]}&secret=$sec&email=$obemail
            Your new email address will appear in your profile after you do this. Otherwise
            your profile will remain unchanged.
            EOD;

            $TTMail = new TTMail();
            $TTMail->Send($email, "$sitename profile update confirmation", $body, "From: " . SITEEMAIL . "", "-f" . SITEEMAIL . "");
            Users::updateUserEditSecret($sec, $this->session['id']);
            Redirect::autolink(URLROOT . "/profile?id=$id", Lang::T("Email Edited"));
        }

        $user = Users::selectUserEmail($id);
        $data = [
            'id' => $id,
            'email' => $user['email'],
        ];
        View::render('user/changeemail', $data, 'user');
    }

    public function avatar()
    {
        $id = (int) Input::get("id");
        if ($id != $this->session['id']) {
            Redirect::autolink(URLROOT . "/index", Lang::T("Its not your account"));
        }
        if (isset($_FILES["upfile"])) {
            $upload = new Uploader($_FILES["upfile"]);
            $upload->must_be_image();
            $upload->max_size(100); // in MB
            $upload->max_image_dimensions(130, 130);
            $upload->encrypt_name();
            $upload->path("uploads/avatars");
            if (!$upload->upload()) {
                Redirect::autolink(URLROOT . "/profile/edit?id=$id", "Upload error: " . $upload->get_error() . " image should be 90px x 90px or lower");
            } else {
                $avatar = URLROOT . "/uploads/avatars/" . $upload->get_name();
                Users::updateUserAvatar($avatar, $id);
                Redirect::autolink(URLROOT . "/profile/edit?id=$id", "Avatar Upload OK");
                
            }

        }
        $data = [
            'id' => $id,
        ];
        View::render('user/avatar', $data, 'user');
    }

}