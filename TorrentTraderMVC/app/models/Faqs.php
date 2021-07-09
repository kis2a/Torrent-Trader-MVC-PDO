<?php
class Faqs
{

    public static function getFaqByCat()
    {
        $stmt = DB::run("SELECT `id`, `question`, `flag` FROM `faq` WHERE `type`=? ORDER BY `order` ASC", ['categ']);
        return $stmt;
// $this->db->run("SELECT `id`, `question`, `flag` FROM `faq` WHERE `type`=? ORDER BY `order` ASC", ['categ'])->fetch(PDO::FETCH_BOTH);

    }

    public static function getFaqByType()
    {
        $stmt = DB::run("SELECT `id`, `question`, `answer`, `flag`, `categ` FROM `faq` WHERE `type`=? ORDER BY `order` ASC", ['item']);
        return $stmt;

    }

    public static function bigone()
    {
        $res = DB::run("SELECT `id`, `question`, `flag` FROM `faq` WHERE `type`=? ORDER BY `order` ASC", ['categ']);
        while ($arr = $res->fetch(PDO::FETCH_BOTH)) {
            $faq_categ[$arr['id']]['title'] = $arr['question'];
            $faq_categ[$arr['id']]['flag'] = $arr['flag'];
        }
        $res = DB::run("SELECT `id`, `question`, `answer`, `flag`, `categ` FROM `faq` WHERE `type`=? ORDER BY `order` ASC", ['item']);
        while ($arr = $res->fetch(PDO::FETCH_BOTH)) {
            $faq_categ[$arr['categ']]['items'][$arr['id']]['question'] = $arr['question'];
            $faq_categ[$arr['categ']]['items'][$arr['id']]['answer'] = $arr['answer'];
            $faq_categ[$arr['categ']]['items'][$arr['id']]['flag'] = $arr['flag'];
        }
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

        return $faq_categ;
    }

}
