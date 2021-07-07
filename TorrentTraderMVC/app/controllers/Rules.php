<?php
class Rules extends Controller
{

    public function __construct()
    {
        $this->user = $this->session = Auth::user(0, 1);
        $this->rulesModel = $this->model('Rule');
    }

    public function index()
    {
        $res = $this->rulesModel->getRules();
        $data = [
            'title' => 'Rules',
            'res' => $res
        ];
        View::render('rules/index', $data, 'user');
    }
}
