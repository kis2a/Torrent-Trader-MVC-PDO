<?php
class Adminsnatched
{

    public function __construct()
    {
        $this->session = Auth::user(_MODERATOR, 2);
    }

    public function index()
    {
        if ($_POST['do'] == 'delete') {
            if (!@count($_POST['ids'])) {
                Redirect::autolink(URLROOT . "/adminsnatched", "Nothing Selected.");
            }
            $ids = array_map('intval', $_POST['ids']);
            $ids = implode(',', $ids);
            DB::run("UPDATE snatched SET ltime = '86400', hnr = 'no', done = 'yes' WHERE `sid` IN ($ids)");
            Redirect::autolink(URLROOT . "/Adminsnatched", "Entries deleted.");
        }
        if (HNR_ON) {
            $count = DB::run("SELECT count(*) FROM `snatched` where hnr='yes' ")->fetchColumn();
            $perpage = 50;
            list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, "Adminsnatched?");
            $sql = "SELECT *,s.tid FROM users u left join snatched s on s.uid=u.id  where hnr='yes' ORDER BY s.uid DESC $limit";
            $res = DB::run($sql);
            $data = [
                'title' => "List of Hit and Run",
                'count' => $count,
                'pagertop' => $pagertop,
                'pagerbottom' => $pagerbottom,
                'res' => $res,
            ];
            View::render('snatched/hitnrun', $data, 'admin');
        } else {
            Redirect::autolink(URLROOT, "Hit & Run Disabled in Config.php (mod in progress)");
        }
    }

}