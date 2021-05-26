<?php
class Account extends Controller
{
    public function __construct()
    {
        Auth::user();
        $this->userModel = $this->model('User');
    }

    public function index()
    {
        Redirect::to(URLROOT);
    }

    public function changepw()
    {
        $pdo = new Database();
        $id = (int) $_GET["id"];
        if ($_SESSION['class'] < 5 && $id != $_SESSION['id']) {
            Redirect::autolink(URLROOT . "/index", Lang::T("Sorry Staff only"));
        }

        if ($_POST['do'] == "newpassword") {
            $chpassword = $_POST['chpassword'];
            $passagain = $_POST['passagain'];
            if ($chpassword != "") {
                if (strlen($chpassword) < 6) {
                    $message = Lang::T("PASS_TOO_SHORT");
                }
                if ($chpassword != $passagain) {
                    $message = Lang::T("PASSWORDS_NOT_MATCH");
                }
                $chpassword = password_hash($chpassword, PASSWORD_BCRYPT);
                $secret = mksecret();
            }
            if ((!$chpassword) || (!$passagain)) {
                $message = "You must enter something!";
            }
            
            $this->userModel->updateUserPasswordSecret($chpassword, $secret, $id);

            if (!$message) {
                Session::flash('info', Lang::T("PASSWORD_CHANGED_OK"), URLROOT . "/logout");
            } else {
                Session::flash('info', $message, URLROOT . "/account/changepw?id=$id");
            }
            die();
        }

        $data = [
            'id' => $id,
        ];
        $this->view('user/changepass', $data, 'user');
    }

    public function email()
    {
        $id = (int) $_GET["id"];
        if ($id != $_SESSION['id']) {
            Redirect::autolink(URLROOT . "/index", Lang::T("You dont have permission"));
        }

        if ($_POST) {
            $email = $_POST["email"];
            $sec = mksecret();
            $obemail = rawurlencode($email);
            $sitename = URLROOT;
            $body = <<<EOD
You have requested that your user profile (username {$_SESSION["username"]})
on {$sitename} should be updated with this email address ($email) as
user contact.
If you did not do this, please ignore this email. The person who entered your
email address had the IP address {$_SERVER["REMOTE_ADDR"]}. Please do not reply.
To complete the update of your user profile, please follow this link:
{$sitename}/confirmemail?id={$_SESSION["id"]}&secret=$sec&email=$obemail
Your new email address will appear in your profile after you do this. Otherwise
your profile will remain unchanged.
EOD;

            $TTMail = new TTMail();
            $TTMail->Send($email, "$sitename profile update confirmation", $body, "From: " . SITEEMAIL . "", "-f" . SITEEMAIL . "");
            $this->userModel->updateUserEditSecret($sec, $_SESSION['id']);
            Redirect::autolink(URLROOT . "/profile?id=$id", Lang::T("Email Edited"));
        }

        $user = $this->userModel->selectUserEmail($id);
        $data = [
            'id' => $id,
            'email' => $user['email'],
        ];
        $this->view('user/changeemail', $data, 'user');
    }

    public function avatar()
    {
        $id = $_GET["id"];
        if ($id != $_SESSION['id']) {
            Redirect::autolink(URLROOT . "/index", Lang::T("Its not your account"));
        }
        $id = $_GET['id'];
        if (isset($_FILES["upfile"])) {
            $upload = new Uploader($_FILES["upfile"]);
            $upload->must_be_image();
            $upload->max_size(100); // in MB
            $upload->max_image_dimensions(130, 130);
            $upload->encrypt_name();
            $upload->path("uploads/avatars");
            if (!$upload->upload()) {
                Session::flash('info', "Upload error: " . $upload->get_error() . " image should be 90px x 90px or lower", URLROOT . "/profile/edit?id=$id");
            } else {
                $avatar = URLROOT . "/uploads/avatars/" . $upload->get_name();
                $this->userModel->updateUserAvatar($avatar, $id);
                Session::flash('info', "Avatar Upload OK", URLROOT . "/profile/edit?id=$id");
            }

        }
        $data = [
            'id' => $id,
        ];
        $this->view('user/avatar', $data, 'user');
    }

}
