<?php
class Adminnews
{

    public function __construct()
    {
        $this->session = Auth::user(_MODERATOR, 2);
    }

    public function index()
    {
        $res = DB::run("SELECT * FROM news ORDER BY added DESC");
        $data = [
            'title' => Lang::T("NEWS"),
            'sql' => $res
        ];
        View::render('news/index', $data, 'admin');
    }

    public function add()
    {
        $data = [
            'title' => Lang::T("CP_NEWS_ADD"),
        ];
        View::render('news/add', $data, 'admin');
    }

    public function submit()
    {
        $body = $_POST["body"];
        if (!$body) {
            Redirect::autolink(URLROOT."/adminnews/add", Lang::T("ERR_NEWS_ITEM_CAN_NOT_BE_EMPTY"));
        }
        $title = $_POST['title'];
        if (!$title) {
            Redirect::autolink(URLROOT."/adminnews/add", Lang::T("ERR_NEWS_TITLE_CAN_NOT_BE_EMPTY"));
        }
        $added = $_POST["added"];
        if (!$added) {
            $added = TimeDate::get_date_time();
        }
        $afr = DB::run("INSERT INTO news (userid, added, body, title) VALUES (?,?,?,?)", [$_SESSION['id'], $added, $body, $title]);
        if ($afr) {
            Redirect::autolink(URLROOT."/adminnews", Lang::T("CP_NEWS_ITEM_ADDED_SUCCESS"));
        } else {
            Redirect::autolink(URLROOT."/adminnews/add", Lang::T("CP_NEWS_UNABLE_TO_ADD"));
        }
    }

    public function edit()
    {
        $newsid = (int) $_GET["newsid"];
        if (!Validate::Id($newsid)) {
            Redirect::autolink(URLROOT."/adminnews", sprintf(Lang::T("CP_NEWS_INVAILD_ITEM_ID").$newsid));
        }
        $res = DB::run("SELECT * FROM news WHERE id=?", [$newsid]);
        if ($res->rowCount() != 1) {
            Redirect::autolink(URLROOT."/adminnews", sprintf(Lang::T("CP_NEWS_NO_ITEM_WITH_ID").$newsid));
        }
        
        $data = [
            'newsid' => $newsid,
            'res' => $res,
            'title' => Lang::T("CP_NEWS_EDIT"),
            'returnto' => $returnto = htmlspecialchars($_GET['returnto'])
        ];
        View::render('news/edit', $data, 'admin');
    }

    public function updated()
    {
        $newsid = (int) $_GET["id"];
        $body = $_POST['body'];
        if ($body == "") {
            Redirect::autolink(URLROOT."/adminnews/edit", Lang::T("FORUMS_BODY_CANNOT_BE_EMPTY"));
        }
        $title = $_POST['title'];
        if ($title == "") {
            Redirect::autolink(URLROOT."/adminnews/edit", Lang::T("ERR_NEWS_TITLE_CAN_NOT_BE_EMPTY"));
        }
        DB::run("UPDATE news SET body=?, title=? WHERE id=?", [$body, $title, $newsid]);
        Redirect::autolink(URLROOT . "/adminnews", Lang::T("CP_NEWS_ITEM_WAS_EDITED_SUCCESS"));

    }

    public function newsdelete()
    {
        $newsid = (int) $_GET["newsid"];
        if (!Validate::Id($newsid)) {
            Redirect::autolink(URLROOT."/adminnews", sprintf(Lang::T("CP_NEWS_INVAILD_ITEM_ID").$newsid));
        }
        DB::run("DELETE FROM news WHERE id=?", [$newsid]);
        DB::run("DELETE FROM comments WHERE news =?", [$newsid]);
        Redirect::autolink(URLROOT . "/adminnews", Lang::T("CP_NEWS_ITEM_DEL_SUCCESS"));
    }

}