<?php
class Adminsnatched extends Controller
{

    public function __construct()
    {
        $this->session = (new Auth)->user(_MODERATOR, 2); // should check admin here
        // $this->userModel = $this->model('User');
        $this->logsModel = $this->model('Logs');
        $this->valid = new Validation();
    }


    public function index()
    {
        if ($_POST['do'] == 'delete') {
            if (!@count($_POST['ids'])) {
                show_error_msg(Lang::T("ERROR"), "Nothing Selected.", 1);
            }
            $ids = array_map('intval', $_POST['ids']);
            $ids = implode(',', $ids);
            DB::run("UPDATE snatched SET ltime = '86400', hnr = 'no', done = 'yes' WHERE `sid` IN ($ids)");
            Redirect::autolink(URLROOT . "/Adminsnatched", "Entries deleted.");
        }
        if (HNR_ON) {
            $res = DB::run("SELECT * FROM `snatched` where hnr='yes' ");
            $row = $res->fetch(PDO::FETCH_ASSOC);
            $count = $row[0];
            $perpage = 50;
            list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, "Adminsnatched?");
            $sql = "SELECT *,s.tid FROM users u left join snatched s on s.uid=u.id  where hnr='yes' ORDER BY s.uid DESC $limit";
            $res = DB::run($sql);
            $title = "List of Hit and Run";

            require APPROOT . '/views/admin/header.php';
            Style::adminnavmenu();
            $data = [
                'count' => $count,
                'pagertop' => $pagertop,
                'pagerbottom' => $pagerbottom,
                'res' => $res,
            ];
            $this->view('snatched/admin/hitnrun', $data);
            require APPROOT . '/views/admin/footer.php';
        } else {
            require APPROOT . '/views/admin/header.php';
            Style::adminnavmenu();
            Style::begin($data['title']);
            print '<b><center>Hit & Run Disabled in Config.php (mod in progress)</center></b>';
            Style::end();
            require APPROOT . '/views/admin/footer.php';
        }
    }

}