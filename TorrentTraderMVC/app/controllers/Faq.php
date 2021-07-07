<?php
class Faq extends Controller
{
    public function __construct()
    {
        $this->session = Auth::user(0, 1);
        $this->faqModel = $this->model('Faqs');
        
    }

    public function index()
    {
        $faq_categ = $this->faqModel->bigone();
        $data = [
            'title' => 'FAQ',
            'faq_categ' => $faq_categ,
            ];
        View::render('faq/index', $data, 'user');
    }

}