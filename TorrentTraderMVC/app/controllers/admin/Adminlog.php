<?php
class Adminlog extends Controller
{

    public function __construct()
    {
        $this->session = Auth::user(_SUPERMODERATOR, 2);
    }

    public function index()
    {
        $search = trim($_GET['search']);
        if ($search != '') {
            $where = "WHERE txt LIKE " . sqlesc("%$search%") . "";
        }
        $res2 = DB::run("SELECT COUNT(*) FROM log $where");
        $row = $res2->fetch(PDO::FETCH_LAZY);
        $count = $row[0];
        $perpage = 50;
        list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, "/adminlog?");
        $rqq = "SELECT id, added, txt FROM log $where ORDER BY id DESC $limit";
        $res = DB::run($rqq);

        $data = [
            'title' => Lang::T("Site Log"),
            'pagerbottom' => $pagerbottom,
            'res' => $res,
        ];
        View::render('log/admin/sitelog', $data, 'admin');
    }

    public function delete() {
        if ($_POST['del']) {
            if ($_POST["delall"]) {
                DB::run("DELETE FROM `log`");
            } else {
                if (!@count($_POST["del"])) {
                    Redirect::autolink(URLROOT."/adminlog", Lang::T("NOTHING_SELECTED"));
                }
                $ids = array_map("intval", $_POST["del"]);
                $ids = implode(", ", $ids);
                DB::run("DELETE FROM `log` WHERE `id` IN ($ids)");
            }
            Redirect::autolink(URLROOT . "/adminlog", Lang::T("CP_DELETED_ENTRIES"));
            die;
        }
    }
}