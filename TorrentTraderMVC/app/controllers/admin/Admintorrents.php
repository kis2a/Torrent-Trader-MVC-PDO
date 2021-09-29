<?php
class Admintorrents
{

    public function __construct()
    {
        $this->session = Auth::user(_MODERATOR, 2);
    }

    public function index()
    {
        if ($_POST["do"] == "delete") {
            if (!@count($_POST["torrentids"])) {
                Redirect::autolink(URLROOT . "/admintorrents", "Nothing selected click <a href='admintorrents'>here</a> to go back.");
            }
            foreach ($_POST["torrentids"] as $id) {
                Torrents::deletetorrent(intval($id));
                Logs::write("Torrent ID $id was deleted by $_SESSION[username]");
            }
            Redirect::autolink(URLROOT . "/admintorrents", "Go <a href='admintorrents'>back</a>?");
        }
        $search = (!empty($_GET["search"])) ? htmlspecialchars(trim($_GET["search"])) : "";
        $where = ($search == "") ? "" : "WHERE name LIKE " . sqlesc("%$search%") . "";
        $count = get_row_count("torrents", $where);
        list($pagertop, $pagerbottom, $limit) = pager(25, $count, "admintorrents&amp;");
        $res = DB::run("SELECT id, name, seeders, leechers, visible, banned, external FROM torrents $where ORDER BY name $limit");

        $data = [
            'title' => Lang::T("Torrent Management"),
            'count' => $count,
            'pagerbottom' => $pagerbottom,
            'res' => $res,
            'search' => $search,
        ];
        View::render('torrent/admin/torrentmanage', $data, 'admin');
    }

    public function free()
    {
        $search = trim($_GET['search'] ?? '');
        if ($search != '') {
            $whereand = "AND name LIKE '%$search%";
        }
        $count = DB::run("SELECT COUNT(*) FROM torrents WHERE freeleech='1' $whereand")->fetchColumn();
        list($pagertop, $pagerbottom, $limit) = pager(40, $count, "/admintorrent/free?");

        $resqq = DB::run("SELECT id, name, seeders, leechers, visible, banned FROM torrents WHERE freeleech='1' $whereand ORDER BY name $limit");
        $data = [
            'title' => Lang::T("Free Leech"),
            'pagertop' => $pagertop,
            'resqq' => $resqq,
            'pagerbottom' => $pagerbottom,
        ];
        View::render('torrent/admin/freetorrent', $data, 'admin');
    }
    
    
    // dead torrents
    public function dead()
    {
        if (Users::has("control_panel") != "yes") {
            Redirect::autolink(URLROOT, Lang::T("SORRY_NO_RIGHTS_TO_ACCESS"));
        }
        $page = (int) $_GET["page"];
        $perpage = 50;
        $res2 = DB::run("SELECT COUNT(*) FROM torrents WHERE banned = 'no' AND seeders < 1");
        $row2 = $res2->fetch(PDO::FETCH_LAZY);
        $count = $row2[0];
        list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, URLROOT . "/admintorrents/dead&amp;");
        $res = DB::run("SELECT torrents.id, torrents.name, torrents.owner, torrents.external, torrents.size, torrents.seeders, torrents.leechers, torrents.times_completed, torrents.added, torrents.last_action, users.username FROM torrents LEFT JOIN users ON torrents.owner = users.id WHERE torrents.banned = 'no' AND torrents.seeders < 1 ORDER BY torrents.added DESC $limit");

        if ($_POST["do"] == "delete") {
            if (!@count($_POST["torrentids"])) {
                Redirect::autolink(URLROOT . "/admintorrents/dead", "You must select at least one torrent.");
            }
            foreach ($_POST["torrentids"] as $id) {
                Torrents::deletetorrent(intval($id));
                Logs::write("<a href=" . URLROOT . "/profile?id=$_SESSION[id]><b>$_SESSION[username]</b></a>deleted the torrent ID : [<b>$id</b>]of the page: <i><b>Dead Torrents </b></i>");
            }
            Redirect::autolink(URLROOT . "/admintorrents/dead", "The selected torrent has been successfully deleted.");
        }

        if ($count < 1) {
            Redirect::autolink(URLROOT, "No Dead Torrents !");
        }
        $title = "The Dead Torrents";
        $data = [
            'res' => $res,
            'title' => $title,
            'count' => $count,
            'perpage' => $perpage,
            'pagertop' => $pagertop,
            'pagerbottom' => $pagerbottom,
        ];
        View::render('torrent/admin/dead', $data, 'admin');
    }

}