<?php
class Admincomments
{

    public function __construct()
    {
        $this->session = Auth::user(_MODERATOR, 2);
    }

    public function index()
    {
        $count = get_row_count("comments");
        list($pagertop, $pagerbottom, $limit) = pager(10, $count, URLROOT."/admincomments?");
        $res = Comment::graball($limit);
        $data = [
            'title' => Lang::T("TORRENT_CATEGORIES"),
            'res' => $res,
            'pagerbottom' => $pagerbottom,
            'count' => $count,
        ];
        View::render('comments/admin/index', $data, 'admin');
    }

}