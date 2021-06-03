<?php
class Adminstylesheet extends Controller
{

    public function __construct()
    {
        Auth::user();
        Auth::isStaff();
        // $this->userModel = $this->model('User');
        $this->logsModel = $this->model('Logs');
        $this->valid = new Validation();
    }


    public function index()
    {
        $res = DB::run("SELECT * FROM stylesheets");
        $data = [
            'title' => Lang::T("THEME_MANAGEMENT"),
            'sql' => $res
        ];
        $this->view('stylesheet/admin/index', $data, 'admin');
    }



    public function add()
    {
        if ($_POST['do'] == "add") {
            if ($_POST) {
                if (empty($_POST['name'])) {
                    $error .= Lang::T("THEME_NAME_WAS_EMPTY") . "<br />";
                }
                if (empty($_POST['uri'])) {
                    $error .= Lang::T("THEME_FOLDER_NAME_WAS_EMPTY");
                }
                if ($error) {
                    show_error_msg(Lang::T("ERROR"), Lang::T("THEME_NOT_ADDED_REASON") . " $error", 1);
                }
                if ($qry = DB::run("INSERT INTO stylesheets (name, uri) VALUES (?, ?)", [$_POST["name"], $_POST["uri"]])) {
                    show_error_msg(Lang::T("SUCCESS"), "Theme '" . htmlspecialchars($_POST["name"]) . "' added.", 0);
                } elseif ($qry->errorCode() == 1062) {
                    show_error_msg(Lang::T("FAILED"), Lang::T("THEME_ALREADY_EXISTS"), 0);
                } else {
                    show_error_msg(Lang::T("FAILED"), Lang::T("THEME_NOT_ADDED_DB_ERROR") . " " . $qry->errorInfo(), 0);
                }
            }
        }
            $data = [
                'title' => Lang::T("Theme")
            ];
            $this->view('stylesheet/admin/add', $data, 'admin');
    }   
    
    public function delete()
    {
            if (!@count($_POST["ids"])) {
                show_error_msg(Lang::T("ERROR"), Lang::T("NOTHING_SELECTED"), 1);
            }
            $ids = array_map("intval", $_POST["ids"]);
            $ids = implode(', ', $ids);
            DB::run("DELETE FROM `stylesheets` WHERE `id` IN ($ids)");
            DB::run("UPDATE `users` SET `stylesheet` = " . DEFAULTTHEME . " WHERE stylesheet NOT IN (SELECT id FROM stylesheets)");
            Redirect::autolink(URLROOT . "/adminstylesheet", Lang::T("THEME_SUCCESS_THEME_DELETED"));
    }
    
}