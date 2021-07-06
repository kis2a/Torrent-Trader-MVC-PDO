<?php
class Adminavatar extends Controller
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
        $query = DB::run("SELECT count(*) FROM users WHERE enabled=? AND avatar !=?", ['yes', '']);
        $count = $query->fetch(PDO::FETCH_LAZY);
        $count = $count[0];
        list($pagertop, $pagerbottom, $limit) = pager(50, $count, '/adminavatar?');
        $query = "SELECT username, id, avatar FROM users WHERE enabled='yes' AND avatar !='' $limit";
        $res = DB::run($query);

        $title = "Avatar Log";
        $data = [
            'title' => $title,
            'pagertop' => $pagertop,
            'res' => $res,
            'pagerbottom' => $pagerbottom,
        ];
        $this->view('user/admin/avatar', $data, 'admin');
    }

}