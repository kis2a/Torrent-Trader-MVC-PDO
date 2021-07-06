<?php
class Admincp extends Controller
{

    public function __construct()
    {
        $this->session = (new Auth)->user(_MODERATOR, 2);
        $this->logsModel = $this->model('Logs');
        $this->valid = new Validation();
    }

    public function index()
    {
        $data = [
            'title' => 'Staff Panel'
        ];
        $this->view('admin/index', $data, 'admin');
    }
    
}