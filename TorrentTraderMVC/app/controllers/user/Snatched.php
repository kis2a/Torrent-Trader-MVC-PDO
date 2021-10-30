<?php
class Snatched
{

    public function __construct()
    {
        $this->session = Auth::user(0, 2);
    }

    public function user()
    {
        $uid = (int) $_GET['id'];

        if (($_SESSION["control_panel"] == "no") && $_SESSION["id"] != $uid) {
            Redirect::autolink(URLROOT, Lang::T("NO_PERMISSION"));
        }

        $count_uid = get_row_count('snatched', 'WHERE `uid` = \'' . $uid . '\'');
        list($pagertop, $pagerbottom, $limit) = pager(50, $count_uid, '/snatched?id=' . $uid . ' &amp;');
        $qry = "SELECT
				snatched.tid as tid,
				torrents.name,
				snatched.uload,
				snatched.dload,
				snatched.stime,
				snatched.utime,
				snatched.ltime,
				snatched.completed,
				snatched.hnr,
				(
					SELECT seeder
					FROM peers
					WHERE torrent = tid AND userid = $uid LIMIT 1
				) AS seeding
				FROM
				snatched
				INNER JOIN users ON snatched.uid = users.id
				INNER JOIN torrents ON snatched.tid = torrents.id
				WHERE
				users.status = 'confirmed' AND
				torrents.banned = 'no' AND snatched.uid = '$uid'
				ORDER BY stime DESC $limit";

        $res = DB::run($qry);

        if ($count_uid > 0) {
            $users = DB::run("SELECT `username` FROM `users` WHERE `id` = '$uid'")->fetchColumn();
            $title = "" . Lang::T("SNATCHLIST_FOR") . " " . htmlspecialchars($users) . "";

            $data = [
                'title' => $title,
                'res' => $res,
                'count_uid' => $count_uid,
                'uid' => $uid,
            ];
            View::render('snatched/user', $data, 'user');
        } else {
            Redirect::autolink(URLROOT, Lang::T("User Has No Snatched Torrents :)"));
        }

    }

    public function index()
    {
        $tid = (int) $_GET['tid'];

        if ($tid > 0) {
            $count_tid = get_row_count('snatched', 'WHERE `tid` = \'' . $tid . '\'');
        }

        $torrents = DB::run("SELECT `name` FROM `torrents` WHERE `id` = '$tid'")->fetchColumn();
        $title = "" . Lang::T("REGISTERED_MEMBERS_TO_TORRENT") . " " . htmlspecialchars($torrents) . "";
        $perpage = 50;
        list($pagertop, $pagerbottom, $limit) = pager($perpage, $count_tid, '/snatched?tid=' . $tid . ' &amp;');
        $qry = "SELECT
				users.id,
				users.username,
				users.class,
				snatched.uid as uid,
				snatched.tid as tid,
				snatched.uload,
				snatched.dload,
				snatched.stime,
				snatched.utime,
				snatched.ltime,
				snatched.completed,
				snatched.hnr,
				(
					SELECT seeder
					FROM peers
					WHERE torrent = tid AND userid = uid LIMIT 1
				) AS seeding
				FROM
				snatched
				INNER JOIN users ON snatched.uid = users.id
				INNER JOIN torrents ON snatched.tid = torrents.id
				WHERE
				users.status = 'confirmed' AND
				torrents.banned = 'no' AND snatched.tid = '$tid'
				ORDER BY stime DESC $limit";

        $res = DB::run($qry);

        if ($count_tid > 0) {

            $data = [
                'title' => $title,
                'res' => $res,
                'tid' => $tid,
            ];
            View::render('snatched/torrent', $data, 'user');

        } else {
            Redirect::autolink(URLROOT, Lang::T("Torrent Has No Snatched Users :)"));
        }

    }
}