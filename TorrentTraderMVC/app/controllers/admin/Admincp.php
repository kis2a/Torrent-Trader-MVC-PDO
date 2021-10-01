<?php
class Admincp
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
        View::render('admininc/index', $data, 'admin');
    }
    
}