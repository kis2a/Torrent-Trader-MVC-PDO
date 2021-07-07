<?php
class Adminstylesheet extends Controller
{

    public function __construct()
    {
        $this->session = Auth::user(_ADMINISTRATOR, 2);
        // $this->userModel = $this->model('User');
        $this->logsModel = $this->model('Logs');
        
    }


    public function index()
    {
        $res = DB::run("SELECT * FROM stylesheets");
        $data = [
            'title' => Lang::T("THEME_MANAGEMENT"),
            'sql' => $res
        ];
        View::render('stylesheet/admin/index', $data, 'admin');
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
                    Redirect::autolink(URLROOT."/adminstylesheet/add", Lang::T("THEME_NOT_ADDED_REASON") . " $error");
                }
                if ($qry = DB::run("INSERT INTO stylesheets (name, uri) VALUES (?, ?)", [$_POST["name"], $_POST["uri"]])) {
                    Redirect::autolink(URLROOT."/adminstylesheet/add", "Theme '" . htmlspecialchars($_POST["name"]) . "' added.");
                } elseif ($qry->errorCode() == 1062) {
                    Redirect::autolink(URLROOT."/adminstylesheet/add", Lang::T("THEME_ALREADY_EXISTS"));
                } else {
                    Redirect::autolink(URLROOT."/adminstylesheet/add", Lang::T("THEME_NOT_ADDED_DB_ERROR") . " " . $qry->errorInfo());
                }
            }
        }
            $data = [
                'title' => Lang::T("Theme")
            ];
            View::render('stylesheet/admin/add', $data, 'admin');
    }   
    
    public function delete()
    {
            if (!@count($_POST["ids"])) {
                Redirect::autolink(URLROOT."/adminstylesheet", Lang::T("NOTHING_SELECTED"));
            }
            $ids = array_map("intval", $_POST["ids"]);
            $ids = implode(', ', $ids);
            DB::run("DELETE FROM `stylesheets` WHERE `id` IN ($ids)");
            DB::run("UPDATE `users` SET `stylesheet` = " . DEFAULTTHEME . " WHERE stylesheet NOT IN (SELECT id FROM stylesheets)");
            Redirect::autolink(URLROOT . "/adminstylesheet", Lang::T("THEME_SUCCESS_THEME_DELETED"));
    }
    
}