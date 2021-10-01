<?php
class Adminpeers
{

    public function __construct()
    {
        $this->session = Auth::user(_MODERATOR, 2);
    }

    public function index()
    {
        $count = number_format(get_row_count("peers"));
        list($pagertop, $pagerbottom, $limit) = pager(50, $count, "/adminpeers?");
        $result = DB::run("SELECT * FROM peers ORDER BY started DESC $limit");
        $data = [
            'title' => Lang::T("Peers List"),
            'count1' => $count,
            'pagertop' => $pagertop,
            'pagerbottom' => $pagerbottom,
            'result' => $result
        ];
        View::render('peers/index', $data, 'admin');
   }

}