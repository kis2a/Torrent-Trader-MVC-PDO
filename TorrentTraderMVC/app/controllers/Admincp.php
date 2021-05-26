<?php
class Admincp extends Controller
{

    public function __construct()
    {
        Auth::user(); // should check admin here
        $this->logsModel = $this->model('Logs');
        $this->valid = new Validation();
    }

    public function index()
    {
        if (!$_SESSION['class'] > 5 || $_SESSION["control_panel"] != "yes") {
            show_error_msg(Lang::T("ERROR"), Lang::T("SORRY_NO_RIGHTS_TO_ACCESS"), 1);
        }
        $title = 'Add User';
        $data = [
            'title' => $title,
        ];
        $this->view('admin/index', $data);
    }
    
}