<?php
class Comments extends Controller
{
    public function __construct()
    {
        Auth::user();
        // $this->userModel = $this->model('User');
        $this->logsModel = $this->model('Logs');
    }

    public function index()
    {
        $id = (int) ($_GET["id"] ?? 0);
        $type = $_GET["type"];
        if (!isset($id) || !$id || ($type != "torrent" && $type != "news" && $type != "req")) {
            Session::flash('warning', Lang::T("ERROR"), URLROOT."/home");
        }
        //NEWS
        if ($type == "news") {
            $res = DB::run("SELECT * FROM news WHERE id =?", [$id]);
            $row = $res->fetch(PDO::FETCH_LAZY);
            if (!$row) {
                Session::flash('warning', "News id invalid", URLROOT."/comments?type=news&id=$id");
            }
            Style::header(Lang::T("COMMENTS"));
            Style::begin(Lang::T("NEWS"));
            echo htmlspecialchars($row['title']) . "<br /><br />" . format_comment($row['body']) . "<br />";
            Style::end();
        }

        $title = Lang::T("COMMENTS");
        Style::header(Lang::T("COMMENTS"));
        Style::begin($title);
        //TORRENT
        
        if ($type == "torrent") {
            torrentmenu($id);
            $res = DB::run("SELECT id, name FROM torrents WHERE id =?", [$id]);
            $row = $res->fetch(PDO::FETCH_LAZY);
            if (!$row) {
                Session::flash('warning', "News id invalid", URLROOT."/home");
            }
            $title = Lang::T("COMMENTSFOR") . "<a href='torrent?id=" . $row['id'] . "'>" . htmlspecialchars($row['name']) . "</a>";
        }

        if ($type == "req") {
            $res = DB::run("SELECT * FROM comments WHERE req =?", [$id]);
            $row = $res->fetch(PDO::FETCH_LAZY);
            if (!$row) {
                Session::flash('warning', "Request id invalid", URLROOT."/home");
            }
            $title = Lang::T("COMMENTSFOR") . "<a href='".URLROOT."/request'>" . htmlspecialchars($row['name']) . "</a>";
        }

        echo "<center><a href='".URLROOT."/comments/add?type=$type&amp;id=$id'><b>Add Comment</b></a></center><br>";

        $commcount = DB::run("SELECT COUNT(*) FROM comments WHERE $type =?", [$id])->fetchColumn();
        if ($commcount) {
            list($pagertop, $pagerbottom, $limit) = pager(10, $commcount, "comments?id=$id&amp;type=$type");
            $commres = DB::run("SELECT comments.id, text, user, comments.added, avatar, signature, username, title, class, uploaded, downloaded, privacy, donated FROM comments LEFT JOIN users ON comments.user = users.id WHERE $type = $id ORDER BY comments.id $limit");
        } else {
            unset($commres);
        }
        if ($commcount) {
            commenttable($commres, $type);
            print($pagerbottom);
        } else {
            print("<br><b>" . Lang::T("NOCOMMENTS") . "</b><br>\n");
        }
        
        echo "<div class='form-group'>";
        echo "<form name='comment' method='post' action=\"comments/take?type=$type&amp;id=$id\">";
        echo textbbcode("comment", "body") . "<br>";
        echo "<center><input type=\"submit\"  value=\"" . Lang::T("ADDCOMMENT") . "\" /></center>";
        echo "</form></div>";
        Style::end();
        Style::footer();
    }

    public function add()
    {
        $id = (int) ($_GET["id"] ?? 0);
        $type = $_GET["type"];
        if (!isset($id) || !$id || ($type != "torrent" && $type != "news" && $type != "req")) {
            Session::flash('warning', Lang::T("ERROR"), URLROOT."/home");
        }$data = [
            'title' => 'Add Comment',
            'id' => $id,
            'type' => $type,
        ];
        $this->view('comments/add', $data, 'user');
    }

    public function edit()
    {
        $id = (int) ($_GET["id"] ?? 0);
        $type = $_GET["type"];
        $edit = (int) ($_GET["edit"] ?? 0);
        if (!isset($id) || !$id || ($type != "torrent" && $type != "news" && $type != "req")) {
            Session::flash('warning', Lang::T("ERROR"), URLROOT."/home");
        }
        $row = DB::run("SELECT user FROM comments WHERE id=?", [$id])->fetch();
        if (($type == "torrent" && $_SESSION["edit_torrents"] == "no" || $type == "news" && $_SESSION["edit_news"] == "no") && $_SESSION['id'] != $row['user'] || $type == "req" && $_SESSION['id'] != $row['user']) {
            Session::flash('warning', Lang::T("ERR_YOU_CANT_DO_THIS"), URLROOT."/home");
        }
        $save = (int) $_GET["save"];
        if ($save) {
            $text = $_POST['text'];
            $result = DB::run("UPDATE comments SET text=? WHERE id=?", [$text, $id]);
            Logs::write(Users::coloredname($_SESSION['username']) . " has edited comment: ID:$id");
            Session::flash('warning', "Comment Edited OK", URLROOT."/home");
        }
        $arr = DB::run("SELECT * FROM comments WHERE id=?", [$id])->fetch();

        $data = [
            'title' => 'Edit Comment',
            'text' => $arr["text"],
            'id' => $id,
            'type' => $type,
        ];
        $this->view('comments/index', $data, 'user');
        die();
    }

    public function delete()
    {
        $id = (int) ($_GET["id"] ?? 0);
        $type = $_GET["type"];
        if ($_SESSION["delete_news"] == "no" && $type == "news" || $_SESSION["delete_torrents"] == "no" && $type == "torrent") {
            Session::flash('warning', Lang::T("ERR_YOU_CANT_DO_THIS"), URLROOT."/home");
        }
        if ($type == "torrent") {
            $res = DB::run("SELECT torrent FROM comments WHERE id=?", [$id]);
            $row = $res->fetch(PDO::FETCH_ASSOC);
            if ($row["torrent"] > 0) {
                DB::run("UPDATE torrents SET comments = comments - 1 WHERE id = $row[torrent]");
            }
        }
        DB::run("DELETE FROM comments WHERE id =?", [$id]);
        Logs::write(Users::coloredname($_SESSION['username']) . " has deleted comment: ID: $id");
        Session::flash('warning', "Comment deleted OK", URLROOT."/home");
    }

    public function take()
    {
        $id = (int) ($_GET["id"] ?? 0);
        $type = $_GET["type"];
        $body = $_POST['body'];
        if (!$body) {
            Session::set('message', Lang::T("YOU_DID_NOT_ENTER_ANYTHING"));
            Redirect::to(URLROOT . "/comments?type=$type&id=$id");
        }
        if ($type == "torrent") {
            DB::run("UPDATE torrents SET comments = comments + 1 WHERE id = $id");
        }
        $ins = DB::run("INSERT INTO comments (user, " . $type . ", added, text) VALUES (?, ?, ?, ?)", [$_SESSION["id"], $id, TimeDate::get_date_time(), $body]);
        if ($ins) {
            Session::set('message', "Your Comment was added successfully.",URLROOT."/home" );
            Redirect::to(URLROOT . "/comments?type=$type&id=$id");
        } else {
            Session::set('message', Lang::T("UNABLE_TO_ADD_COMMENT"));
            Redirect::to(URLROOT . "/comments?type=$type&id=$id");
        }
    }

    public function user()
    {
        $id = (int) ($_GET["id"] ?? 0);
        if (!isset($id) || !$id) {
            Session::flash('warning', Lang::T("ERROR"), URLROOT."/home");
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
            Session::flash('warning', "User id invalid", URLROOT."/home");
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