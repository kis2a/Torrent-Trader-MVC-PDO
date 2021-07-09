<?php
class Adminpeers extends Controller
{

    public function __construct()
    {
        $this->session = Auth::user(_MODERATOR, 2);
    }

    public function index()
    {
        $count1 = number_format(get_row_count("peers"));
        //$count = DB::run("SELECT COUNT(*) FROM peers")->fetchColumn();
        $peersperpage = 50;
        list($pagertop, $pagerbottom, $limit) = pager($peersperpage, $count1, "/adminpeers?");

        $result = DB::run("SELECT * FROM peers ORDER BY started DESC $limit");
        $data = [
            'title' => Lang::T("Peers List"),
            'count1' => $count1,
            'pagertop' => $pagertop,
            'pagerbottom' => $pagerbottom,
            'result' => $result
        ];
        View::render('peers/admin/index', $data, 'admin');
   }
}