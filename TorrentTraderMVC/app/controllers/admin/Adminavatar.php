<?php
class Adminavatar extends Controller
{

    public function __construct()
    {
        $this->session = Auth::user(_MODERATOR, 2);
    }

    public function index()
    {
        $count = DB::run("SELECT count(*) FROM users WHERE enabled=? AND avatar !=?", ['yes', ''])->fetchColumn();
        list($pagertop, $pagerbottom, $limit) = pager(50, $count, URLROOT.'/adminavatar?');
        $res = DB::run("SELECT username, id, avatar FROM users WHERE enabled='yes' AND avatar !='' $limit");

        $data = [
            'title' => "Avatar Log",
            'pagertop' => $pagertop,
            'res' => $res,
            'pagerbottom' => $pagerbottom,
        ];
        View::render('user/admin/avatar', $data, 'admin');
    }

}