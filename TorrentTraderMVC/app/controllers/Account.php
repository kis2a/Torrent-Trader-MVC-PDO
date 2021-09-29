<?php
class Account
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
        if (Users::has('class') < _MODERATOR && $id != Users::has('id')) {
            Redirect::autolink(URLROOT . "/index", Lang::T("NO_PERMISSION"));
        }

        if (Input::get('do') == "newpassword") {
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
                $message = Lang::T("YOU_DID_NOT_ENTER_ANYTHING");
            }
            
            Users::updateUserPasswordSecret($chpassword, $secret, $id);

            if (!$message) {
                Redirect::autolink(URLROOT . "/logout", Lang::T("PASSWORD_CHANGED_OK"));
            } else {
                Redirect::autolink(URLROOT . "/account/changepw?id=$id", $message);
            }
        }

        $data = [
            'title' => Lang::T('CHANGE_PASS'),
            'id' => $id,
        ];
        View::render('user/accountpassword', $data, 'user');
    }

    public function email()
    {
        $id = (int) Input::get("id");
        if ($id != Users::has('id')) {
            Redirect::autolink(URLROOT . "/index", Lang::T("NO_PERMISSION"));
        }

        if (Input::exist()) {
            $email = $_POST["email"];
            $sec = Helper::mksecret();
            $obemail = rawurlencode($email);
            $sitename = URLROOT;

            $body = file_get_contents(APPROOT . "/views/emails/changeemail.php");
            $body = str_replace("%usersname%", Users::has("username"), $body);
            $body = str_replace("%sitename%", $sitename, $body);
            $body = str_replace("%usersip%", $_SERVER["REMOTE_ADDR"], $body);
            $body = str_replace("%usersid%", Users::has("id"), $body);
            $body = str_replace("%userssecret%", $sec, $body);
            $body = str_replace("%obemail%", $obemail, $body);
            $body = str_replace("%newemail%", $email, $body);

            $TTMail = new TTMail();
            $TTMail->Send($email, "$sitename profile update confirmation", $body, "From: " . Config::TT()['SITEEMAIL'] . "", "-f" . Config::TT()['SITEEMAIL'] . "");
            Users::updateUserEditSecret($sec, Users::has('id'));
            Redirect::autolink(URLROOT . "/profile?id=$id", Lang::T("EMAIL_CHANGE_SEND"));
        }
        $user = Users::selectUserEmail($id);
        $data = [
            'id' => $id,
            'email' => $user['email'],
        ];
        View::render('user/accountemail', $data, 'user');
    }

    public function avatar()
    {
        $id = (int) Input::get("id");
        if ($id != Users::has('id')) {
            Redirect::autolink(URLROOT . "/index", Lang::T("NO_PERMISSION"));
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
                Redirect::autolink(URLROOT . "/profile/edit?id=$id", Lang::T("UP_AVATAR")." OK");
            }
        }
        $data = [
            'title' => Lang::T("AVATAR_UPLOAD"),
            'id' => $id,
        ];
        View::render('user/accountavatar', $data, 'user');
    }

}