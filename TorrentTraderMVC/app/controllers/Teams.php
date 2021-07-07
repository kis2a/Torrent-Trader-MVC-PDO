<?php
class Teams extends Controller
{

    public function __construct()
    {
        $this->session = Auth::user(0, 2);
        $this->teamModel = $this->model('Team');
        $this->log = $this->model('Logs');
    }
    
    public function index()
    {
        $res = $this->teamModel->getTeams();
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