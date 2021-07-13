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
            Redirect::autolink(URLROOT, Lang::T("NO_TEAM"));
        }
        $data = [
            'title' => Lang::T("TEAM[1]"),
            'res' => $res
        ];
        View::render('teams/index', $data, 'user');
    }

}