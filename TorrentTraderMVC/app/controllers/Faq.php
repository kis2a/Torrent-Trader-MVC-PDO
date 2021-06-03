<?php
class Faq extends Controller
{
    public function __construct()
    {
        Auth::user();
        $this->faqModel = $this->model('Faqs');
        $this->valid = new Validation();
    }

    public function index()
    {
        $faq_categ = $this->faqModel->bigone();
        $data = [
            'title' => 'FAQ',
            'faq_categ' => $faq_categ,
            ];
        $this->view('faq/index', $data, 'user');
    }

}