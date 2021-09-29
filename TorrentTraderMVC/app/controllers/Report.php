<?php
class Report
{

    public function __construct()
    {
        $this->session = Auth::user(0, 2);
    }

    public function index()
    {
        Redirect::autolink(URLROOT, Lang::T("NO_ID"));
    }

    public function user()
    {
        $takeuser = (int) Input::get("user");
        $takereason = Input::get("reason");
        $user = (int) Input::get("id");

        if ($_SESSION["view_users"] == "no") {
            Redirect::autolink(URLROOT, Lang::T("NO_USER_VIEW"));
        }

        if ($takeuser) {
            if (empty($takereason)) {
                Redirect::autolink(URLROOT . "/report/user?user=$user", Lang::T("YOU_MUST_ENTER_A_REASON"));
            }
            $res = Reports::selectReport($_SESSION['id'], $takeuser, 'user');
            if ($res->rowCount() == 0) {
                Reports::insertReport($_SESSION['id'], $takeuser, 'user', $takereason);
                Redirect::autolink(URLROOT . "/profile?id=$user", "User: $takeuser, Reason: " . htmlspecialchars($takereason) . "<p>Successfully Reported</p>");
            } else {
                Redirect::autolink(URLROOT . "/profile?id=$user", Lang::T("YOU_HAVE_ALREADY_REPORTED") . " user $takeuser");
            }
        }

        if ($user != "") {
            $res = DB::run("SELECT username, class FROM users WHERE id=?", [$user]);
            if ($res->rowCount() == 0) {
                Redirect::autolink(URLROOT, Lang::T("INVALID_USERID"));
            }
            $arr = $res->fetch(PDO::FETCH_ASSOC);
            $title = 'Report';
            // Template
            $data = [
                'title' => $title,
                'username' => $arr['username'],
                'user' => $user,
            ];
            View::render('report/user', $data, 'user');
            die();
        } else {
            Redirect::autolink(URLROOT . "/profile?id=$user", Lang::T("MISSING_INFO"));
        }
    }

    public function torrent()
    {
        $taketorrent = (int) Input::get("torrent");
        $takereason = Input::get("reason");
        $torrent = (int) Input::get("torrent");

        if (Users::has("view_torrents") == "no") {
            Redirect::autolink(URLROOT, Lang::T("NO_TORRENT_VIEW"));
        }

        if (($taketorrent != "") && ($takereason != "")) {
            if (!$takereason) {
                Redirect::autolink(URLROOT . "/report/torrent?torrent=$torrent", Lang::T("YOU_MUST_ENTER_A_REASON"));
            }
            $res = Reports::selectReport($_SESSION['id'], $taketorrent, 'torrent');
            if ($res->rowCount() == 0) {
                Reports::insertReport($_SESSION['id'], $taketorrent, 'torrent', $takereason);
                Redirect::autolink(URLROOT . "/torrent?id=$torrent", "Torrent with id: $taketorrent, Reason for report: " . htmlspecialchars($takereason) . "<p>Successfully Reported</p>");
            } else {
                Redirect::autolink(URLROOT . "/torrent?id=$torrent", Lang::T("YOU_HAVE_ALREADY_REPORTED") . " torrent $taketorrent");
            }
        }

        if ($torrent != "") {
            $res = DB::run("SELECT name FROM torrents WHERE id=?", [$torrent]);
            if ($res->rowCount() == 0) {
                Redirect::autolink(URLROOT . "/torrent?id=$torrent", 'Invalid TorrentID');
            }
            $arr = $res->fetch(PDO::FETCH_LAZY);
            $title = 'Report';
            // Template
            $data = [
                'title' => $title,
                'name' => $arr['name'],
                'torrent' => $torrent,
            ];
            View::render('report/torrent', $data, 'user');
            die();
        } else {
            Redirect::autolink(URLROOT . "/torrent?id=$torrent", Lang::T("MISSING_INFO"));
        }
    }

    public function comment()
    {
        // Get Inputs
        $takecomment = (int) Input::get("comment");
        $takereason = Input::get("reason");
        $type = Input::get("type");
        // Check Type
        if ($type == "req") {
            $whattype = 'req';
        } else {
            $whattype = 'comment';
        }
        // Validate Input/User Checks
        if (($takecomment != "") && ($takereason != "")) {
            if (!$takereason) {
                Redirect::autolink(URLROOT . "/report/comment?comment=$takecomment", Lang::T("YOU_MUST_ENTER_A_REASON"));
            }
            $res = Reports::selectReport($_SESSION['id'], $takecomment, $whattype);
            if ($res->rowCount() == 0) {
                Reports::insertReport($_SESSION['id'], $takecomment, $whattype, $takereason);
                Redirect::autolink(URLROOT, "Comment with id: $takecomment, Reason for report: " . htmlspecialchars($takereason) . "<p>Successfully Reported</p>");
            } else {
                Redirect::autolink(URLROOT, Lang::T("YOU_HAVE_ALREADY_REPORTED") . " torrent $takecomment");
            }
        }
        // No Input So Show Form
        if ($takecomment != "") {
            $res = DB::run("SELECT id, text FROM comments WHERE id=?", [$takecomment]);
            if ($res->rowCount() == 0) {
                Redirect::autolink(URLROOT, "Invalid Comment");
                die();
            }
            $arr = $res->fetch(PDO::FETCH_LAZY);
            $data = [
                'type' => $type,
                'title' => 'Report',
                'text' => $arr['text'],
                'comment' => $takecomment,
            ];
            View::render('report/comment', $data, 'user');
            die();
        } else {
            Redirect::autolink(URLROOT, Lang::T("MISSING_INFO"));
        }
    }

    public function forum()
    {
        $takeforumid = (int) Input::get("forumid");
        $takeforumpost = (int) Input::get("forumpost");
        $takereason = Input::get("reason");
        $forumid = (int) Input::get("forumid");
        $forumpost = (int) Input::get("forumpost");

        if ($_SESSION["forumbanned"] == "yes" || $_SESSION["view_forum"] == "no") {
            Redirect::autolink(URLROOT, Lang::T("FORUM_BANNED"));
        }
        
        if (($takeforumid != "") && ($takereason != "")) {
            if (!$takereason) {
                Redirect::autolink(URLROOT, Lang::T("YOU_MUST_ENTER_A_REASON"));
            }
            $res = Reports::selectForumReport($_SESSION['id'], $takeforumid, $takeforumpost, 'forum');
            if ($res->rowCount() == 0) {
                Reports::insertReport($_SESSION['id'], $takeforumid, 'forum', $takereason, $takeforumpost);
                Redirect::autolink(URLROOT, "User: $_SESSION[username], Reason: " . htmlspecialchars($takereason) . "<p>Successfully Reported</p>");
            } else {
                Redirect::autolink(URLROOT, Lang::T("YOU_HAVE_ALREADY_REPORTED") . " post $takeforumid");
            }
        }

        if (($forumid != "") && ($forumpost != "")) {
            $res = DB::run("SELECT subject FROM forum_topics WHERE id=?", [$forumid]);
            if ($res->rowCount() == 0) {
                Redirect::autolink(URLROOT, "Invalid Forum ID");
            }
            $arr = $res->fetch(PDO::FETCH_LAZY);
            $title = 'Report';
            // Template
            $data = [
                'title' => $title,
                'subject' => $arr['subject'],
                'forumpost' => $forumpost,
                'forumid' => $forumid,
            ];
            View::render('report/forum', $data, 'user');
            die();
        }

        Redirect::autolink(URLROOT, Lang::T("MISSING_INFO"));
    }

}