<?php
class Faq extends Controller
{
    public function __construct()
    {
        $this->session = Auth::user(0, 1);
    }

    public function index()
    {
        $faq_categ = Faqs::bigone();
        $data = [
            'title' => 'FAQ',
            'faq_categ' => $faq_categ,
            ];
        View::render('faq/index', $data, 'user');
    }

}