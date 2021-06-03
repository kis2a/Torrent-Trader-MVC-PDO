<?php
class Adminbantorrent extends Controller
{

    public function __construct()
    {
        Auth::user(); // should check admin here
        // $this->userModel = $this->model('User');
        $this->logsModel = $this->model('Logs');
        $this->valid = new Validation();
    }

    public function index()
    {
        $res2 = DB::run("SELECT COUNT(*) FROM torrents WHERE banned=?", ['yes']);
        $row = $res2->fetch(PDO::FETCH_LAZY);
        $count = $row[0];
        $perpage = 50;
        list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, "/adminbantorrent?");

        $resqq = DB::run("SELECT id, name, seeders, leechers, visible, banned, external FROM torrents WHERE banned=? ORDER BY name", ['yes']);
        $title = "Banned " . Lang::T("TORRENT_MANAGEMENT");
        $data = [
            'title' => $title,
            'pagerbottom' => $pagerbottom,
            'count' => $count,
            'pagertop' => $pagertop,
            'resqq' => $resqq,
        ];
        $this->view('torrent/admin/bannedtorrents', $data, 'admin');
    }
}