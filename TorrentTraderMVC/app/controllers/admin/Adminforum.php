<?php
class Adminforum
{

    public function __construct()
    {
        $this->session = Auth::user(_MODERATOR, 2);
    }

    public function index()
    {
        $groupsres = Groups::getGroups();
        $data = [
            'title' => Lang::T("FORUM_MANAGEMENT"),
            'groupsres' => $groupsres,
        ];
        View::render('forum/index', $data, 'admin');
    }

    public function addcat()
    {
        $error_ac = "";
        $new_forumcat_name = $_POST["new_forumcat_name"];
        $new_forumcat_sort = $_POST["new_forumcat_sort"];
        if ($new_forumcat_name == "") {
            $error_ac .= "<li>" . Lang::T("CP_FORUM_CAT_NAME_WAS_EMPTY") . "</li>\n";
        }
        if ($new_forumcat_sort == "") {
            $error_ac .= "<li>" . Lang::T("CP_FORUM_CAT_SORT_WAS_EMPTY") . "</li>\n";
        }
        if ($error_ac == "") {
            $res = DB::run("INSERT INTO forumcats (`name`, `sort`) VALUES (?,?)", [$new_forumcat_name, intval($new_forumcat_sort)]);
            if ($res) {
                Redirect::autolink(URLROOT . "/adminforum", "Thank you, new forum cat added to db ...");
            } else {
                Redirect::autolink(URLROOT . "/adminforum", Lang::T("CP_COULD_NOT_SAVE_TO_DB"));
            }
        } else {
            Redirect::autolink(URLROOT . "/adminforum", $error_ac);
        }
    }

    public function delcat()
    {
        $id = (int) $_GET["id"];
        $t = DB::run("SELECT * FROM forumcats WHERE id = '$id'");
        $v = $t->fetch();
        if (!$v) {
            Redirect::autolink(URLROOT . "/adminforum", Lang::T("FORUM_INVALID_CAT"));
        }
        $data = [
            'title' => Lang::T("FORUM_MANAGEMENT"),
            'id' => $id,
            'catid' => $v['id'],
            'name' => $v['name'],
        ];
        View::render('forum/deletecat', $data, 'admin');
    }

    public function deleteforumcat()
    {
        DB::run("DELETE FROM forumcats WHERE id = $_POST[id]");
        $res = DB::run("SELECT id FROM forum_forums WHERE category = $_POST[id]");
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $res2 = DB::run("SELECT id FROM forum_topics WHERE forumid = $row[id]");
            while ($arr = $res2->fetch(PDO::FETCH_ASSOC)) {
                DB::run("DELETE FROM forum_posts WHERE topicid = $arr[id]");
                DB::run("DELETE FROM forum_readposts WHERE topicid = $arr[id]");
            }
            DB::run("DELETE FROM forum_topics WHERE forumid = $row[id]");
            DB::run("DELETE FROM forum_forums WHERE id = $row[id]");
        }
        Redirect::autolink(URLROOT . "/adminforum", Lang::T("CP_FORUM_CAT_DELETED"));
    }

    public function editcat()
    {
        $id = (int) $_GET["id"];
        $r = DB::run("SELECT * FROM forumcats WHERE id = '$id'")->fetch();
        if (!$r) {
            Redirect::autolink(URLROOT . "/adminforum", Lang::T("FORUM_INVALID_CAT"));
        }
        $data = [
            'title' => Lang::T("FORUM_MANAGEMENT"),
            'id' => $id,
            'sort' => $r['sort'],
            'name' => $r['name'],
        ];
        View::render('forum/editcat', $data, 'admin');
    }

    public function saveeditcat()
    {
        $id = (int) $_POST["id"];
        $changed_sortcat = (int) $_POST["changed_sortcat"];
        DB::run("UPDATE forumcats SET sort = ?, name = ? WHERE id=?", [$changed_sortcat, $_POST["changed_forumcat"], $id]);
        Redirect::autolink(URLROOT . "/adminforum", "<center><b>" . Lang::T("CP_UPDATE_COMPLETED") . "</b></center>");
    }

    public function addforum()
    {
        $error_ac = "";
        $new_forum_name = $_POST["new_forum_name"];
        $new_desc = $_POST["new_desc"];
        $new_forum_sort = (int) $_POST["new_forum_sort"];
        $new_forum_cat = (int) $_POST["new_forum_cat"];
        $minclassread = (int) $_POST["minclassread"];
        $minclasswrite = (int) $_POST["minclasswrite"];
        $guest_read = $_POST["guest_read"];

        $new_forum_forum = (int) $_POST["new_forum_forum"] ?? 0; // sub forum mod

        if ($new_forum_name == "") {
            $error_ac .= "<li>" . Lang::T("CP_FORUM_NAME_WAS_EMPTY") . "</li>\n";
        }
        if ($new_desc == "") {
            $error_ac .= "<li>" . Lang::T("CP_FORUM_DESC_WAS_EMPTY") . "</li>\n";
        }
        if ($new_forum_sort == "") {
            $error_ac .= "<li>" . Lang::T("CP_FORUM_SORT_ORDER_WAS_EMPTY") . "</li>\n";
        }
        if ($new_forum_cat == "") {
            $error_ac .= "<li>" . Lang::T("CP_FORUM_CATAGORY_WAS_EMPTY") . "</li>\n";
        }
        if ($error_ac == "") {
            $res = DB::run("INSERT INTO forum_forums (`name`, `description`, `sort`, `category`, `minclassread`, `minclasswrite`, `guest_read`, `sub`) VALUES (?,?,?,?,?,?,?,?)", [$new_forum_name, $new_desc, $new_forum_sort, $new_forum_cat, $minclassread, $minclasswrite, $guest_read, $new_forum_forum]);
            if ($res) {
                Redirect::autolink(URLROOT . "/adminforum", Lang::T("CP_FORUM_NEW_ADDED_TO_DB"));
            } else {
                Redirect::autolink(URLROOT . "/adminforum", Lang::T("CP_COULD_NOT_SAVE_TO_DB"));
            }
        } else {
            Redirect::autolink(URLROOT . "/adminforum", $error_ac);
        }
    }

    public function deleteforum()
    {
        $id = (int) $_GET["id"];
        $v = DB::run("SELECT * FROM forum_forums WHERE id = '$id'")->fetch();
        if (!$v) {
            Redirect::autolink(URLROOT . "/adminforum", Lang::T("FORUM_INVALID"));
        }
        $data = [
            'title' => Lang::T("FORUM_MANAGEMENT"),
            'id' => $id,
            'catid' => $v['sort'],
            'name' => $v['name'],
        ];
        View::render('forum/deleteforum', $data, 'admin');
    }

    public function deleteforumok()
    {
        DB::run("DELETE FROM forum_forums WHERE id = $_POST[id]");
        DB::run("DELETE FROM forum_topics WHERE forumid = $_POST[id]");
        DB::run("DELETE FROM forum_posts WHERE topicid = $_POST[id]");
        DB::run("DELETE FROM forum_readposts WHERE topicid = $_POST[id]");
        Redirect::autolink(URLROOT . "/adminforum", Lang::T("CP_FORUM_DELETED"));
    }

    public function editforum()
    {
        $id = (int) $_GET["id"];
        $q = DB::run("SELECT * FROM forum_forums WHERE id = '$id'");
        $r = $q->fetch();
        if (!$r) {
            Redirect::autolink(URLROOT . "/adminforum", Lang::T("FORUM_INVALID"));
        }
        $query = DB::query("SELECT * FROM forumcats ORDER BY sort, name");
        $data = [
            'title' => Lang::T("FORUM_MANAGEMENT"),
            'id' => $id,
            'sort' => $r['sort'],
            'name' => $r['name'],
            'sub' => $r['sub'],
            'description' => $r['description'],
            'guest_read' => $r['guest_read'],
            'query' => $query,
        ];
        View::render('forum/editforum', $data, 'admin');
    }

    public function saveeditforum()
    {
        $id = (int) $_POST["id"];
        $changed_sort = (int) $_POST["changed_sort"];
        $changed_forum = $_POST["changed_forum"];
        $changed_forum_desc = $_POST["changed_forum_desc"];
        $changed_forum_cat = (int) $_POST["changed_forum_cat"];
        $minclasswrite = (int) $_POST["minclasswrite"];
        $minclassread = (int) $_POST["minclassread"];
        $guest_read = $_POST["guest_read"];
        $changed_sub = (int) $_POST["changed_sub"];
        DB::run("UPDATE forum_forums SET sort =?, name =?, description =?, category =?, minclassread=?, minclasswrite=?, guest_read=?, sub=? WHERE id=?", [$changed_sort, $changed_forum, $changed_forum_desc, $changed_forum_cat, $minclassread, $minclasswrite, $guest_read, $changed_sub, $id]);
        Redirect::autolink(URLROOT . "/adminforum", "<center><b>" . Lang::T("CP_UPDATE_COMPLETED") . "</b></center>");
    }

}