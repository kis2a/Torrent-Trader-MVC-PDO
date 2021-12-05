<?php
class Bookmark
{

    public function __construct()
    {
        $this->session = Auth::user(0, 2, true);
    }

    public function index()
    {
        $count = DB::run("SELECT COUNT(*) FROM bookmarks WHERE userid = ? AND type =?", [$_SESSION["id"], 'torrent'])->fetchColumn();
        list($pagertop, $pagerbottom, $limit) = pager(25, $count, URLROOT."/bookmark?");
        $query = DB::run("SELECT bookmarks.id as bookmarkid,
                                 torrents.size,
                                 torrents.freeleech,
                                 torrents.external,
                                 torrents.id,
                                 torrents.category,
                                 torrents.name,
                                 torrents.filename,
                                 torrents.added,
                                 torrents.banned,
                                 torrents.comments,
                                 torrents.seeders,
                                 torrents.leechers,
                                 torrents.times_completed,
                                 categories.name AS cat_name,
                                 categories.parent_cat AS cat_parent,
                                 categories.image AS cat_pic
                                 FROM bookmarks
                                 LEFT JOIN torrents ON bookmarks.targetid = torrents.id
                                 LEFT JOIN categories ON category = categories.id
                                 WHERE bookmarks.userid = ?
                                 ORDER BY added DESC 
                                 $limit", [$_SESSION['id']]);

        if ($count == 0) {
            Redirect::autolink(URLROOT, "Your Bookmarks list is empty !");
        } else {
            $data = [
                'title' => 'My Bookmarks',
                'count' => $count,
                'pagertop' => $pagertop,
                'res' => $query,
                'configautoclean_interval' => floor(Config::TT()['ADDBONUS'] / 60),
                'usersid' => $_SESSION['id'],
            ];
            View::render('bookmark/index', $data, 'user');
        }

    }

    public function add($type = 'torrent')
    {
        $target = (int) $_GET['target'];
        if (!isset($target)) {
            Redirect::autolink(URLROOT, "No target selected...");
        }
        $arr = DB::run("SELECT COUNT(*) FROM bookmarks WHERE targetid = ? AND `type` = ? AND userid = ?", [$target, $type, $_SESSION['id']])->fetchColumn();
        if ($arr > 0) {
            Redirect::autolink(URLROOT, "Already bookmarked...");
        }

        if ($type === 'torrent') {
            if ((get_row_count("torrents", "WHERE id=$target")) > 0) {
                DB::run("INSERT INTO bookmarks (userid, targetid, type) VALUES (?,?,?)", [$_SESSION['id'], $target, 'torrent']);
                Redirect::autolink(URLROOT."/torrent?id=$target", "Torrent was successfully bookmarked.");
            }
        } else {
            // if type forum ???
        }
        Redirect::autolink(URLROOT, "ID not found");
    }

    public function delete($type = 'torrent')
    {
        $target = (int) $_GET['target'];
        $arr = DB::run("SELECT COUNT(*) FROM bookmarks WHERE targetid = ? AND `type` = ? AND userid = ?", [$target, $type, $_SESSION['id']])->fetchColumn();
        if (!$arr) {
            Redirect::autolink(URLROOT, "ID not found in your bookmarks list...");
        }
        DB::run("DELETE FROM bookmarks WHERE targetid = ? AND `type` = ? AND userid = ?", [$target, $type, $_SESSION['id']]);
        if ($type === 'torrent') {
            Redirect::autolink(URLROOT."/torrent?id=$target", "Book Mark Deleted...");
        } else {
            // redirect forum
        }
    }

}