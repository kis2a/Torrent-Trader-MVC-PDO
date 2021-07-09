<?php
class Teams extends Controller
{

    public function __construct()
    {
        $this->session = Auth::user(0, 2);
    }
    
    public function index()
    {
        $res = Team::getTeams();
        if ($res->rowCount() == 0) {
            Redirect::autolink(URLROOT . '/home', 'No teams available, to create a group please contact <a href='.URLROOT.'/group/staff>staff</a>');
        }
        $data = [
            'title' => Lang::T("Teams"),
            'res' => $res
        ];
        View::render('teams/index', $data, 'user');
    }

}