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
                Redirect::autolink(URLROOT . "/comments?type=news&id=$id", "News id invalid");
            }
            $title = Lang::T("NEWS");
        }

        if ($type == "torrent") {
            $row = Torrents::getIdName($id);
            if (!$row) {
                Redirect::autolink(URLROOT, "Invalid Torrent");
            }
            $title = Lang::T("COMMENTSFOR") . "<a href='torrent?id=" . $row['id'] . "'>" . htmlspecialchars($row['name']) . "</a>";
        }

        if ($type == "req") {
            $row = Comment::selectByRequest($id);
            if (!$row) {
                Redirect::autolink(URLROOT, "Request id invalid");
            }
            $title = Lang::T("COMMENTSFOR") . "<a href='" . URLROOT . "/request'>" . htmlspecialchars($row['name']) . "</a>";
        }

        $pager = $this->commentPager($id, $type);
        // Template
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

    public function commentPager($id, $type)
    {
        $commcount = DB::run("SELECT COUNT(*) FROM comments WHERE $type =?", [$id])->fetchColumn();
        if ($commcount) {
            list($pagertop, $pagerbottom, $limit) = pager(10, $commcount, "comments?id=$id&amp;type=$type");
            $commres = DB::run("SELECT comments.id, text, user, comments.added, avatar, signature, username, title, class, uploaded, downloaded, privacy, donated FROM comments LEFT JOIN users ON comments.user = users.id WHERE $type = $id ORDER BY comments.id $limit");
        } else {
            unset($commres);
        }
        return $pager = [
            'pagertop' => $pagertop,
            'commres' => $commres,
            'pagerbottom' => $pagerbottom,
            'limit' => $limit,
            'commcount' => $commcount,
        ];
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
        $row = DB::run("SELECT user FROM comments WHERE id=?", [$id])->fetch();
        if (($type == "torrent" && $_SESSION["edit_torrents"] == "no" || $type == "news" && $_SESSION["edit_news"] == "no") && $_SESSION['id'] != $row['user'] || $type == "req" && $_SESSION['id'] != $row['user']) {
            Redirect::autolink(URLROOT, Lang::T("ERR_YOU_CANT_DO_THIS"));
        }
        $save = (int) $_GET["save"];
        if ($save) {
            $text = $_POST['text'];
            $result = DB::run("UPDATE comments SET text=? WHERE id=?", [$text, $id]);
            Logs::write(Users::coloredname($_SESSION['username']) . " has edited comment: ID:$id");
            Redirect::autolink(URLROOT, "Comment Edited OK");
        }
        $arr = Comment::selectAll($id);

        $data = [
            'title' => 'Edit Comment',
            'text' => $arr->text,
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
            $res = DB::run("SELECT torrent FROM comments WHERE id=?", [$id]);
            $row = $res->fetch(PDO::FETCH_ASSOC);
            if ($row["torrent"] > 0) {
                DB::run("UPDATE torrents SET comments = comments - 1 WHERE id = $row[torrent]");
            }
        }
        Comment::delete($id);
        Logs::write(Users::coloredname($_SESSION['username']) . " has deleted comment: ID: $id");
        Redirect::autolink(URLROOT, "Comment deleted OK");
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
            DB::run("UPDATE torrents SET comments = comments + 1 WHERE id = $id");
        }
        $ins = Comment::insert($type, $_SESSION["id"], $id, TimeDate::get_date_time(), $body);
        if ($ins) {
            Redirect::autolink(URLROOT . "/comments?type=$type&id=$id", "Your Comment was added successfully.");
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

        $res = DB::run("SELECT
            comments.id, text, user, comments.added, avatar,
            signature, username, title, class, uploaded, downloaded, privacy, donated
            FROM comments
            LEFT JOIN users
            ON comments.user = users.id
            WHERE user = $id ORDER BY comments.id "); //$limit
        $row = $res->fetch(PDO::FETCH_LAZY);
        if (!$row) {
            Redirect::autolink(URLROOT, "User id invalid");
        }

        $title = Lang::T("COMMENTSFOR") . "<a href='profile?id=" . $row['user'] . "'>&nbsp;$row[username]</a>";

        Style::header(Lang::T("COMMENTS"));
        Style::begin($title);
        $commcount = DB::run("SELECT COUNT(*) FROM comments WHERE user =? AND torrent = ?", [$id, 0])->fetchColumn();
        if ($commcount) {
            list($pagertop, $pagerbottom, $limit) = pager(10, $commcount, "comments?id=$id");
            $commres = DB::run("SELECT comments.id, text, user, comments.added, avatar, signature, username, title, class, uploaded, downloaded, privacy, donated FROM comments LEFT JOIN users ON comments.user = users.id WHERE user = $id ORDER BY comments.id"); // $limit
        } else {
            unset($commres);
        }
        if ($commcount) {
            print($pagertop);
            commenttable($commres, 'torrent');
            print($pagerbottom);
        } else {
            print("<br><b>" . Lang::T("NOCOMMENTS") . "</b><br>\n");
        }
        Style::end();
        Style::footer();
    }

}