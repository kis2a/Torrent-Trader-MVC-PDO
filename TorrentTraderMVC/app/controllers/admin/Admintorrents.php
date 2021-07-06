<?php
class Admintorrents extends Controller
{

    public function __construct()
    {
        $this->session = (new Auth)->user(_MODERATOR, 2);
        // $this->userModel = $this->model('User');
        $this->logsModel = $this->model('Logs');
        $this->valid = new Validation();
    }

    public function index()
    {
        if ($_POST["do"] == "delete") {
            if (!@count($_POST["torrentids"])) {
                Redirect::autolink(URLROOT . "/admintorrents", "Nothing selected click <a href='admintorrents'>here</a> to go back.");
            }
            foreach ($_POST["torrentids"] as $id) {
                deletetorrent(intval($id));
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
        $this->view('torrent/admin/torrentmanage', $data, 'admin');
    }

    public function free()
    {
        $search = trim($_GET['search']);
        if ($search != '') {
            $whereand = "AND name LIKE '%$search%";
        }
        $res2 = DB::run("SELECT COUNT(*) FROM torrents WHERE freeleech='1' $whereand");
        $row = $res2->fetch(PDO::FETCH_LAZY);
        $count = $row[0];
        $perpage = 50;
        list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, "/admintorrent/free?");

        $rqq = "SELECT id, name, seeders, leechers, visible, banned FROM torrents WHERE freeleech='1' $whereand ORDER BY name $limit";
        $resqq = DB::run($rqq);
        $data = [
            'title' => Lang::T("Free Leech"),
            'pagertop' => $pagertop,
            'resqq' => $resqq,
            'pagerbottom' => $pagerbottom,
        ];
        $this->view('torrent/admin/freetorrent', $data, 'admin');
    }
}