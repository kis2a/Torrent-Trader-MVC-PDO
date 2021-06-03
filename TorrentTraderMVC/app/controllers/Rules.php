<?php
class Rules extends Controller
{

    public function __construct()
    {
        Auth::user();
        $this->rulesModel = $this->model('Rule');
    }

    public function index()
    {
        $res = $this->rulesModel->getRules();
        $data = [
            'title' => 'Rules',
            'res' => $res
        ];
        $this->view('rules/index', $data, 'user');
    }
}
