<?php
class Adminnews extends Controller
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
        $res = DB::run("SELECT * FROM news ORDER BY added DESC");
        $data = [
            'title' => Lang::T("NEWS"),
            'sql' => $res
        ];
        $this->view('news/index', $data, 'admin');
    }

    public function add()
    {
        $data = [
            'title' => Lang::T("CP_NEWS_ADD"),
        ];
        $this->view('news/add', $data, 'admin');
    }

    public function submit()
    {
        $body = $_POST["body"];
        if (!$body) {
            show_error_msg(Lang::T("ERROR"), Lang::T("ERR_NEWS_ITEM_CAN_NOT_BE_EMPTY"), 1);
        }
        $title = $_POST['title'];
        if (!$title) {
            show_error_msg(Lang::T("ERROR"), Lang::T("ERR_NEWS_TITLE_CAN_NOT_BE_EMPTY"), 1);
        }
        $added = $_POST["added"];
        if (!$added) {
            $added = TimeDate::get_date_time();
        }
        $afr = DB::run("INSERT INTO news (userid, added, body, title) VALUES (?,?,?,?)", [$_SESSION['id'], $added, $body, $title]);
        if ($afr) {
            Redirect::autolink(URLROOT . "/adminnews", Lang::T("CP_NEWS_ITEM_ADDED_SUCCESS"));
        } else {
            show_error_msg(Lang::T("ERROR"), Lang::T("CP_NEWS_UNABLE_TO_ADD"), 1);
        }
    }

    public function edit()
    {
        $newsid = (int) $_GET["newsid"];
        if (!$this->valid->validId($newsid)) {
            show_error_msg(Lang::T("ERROR"), sprintf(Lang::T("CP_NEWS_INVAILD_ITEM_ID"), $newsid), 1);
        }
        $res = DB::run("SELECT * FROM news WHERE id=?", [$newsid]);
        if ($res->rowCount() != 1) {
            show_error_msg(Lang::T("ERROR"), sprintf(Lang::T("CP_NEWS_NO_ITEM_WITH_ID"), $newsid), 1);
        }
        
        $data = [
            'newsid' => $newsid,
            'res' => $res,
            'title' => Lang::T("CP_NEWS_EDIT"),
            'returnto' => $returnto = htmlspecialchars($_GET['returnto'])
        ];
        $this->view('news/edit', $data, 'admin');
    }

    public function updated()
    {
        $newsid = (int) $_GET["id"];
        $body = $_POST['body'];
        if ($body == "") {
            show_error_msg(Lang::T("ERROR"), Lang::T("FORUMS_BODY_CANNOT_BE_EMPTY"), 1);
        }
        $title = $_POST['title'];
        if ($title == "") {
            show_error_msg(Lang::T("ERROR"), Lang::T("ERR_NEWS_TITLE_CAN_NOT_BE_EMPTY"), 1);
        }
        $editedat = TimeDate::get_date_time();
        DB::run("UPDATE news SET body=?, title=? WHERE id=?", [$body, $title, $newsid]);
        Redirect::autolink(URLROOT . "/adminnews", Lang::T("CP_NEWS_ITEM_WAS_EDITED_SUCCESS"));

    }

    public function newsdelete()
    {
        $newsid = (int) $_GET["newsid"];
        if (!$this->valid->validId($newsid)) {
            show_error_msg(Lang::T("ERROR"), sprintf(Lang::T("CP_NEWS_INVAILD_ITEM_ID"), $newsid), 1);
        }
        DB::run("DELETE FROM news WHERE id=?", [$newsid]);
        DB::run("DELETE FROM comments WHERE news =?", [$newsid]);
        Redirect::autolink(URLROOT . "/adminnews", Lang::T("CP_NEWS_ITEM_DEL_SUCCESS"));
    }

}