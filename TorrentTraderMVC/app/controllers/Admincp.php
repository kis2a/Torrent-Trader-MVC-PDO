<?php
class Admincp extends Controller
{

    public function __construct()
    {
        $this->session = Auth::user(_MODERATOR, 2);
    }

    public function index()
    {
        $data = [
            'title1' => 'Staff Panel',
            'title' => 'Staff Chat',
            ];
        View::render('admin/index', $data, 'admin');
    }
    
}