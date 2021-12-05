<?php
class Catalog
{
    public function __construct()
    {
        $this->session = Auth::user(0, 2);
    }

    public function index()
    {
        $search = $_GET['search'] ?? '';
        $url = "?";
        if ($search != '') {
            $keys = explode(" ", $search);
            foreach ($keys as $k) {
                $ssa[] = " torrents.name LIKE '%$k%' ";
            }
            $query = '(' . implode(' OR ', $ssa) . ')';
            $url .= "search=" . urlencode($search);
        } else {
            $letter = $_GET["letter"] ?? 't';
            if (strlen($letter) > 1) {
                die;
            }
            if ($letter == "" || strpos("abcdefghijklmnopqrstuvwxyz", $letter) === false) {
                $letter = "t";
            }
            $query = "torrents.name LIKE '$letter%'";
            $url = "?letter=$letter";
        }

        $count = DB::run("SELECT count(id) FROM torrents WHERE $query AND visible != ?", ['no'])->fetchColumn();
        list($pagertop, $pagerbottom, $limit) = pager(28, $count, URLROOT . "/catalog$url&");
        $res = DB::run("SELECT torrents.anon, torrents.seeders, torrents.banned, torrents.leechers, torrents.info_hash, torrents.filename, torrents.nfo, torrents.last_action, torrents.numratings, torrents.name, torrents.owner, torrents.save_as, torrents.descr, torrents.visible, torrents.size, torrents.added, torrents.views, torrents.hits, torrents.times_completed, torrents.id, torrents.type, torrents.external, torrents.image1, torrents.image2, torrents.announce, torrents.numfiles, torrents.freeleech, 
        IF(torrents.numratings < 2, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, torrents.numratings,
         categories.name AS cat_name, 
         torrentlang.name AS lang_name, 
         torrentlang.image AS lang_image, 
         categories.parent_cat as cat_parent, 
         users.username, users.donated, users.warned, users.privacy, snatched.tid, snatched.uid, snatched.uload, snatched.dload, snatched.stime, snatched.utime, snatched.ltime
         FROM torrents 
         LEFT JOIN categories ON torrents.category = categories.id 
         LEFT JOIN torrentlang ON torrents.torrentlang = torrentlang.id 
         LEFT JOIN users ON torrents.owner = users.id 
         LEFT JOIN snatched ON users.id = snatched.uid
         WHERE $query AND visible != ? 
         ORDER BY name ASC $limit", ['no']);

        $data = [
            'title' => Lang::T("CATALOGUE"),
            'res' => $res,
            'count' => $count,
            'pagertop' => $pagertop,
        ];
        View::render('catalog/index', $data, 'user');
        
    }
}