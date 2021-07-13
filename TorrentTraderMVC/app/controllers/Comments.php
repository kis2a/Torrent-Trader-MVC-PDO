<?php
class Comments extends Controller
{
    public function __construct()
    {
        $this->session = Auth::user(0, 2);
    }

    public function index()
    {
        $id = (int) Input::get("id");
        $type = Input::get("type");
        if (!isset($id) || !$id || ($type != "torrent" && $type != "news" && $type != "req")) {
            Redirect::autolink(URLROOT, Lang::T("ERROR"));
        }

        if ($type == "news") {
            $row = News::selectAll($id);
            if (!$row) {
                Redirect::autolink(URLROOT . "/comments?type=news&id=$id", Lang::T("INVALID_ID"));
            }
            $title = Lang::T("NEWS");
        }

        if ($type == "torrent") {
            $row = Torrents::getIdName($id);
            if (!$row) {
                Redirect::autolink(URLROOT, Lang::T("INVALID_ID"));
            }
            $title = Lang::T("COMMENTSFOR") . "<a href='torrent?id=" . $row['id'] . "'>" . htmlspecialchars($row['name']) . "</a>";
        }

        if ($type == "req") {
            $row = Comment::selectByRequest($id);
            if (!$row) {
                Redirect::autolink(URLROOT, Lang::T("INVALID_ID"));
            }
            $title = Lang::T("COMMENTSFOR") . "<a href='" . URLROOT . "/request'>" . htmlspecialchars($row['name']) . "</a>";
        }

        $pager = Comment::commentPager($id, $type);
        
        $data = [
            'title' => $title,
            'pagertop' => $pager['pagertop'],
            'commres' => $pager['commres'],
            'pagerbottom' => $pager['pagerbottom'],
            'limit' => $pager['limit'],
            'commcount' => $pager['commcount'],
            'row' => $row,
            'newsbody' => $row['body'],
            'newstitle' => $row['title'],
            'type' => $type,
            'id' => $id,
        ];
        View::render('comments/index', $data, 'user');
    }

    public function add()
    {
        $id = (int) Input::get("id");
        $type = Input::get("type");
        if (!isset($id) || !$id || ($type != "torrent" && $type != "news" && $type != "req")) {
            Redirect::autolink(URLROOT, Lang::T("ERROR"));
        }
        $data = [
            'title' => 'Add Comment',
            'id' => $id,
            'type' => $type,
        ];
        View::render('comments/add', $data, 'user');
    }

    public function edit()
    {
        $id = (int) Input::get("id");
        $type = Input::get("type");
        if (!isset($id) || !$id || ($type != "torrent" && $type != "news" && $type != "req")) {
            Redirect::autolink(URLROOT, Lang::T("ERROR"));
        }
        $arr = Comment::selectAll($id);
        if (($type == "torrent" && $_SESSION["edit_torrents"] == "no" || $type == "news" && $_SESSION["edit_news"] == "no") && $_SESSION['id'] != $arr['user'] || $type == "req" && $_SESSION['id'] != $arr['user']) {
            Redirect::autolink(URLROOT, Lang::T("ERR_YOU_CANT_DO_THIS"));
        }
        $save = (int) $_GET["save"];
        if ($save) {
            $text = $_POST['text'];
            Comment::updateText($text, $id);
            Logs::write(Users::coloredname($_SESSION['username']) . " has edited comment: ID:$id");
            Redirect::autolink(URLROOT, Lang::T("_SUCCESS_UPD_"));
        }

        $data = [
            'title' => 'Edit Comment',
            'text' => $arr['text'],
            'id' => $id,
            'type' => $type,
        ];
        View::render('comments/edit', $data, 'user');
        die();
    }

    public function delete()
    {
        $id = (int) Input::get("id");
        $type = Input::get("type");
        if ($_SESSION["delete_news"] == "no" && $type == "news" || $_SESSION["delete_torrents"] == "no" && $type == "torrent") {
            Redirect::autolink(URLROOT, Lang::T("ERR_YOU_CANT_DO_THIS"));
        }
        if ($type == "torrent") {
            $row = Comment::selectTorrent($id);
            if ($row["torrent"] > 0) {
                Torrents::updateComments($id, 'sub');
            }
        }
        Comment::delete($id);
        Logs::write(Users::coloredname($_SESSION['username']) . " has deleted comment: ID: $id");
        Redirect::autolink(URLROOT, Lang::T("_SUCCESS_DEL_"));
    }

    public function take()
    {
        $id = (int) Input::get("id");
        $type = Input::get("type");
        $body = Input::get('body');
        if (!$body) {
            Redirect::autolink(URLROOT . "/comments?type=$type&id=$id", Lang::T("YOU_DID_NOT_ENTER_ANYTHING"));
        }
        if ($type == "torrent") {
            Torrents::updateComments($id, 'add');
        }
        $ins = Comment::insert($type, $_SESSION["id"], $id, TimeDate::get_date_time(), $body);
        if ($ins) {
            Redirect::autolink(URLROOT . "/comments?type=$type&id=$id", Lang::T("_SUCCESS_ADD_"));
        } else {
            Redirect::autolink(URLROOT . "/comments?type=$type&id=$id", Lang::T("UNABLE_TO_ADD_COMMENT"));
        }
    }

    public function user()
    {
        $id = (int) Input::get("id");
        if (!isset($id) || !$id) {
            Redirect::autolink(URLROOT, Lang::T("ERROR"));
        }

        $row = Comment::selectCommentUser($id);
        if (!$row) {
            Redirect::autolink(URLROOT, Lang::T("INVALID_USERID"));
        }

        $title = Lang::T("COMMENTSFOR") . "<a href='profile?id=" . $row['user'] . "'>&nbsp;$row[username]</a>";

        $data = [
            'title' => $title,
            'id' => $id,
        ];
        View::render('comments/user', $data, 'user');
        die();
    }

}