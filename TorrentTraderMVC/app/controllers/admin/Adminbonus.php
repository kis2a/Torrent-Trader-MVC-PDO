<?php
class Adminbonus extends Controller
{

    public function __construct()
    {
        $this->user = (new Auth)->user(_MODERATOR, 2);
        // $this->userModel = $this->model('User');
        $this->logsModel = $this->model('Logs');
        $this->valid = new Validation();
    }


    public function index()
    {
        if ($_POST['do'] == "del") {
            if (!@count($_POST["ids"])) {
                show_error_msg("Error", "select nothing.", 1);
            }
            $ids = array_map("intval", $_POST["ids"]);
            $ids = implode(", ", $ids);
            DB::run("DELETE FROM `bonus` WHERE `id` IN ($ids)");
            Redirect::autolink(URLROOT."/adminbonus", "deleted entries");
        }
        $count = get_row_count("bonus");
        list($pagertop, $pagerbottom, $limit) = pager(25, $count, 'adminbonus&amp;');
        $res = DB::run("SELECT id, title, cost, value, descr, type FROM `bonus` ORDER BY `type` $limit");

        $data = [
            'title' => Lang::T("Seedbonus Manager"),
            'count' => $count,
            'pagertop' => $pagertop,
            'pagerbottom' => $pagerbottom,
            'limit' => $limit,
			'res' => $res,
        ];
        $this->view('bonus/admin/seedbonus', $data, 'admin');
    }

    public function change()
    {
        $row = null;
        if ($this->valid->validId($_REQUEST['id'])) {
            $res = DB::run("SELECT id, title, cost, value, descr, type FROM `bonus` WHERE `id` = '$_REQUEST[id]'");
            $row = $res->fetch(PDO::FETCH_LAZY);
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (empty($_POST['title']) or empty($_POST['descr']) or empty($_POST['type']) or !is_numeric($_POST['cost'])) {
                Redirect::autolink($_SERVER['HTTP_REFERER'], "missing information.");
            }
            $_POST["value"] = ($_POST["type"] == "traffic") ? strtobytes($_POST["value"]) : (int) $_POST["value"];
            $var = array_map('sqlesc', $_POST);
            extract($var);
            if ($row == null) {
                DB::run("INSERT INTO `bonus` (`title`, `descr`, `cost`, `value`, `type`) VALUES ($title, $descr, $cost, $value, $type)");
            } else {
                DB::run("UPDATE `bonus` SET `title` = $title, `descr` = $descr, `cost` = $cost, `value` = $value, `type` = $type WHERE `id` = $id");
            }
            Redirect::autolink(URLROOT . "/adminbonus", "Updating the bonus seed.");
        }

        $data = [
            'title' => Lang::T("Seedbonus Manager"),
            'row' => $row,
        ];
        $this->view('bonus/admin/seedbonuschange', $data, 'admin');
    }
}