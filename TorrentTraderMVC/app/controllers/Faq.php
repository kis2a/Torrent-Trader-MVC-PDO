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
        Style::header(Lang::T("FAQ"));
        $faq_categ = null;
        $res = $this->faqModel->getFaqByCat();
        while ($arr = $res->fetch(PDO::FETCH_BOTH)) {
            $faq_categ[$arr['id']]['title'] = $arr['question'];
            $faq_categ[$arr['id']]['flag'] = $arr['flag'];
        }
        $res = $this->faqModel->getFaqByType();
        while ($arr = $res->fetch(PDO::FETCH_BOTH)) {
            $faq_categ[$arr['categ']]['items'][$arr['id']]['question'] = $arr['question'];
            $faq_categ[$arr['categ']]['items'][$arr['id']]['answer'] = $arr['answer'];
            $faq_categ[$arr['categ']]['items'][$arr['id']]['flag'] = $arr['flag'];
        }
        if (isset($faq_categ)) {
            // gather orphaned items
            foreach ($faq_categ as $id => $temp) {
                if (!array_key_exists("title", $faq_categ[$id])) {
                    foreach ($faq_categ[$id]['items'] as $id2 => $temp) {
                        $faq_orphaned[$id2]['question'] = $faq_categ[$id]['items'][$id2]['question'];
                        $faq_orphaned[$id2]['answer'] = $faq_categ[$id]['items'][$id2]['answer'];
                        $faq_orphaned[$id2]['flag'] = $faq_categ[$id]['items'][$id2]['flag'];
                        unset($faq_categ[$id]);
                    }
                }
            }
            $data = [
                'faq_categ' => $faq_categ,
            ];
            $this->view('faq/index', $data);
            Style::footer();
        }
    }

}