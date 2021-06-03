<?php
class Admincp extends Controller
{

    public function __construct()
    {
        Auth::user();
        Auth::isStaff();
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