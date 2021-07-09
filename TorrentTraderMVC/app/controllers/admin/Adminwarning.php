<?php
class Adminwarning extends Controller
{

    public function __construct()
    {
        $this->session = Auth::user(_MODERATOR, 2);
    }

    public function index()
    {
        $count = get_row_count("users", "WHERE enabled = 'yes' AND status = 'confirmed' AND warned = 'yes'");
        list($pagertop, $pagerbottom, $limit) = pager(25, $count, URLROOT.'/adminwarning?');
        $res = DB::run("SELECT `id`, `username`, `class`, `added`, `last_access` FROM `users` WHERE `enabled` = 'yes' AND `status` = 'confirmed' AND `warned` = 'yes' ORDER BY `added` DESC $limit");
        $title = "Warned Users";
        $data = [
            'title' => $title,
            'pagerbottom' => $pagerbottom,
            'count' => $count,
            'res' => $res,
        ];
        View::render('warning/admin/warned', $data, 'admin');
    }

    public function submit()
    {
        if ($_POST["removeall"]) {
            $res = DB::run("SELECT `id` FROM `users` WHERE `enabled` = 'yes' AND `status` = 'confirmed' AND `warned` = 'yes'");
            while ($row = $res->fetch(PDO::FETCH_LAZY)) {
                DB::run("DELETE FROM `warnings` WHERE `active` = 'yes' AND `userid` = '$row[id]'");
                DB::run("UPDATE `users` SET `warned` = 'no' WHERE `id` = '$row[id]'");
            }
        } else {
            if (!@count($_POST['warned'])) {
                Redirect::autolink(URLROOT . "/adminwarning", Lang::T("NOTHING_SELECTED"));
            }
            $ids = array_map("intval", $_POST["warned"]);
            $ids = implode(", ", $ids);
            DB::run("DELETE FROM `warnings` WHERE `active` = 'yes' AND `userid` IN ($ids)");
            DB::run("UPDATE `users` SET `warned` = 'no' WHERE `id` IN ($ids)");
        }
        Redirect::autolink(URLROOT . "/adminwarning", "Entries Confirmed");
    }
}
