<?php
class Admincp extends Controller
{

    public function __construct()
    {
        $this->session = Auth::user(_MODERATOR, 2);
        $this->logsModel = $this->model('Logs');
        
    }

    public function index()
    {
        $data = [
            'title' => 'Staff Panel'
        ];
        View::render('admin/index', $data, 'admin');
    }
    
}