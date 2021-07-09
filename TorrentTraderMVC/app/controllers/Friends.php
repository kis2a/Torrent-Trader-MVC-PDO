<?php
class Friends extends Controller
{

    public function __construct()
    {
        $this->session = Auth::user(0, 2);
    }

    public function index()
    {
        $userid = (int) Input::get('id');
        if (!Validate::Id($userid)) {
            Redirect::autolink(URLROOT, "Invalid ID $userid.");
        }
        if ($this->session["view_users"] == "no" && $this->session["id"] != $userid) {
            Redirect::autolink(URLROOT, Lang::T("NO_USER_VIEW"));
        }
        $res = DB::run("SELECT * FROM users WHERE id=$userid");
        $user = $res->fetch(PDO::FETCH_ASSOC);
        $enemy = DB::run("SELECT f.friendid as id, u.username AS name, u.class, u.avatar, u.title, u.enabled, u.last_access FROM friends AS f LEFT JOIN users as u ON f.friendid = u.id WHERE userid=$user[id] AND friend='enemy' ORDER BY name");
        $friend = DB::run("SELECT f.friendid as id, u.username AS name, u.class, u.avatar, u.title, u.enabled, u.last_access FROM friends AS f LEFT JOIN users as u ON f.friendid = u.id WHERE userid=$user[id] AND friend='friend' ORDER BY name");
        
        // Template
        $data = [
            'title' => 'Friend Lists',
            'sql' => $user,
            'username' => $user['username'],
            'userid' => $userid,
            'friend' => $friend,
            'enemy' => $enemy,
        ];
        View::render('friends/index', $data, 'user');
    }

    public function add()
    {
        $targetid = (int) $_GET['targetid'];
        $type = $_GET['type'];
        if (!Validate::Id($targetid)) {
                Redirect::autolink(URLROOT, "Invalid ID $$targetid.");
        }
        if ($type == 'friend') {
            $r = DB::run("SELECT id FROM friends WHERE userid=$_SESSION[id] AND userid=$targetid");
            if ($r->rowCount() == 1) {
                    Redirect::autolink(URLROOT . "/friends?id=$_SESSION[id]", "User ID $targetid is already in your friends list.");
            }
            DB::run("INSERT INTO friends (id, userid, friendid, friend) VALUES (0,$_SESSION[id], $targetid, 'friend')");
            Redirect::to(URLROOT . "/friends?id=$_SESSION[id]");
            die();
        } elseif ($type == 'block') {
            $r = DB::run("SELECT id FROM friends WHERE userid=$_SESSION[id] AND userid=$targetid");
            if ($r->rowCount() == 1) {
                    Redirect::autolink(URLROOT . "/friends?id=$_SESSION[id]", "User ID $targetid is already in your friends list.");
            }
            DB::run("INSERT INTO friends (id, userid, friendid, friend) VALUES (0,$_SESSION[id], $targetid, 'enemy')");
                Redirect::autolink(URLROOT . "/friends?id=$_SESSION[id]", "Success");
            die();
        } else {
                Redirect::autolink(URLROOT . "/friends?id=$_SESSION[id]", "Unknown type $type");
        }
    }

    public function delete()
    {
        $targetid = (int) $_GET['targetid'];
        $sure = htmlentities($_GET['sure']);
        $type = htmlentities($_GET['type']);
        if ($type != "block") {$typ = "friend from list";} else { $typ = "blocked user from list";}
        if (!Validate::Id($targetid)) {
                Redirect::autolink(URLROOT . "/friends?id=$_SESSION[id]", "Invalid ID $_SESSION[id].");
        }
        if (!$sure) {
            $msg = "<div style='margin-top:10px; margin-bottom:10px' align='center'>Do you really want to delete this $typ? &nbsp; \n" . "<a href=?id=$_SESSION[id]/delete&type=$type&targetid=$targetid&sure=1>Yes</a> | <a href=friends.php>No</a></div>";
                Redirect::autolink(URLROOT."/profile?id=$targetid", $msg);
        }
        if ($type == 'friend') {
            $stmt = DB::run("DELETE FROM friends WHERE userid=$_SESSION[id] AND friendid=$targetid AND friend=friend");
            if ($stmt->rowCount() == 0) {
                    Redirect::autolink(URLROOT."/profile?id=$targetid", "No friend found with ID $targetid");
            }
            $frag = "friends";
        } elseif ($type == 'block') {
            $stmt = DB::run("DELETE FROM friends WHERE userid=$_SESSION[id] AND friendid=$targetid AND friend=?", ['enemy']);
            if ($stmt->rowCount() == 0) {
                    Redirect::autolink(URLROOT."/profile?id=$targetid", "No block found with ID $targetid");
            }
            $frag = "blocked";
        } else {
                Redirect::autolink(URLROOT."/profile?id=$targetid", "Unknown type $type");
        }
            Redirect::autolink(URLROOT . "/friends?id=$_SESSION[id]#$frag", "Success");
        die;
    }
}