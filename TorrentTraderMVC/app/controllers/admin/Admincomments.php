<?php
class Admincomments extends Controller
{

    public function __construct()
    {
        Auth::user();
        Auth::isStaff();
        // $this->userModel = $this->model('User');
        $this->logsModel = $this->model('Logs');
        $this->valid = new Validation();
    }

    public function index()
    {
        $count = get_row_count("comments");
        list($pagertop, $pagerbottom, $limit) = pager(10, $count, URLROOT."/admincomments?");
        $res = DB::run("SELECT c.id, c.text, c.user, c.torrent, c.news, t.name, n.title, u.username, c.added 
        FROM comments c 
        LEFT JOIN torrents t ON c.torrent = t.id 
        LEFT JOIN news n ON c.news = n.id 
        LEFT JOIN users u ON c.user = u.id 
        ORDER BY c.added DESC $limit")->fetchAll(PDO::FETCH_OBJ);  
        $data = [
            'title' => Lang::T("TORRENT_CATEGORIES"),
            'res' => $res,
            'pagerbottom' => $pagerbottom,
            'count' => $count,
        ];
        $this->view('comments/admin/index', $data, 'admin');
    }

}