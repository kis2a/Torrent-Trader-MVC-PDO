<?php
class Stylesheet extends Controller
{

    public function __construct()
    {
        $this->session = Auth::user(0, 2);
    }

    public function index()
    {
        $updateset = array();
        $stylesheet = Input::get('stylesheet');
        $language = Input::get('language');
        $updateset[] = "stylesheet = '$stylesheet'";
        $updateset[] = "language = '$language'";
        if (count($updateset)) {
            Users::updateset($updateset, $this->session['id']);
        }
        if (empty($_SERVER["HTTP_REFERER"])) {
            Redirect::to(URLROOT."/home");
            return;
        }
        Redirect::to($_SERVER["HTTP_REFERER"]);
    }
}