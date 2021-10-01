<?php
class Adminlog
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
        $count = Logs::countWhere($where);
        list($pagertop, $pagerbottom, $limit) = pager(50, $count, URLROOT."/adminlog?");
        $res = Logs::getAll($where, $limit);

        $data = [
            'title' => Lang::T("Site Log"),
            'pagerbottom' => $pagerbottom,
            'res' => $res,
        ];
        View::render('log/index', $data, 'admin');
    }

    public function delete() {
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