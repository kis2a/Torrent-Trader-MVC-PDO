<?php
class Rules extends Controller
{

    public function __construct()
    {
        $this->session = Auth::user(0, 1);
    }

    public function index()
    {
        $res = Rule::getRules();
        $data = [
            'title' => 'Rules',
            'res' => $res
        ];
        View::render('rules/index', $data, 'user');
    }
}
