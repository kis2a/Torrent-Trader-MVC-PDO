<?php
class Admingroups extends Controller
{

    public function __construct()
    {
        Auth::user(); // should check admin here
        // $this->userModel = $this->model('User');
        $this->logsModel = $this->model('Logs');
        $this->valid = new Validation();
    }

    public function index()
    {}


    public function groupsview()
    {
        $getlevel = DB::run("SELECT * from groups ORDER BY group_id");
        $title = Lang::T("GROUPS_MANAGEMENT");
        require APPROOT . '/views/admin/header.php';
        Style::adminnavmenu();
        $data = [
            'getlevel' => $getlevel,
        ];
        $this->view('groups/admin/view', $data);
        require APPROOT . '/views/admin/footer.php';
    }

    public function groupsedit()
    {
        var_dump($_GET);
        $group_id = intval($_GET["group_id"]);
        $rlevel = DB::run("SELECT * FROM groups WHERE group_id=?", [$group_id]);
        if (!$rlevel) {
            show_error_msg(Lang::T("ERROR"), Lang::T("CP_NO_GROUP_ID_FOUND"), 1);
        }

        $title = Lang::T("GROUPS_MANAGEMENT");
        require APPROOT . '/views/admin/header.php';
        Style::adminnavmenu();
        $data = [
            'rlevel' => $rlevel,
        ];
        $this->view('groups/admin/edit', $data);
        require APPROOT . '/views/admin/footer.php';
    }

    public function groupsupdate()
    {
        $title = Lang::T("GROUPS_MANAGEMENT");
        $update = array();
        $update[] = "level = " . sqlesc($_POST["gname"]);
        $update[] = "Color= " . sqlesc($_POST["gcolor"]);
        $update[] = "view_torrents = " . sqlesc($_POST["vtorrent"]);
        $update[] = "edit_torrents = " . sqlesc($_POST["etorrent"]);
        $update[] = "delete_torrents = " . sqlesc($_POST["dtorrent"]);
        $update[] = "view_users = " . sqlesc($_POST["vuser"]);
        $update[] = "edit_users = " . sqlesc($_POST["euser"]);
        $update[] = "delete_users = " . sqlesc($_POST["duser"]);
        $update[] = "view_news = " . sqlesc($_POST["vnews"]);
        $update[] = "edit_news = " . sqlesc($_POST["enews"]);
        $update[] = "delete_news = " . sqlesc($_POST["dnews"]);
        $update[] = "view_forum = " . sqlesc($_POST["vforum"]);
        $update[] = "edit_forum = " . sqlesc($_POST["eforum"]);
        $update[] = "delete_forum = " . sqlesc($_POST["dforum"]);
        $update[] = "can_upload = " . sqlesc($_POST["upload"]);
        $update[] = "can_download = " . sqlesc($_POST["down"]);
        $update[] = "maxslots= ' " . $_POST["downslots"] . " ' "; // TODO
        $update[] = "control_panel = " . sqlesc($_POST["admincp"]);
        $update[] = "staff_page = " . sqlesc($_POST["staffpage"]);
        $update[] = "staff_public = " . sqlesc($_POST["staffpublic"]);
        $update[] = "staff_sort = " . intval($_POST['sort']);
        $strupdate = implode(",", $update);
        $group_id = intval($_GET["group_id"]);
        DB::run("UPDATE groups SET $strupdate WHERE group_id=?", [$group_id]);
        Redirect::autolink(URLROOT . "/admingroups/groupsview", Lang::T("SUCCESS"), "Groups Updated!");
        require APPROOT . '/views/admin/footer.php';
    }

    public function groupsdelete()
    {
        //Needs to be secured!!!!
        $group_id = intval($_GET["group_id"]);
        if (($group_id == "1") || ($group_id == "7")) {
            show_error_msg(Lang::T("ERROR"), Lang::T("CP_YOU_CANT_DEL_THIS_GRP"), 1);
        }
        DB::run("DELETE FROM groups WHERE group_id=?", [$group_id]);
        Redirect::autolink(URLROOT . "/admingroups/groupsview", Lang::T("CP_DEL_OK"));
    }

    public function groupsadd()
    {
        $rlevel = DB::run("SELECT DISTINCT group_id, level FROM groups ORDER BY group_id");

        $title = Lang::T("GROUPS_MANAGEMENT");
        require APPROOT . '/views/admin/header.php';
        Style::adminnavmenu();
        $data = [
            'rlevel' => $rlevel,
        ];
        $this->view('groups/admin/add', $data);
        require APPROOT . '/views/admin/footer.php';
    }

    public function groupsaddnew()
    {
        $gname = $_POST["gname"];
        $gcolor = $_POST["gcolor"];
        $group_id = $_POST["getlevel"];
        $rlevel = DB::run("SELECT * FROM groups WHERE group_id=?", [$group_id]);
        $level = $rlevel->fetch(PDO::FETCH_ASSOC);
        if (!$level) {
            show_error_msg(Lang::T("ERROR"), Lang::T("CP_INVALID_ID"), 1);
        }
        $test = DB::run("INSERT INTO groups
  (level, color, view_torrents, edit_torrents, delete_torrents, view_users, edit_users, delete_users,
	view_news, edit_news, delete_news, view_forum, edit_forum, delete_forum, can_upload, can_download,
	control_panel, staff_page, staff_public, staff_sort, maxslots)
VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
            [$gname, $gcolor, $level['view_torrents'], $level["edit_torrents"], $level["delete_torrents"], $level["view_users"],
                $level["edit_users"], $level["delete_users"], $level["view_news"], $level["edit_news"], $level["delete_news"],
                $level["edit_forum"], $level["edit_forum"], $level["delete_forum"], $level["can_upload"], $level["can_download"], $level["control_panel"],
                $level["staff_page"], $level["staff_public"], $level["staff_sort"], $level["maxslots"]]);
        Redirect::autolink(URLROOT . "/admingroups/groupsview", Lang::T("SUCCESS"), "Groups Updated!");
        require APPROOT . '/views/admin/footer.php';
    }
}