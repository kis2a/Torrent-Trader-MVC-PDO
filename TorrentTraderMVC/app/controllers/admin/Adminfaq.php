<?php
class Adminfaq
{
    public function __construct()
    {
        $this->session = Auth::user(_MODERATOR, 2);
    }

    public function index()
    {
        // make the array that has all the faq in a nice structured
        $res = DB::run("SELECT `id`, `question`, `flag`, `order` FROM `faq` WHERE `type`='categ' ORDER BY `order` ASC");
        while ($arr = $res->fetch(PDO::FETCH_LAZY)) {
            $faq_categ[$arr['id']]['title'] = $arr['question'];
            $faq_categ[$arr['id']]['flag'] = $arr['flag'];
            $faq_categ[$arr['id']]['order'] = $arr['order'];
        }

        $res = DB::run("SELECT `id`, `question`, `flag`, `categ`, `order` FROM `faq` WHERE `type`='item' ORDER BY `order` ASC");
        while ($arr = $res->fetch(PDO::FETCH_LAZY)) {
            $faq_categ[$arr['categ']]['items'][$arr['id']]['question'] = $arr['question'];
            $faq_categ[$arr['categ']]['items'][$arr['id']]['flag'] = $arr['flag'];
            $faq_categ[$arr['categ']]['items'][$arr['id']]['order'] = $arr['order'];
        }

        if (isset($faq_categ)) {
            // gather orphaned items
            foreach ($faq_categ as $id => $temp) {
                if (!array_key_exists("title", $faq_categ[$id])) {
                    foreach ($faq_categ[$id]['items'] as $id2 => $temp) {
                        $faq_orphaned[$id2]['question'] = $faq_categ[$id]['items'][$id2]['question'];
                        $faq_orphaned[$id2]['flag'] = $faq_categ[$id]['items'][$id2]['flag'];
                        unset($faq_categ[$id]);
                    }
                }
            }

            $data = [
                'title' => Lang::T("FAQ_MANAGEMENT"),
                'faq_categ' => $faq_categ,
            ];
            View::render('faq/admin/manage', $data, 'admin');
        }
    }

    public function reorder()
    {
        foreach ($_POST['order'] as $id => $position) {
            DB::run("UPDATE `faq` SET `order`='$position' WHERE id='$id'");
        }
        Redirect::to(URLROOT . "/adminfaq");
    }

    public function delete()
    {
        if ($_GET['confirm'] == "yes") {
            DB::run("DELETE FROM `faq` WHERE `id`=? LIMIT 1", [$_GET['id']]);
            Redirect::to(URLROOT . "/adminfaq");
        } else {
                Redirect::autolink(URLROOT . '/adminfaq',  "Please click <a href=\"" . URLROOT . "/adminfaq/delete?id=$_GET[id]&amp;confirm=yes\">here</a> to confirm.", URLROOT . "/adminfaq");
        }
    }

    public function newsection()
    {
        if ($_POST['action'] == "addnewsect" && $_POST['title'] != null && Validate::Int($_POST['flag'])) {
            $title = $_POST['title'];
            $res = DB::run("SELECT MAX(`order`) FROM `faq` WHERE `type`='categ'");
            while ($arr = $res->fetch(PDO::FETCH_BOTH)) {
                $order = $arr[0] + 1;
            }
            DB::run("INSERT INTO `faq` (`type`, `question`, `answer`, `flag`, `categ`, `order`) VALUES (?,?,?,?,?,?)", ['categ', $title, '', $_POST['flag'], 0, $order]);
            Redirect::to(URLROOT . "/adminfaq");
        }
        $data = [
            'title' => Lang::T("FAQ_MANAGEMENT"),
        ];
        View::render('faq/admin/newsection', $data, 'admin');
    }

    public function additem()
    {
        if ($_POST['question'] != null && $_POST['answer'] != null && Validate::Int($_POST['flag']) && Validate::Int($_POST['categ'])) {
            $question = $_POST['question'];
            $answer = $_POST['answer'];
            $res = DB::run("SELECT MAX(`order`) FROM `faq` WHERE `type`='item' AND `categ`='$_POST[categ]'");
            while ($arr = $res->fetch(PDO::FETCH_BOTH)) {
                $order = $arr[0] + 1;
            }
            DB::run("INSERT INTO `faq` (`type`, `question`, `answer`, `flag`, `categ`, `order`) VALUES (?,?,?,?,?,?)", ['item', $question, $answer, $_POST['flag'], $_POST['categ'], $order]);
            Redirect::to(URLROOT . "/adminfaq");
        }
        $res = DB::run("SELECT `id`, `question` FROM `faq` WHERE `type`='categ' ORDER BY `order` ASC");
        $data = [
            'title' => Lang::T("FAQ_MANAGEMENT"),
            'res' => $res
        ];
        View::render('faq/admin/newitem', $data, 'admin');
    }

    public function edit()
    {
        // subACTION: edititem - edit an item
        if ($_GET['action'] == "edititem" && Validate::Id($_POST['id']) && $_POST['question'] != null && $_POST['answer'] != null && Validate::Int($_POST['flag']) && Validate::Id($_POST['categ'])) {
            $question = $_POST['question'];
            $answer = $_POST['answer'];
            DB::run("UPDATE `faq` SET `question`=?, `answer`=?, `flag`=?, `categ`=? WHERE id=?", [$question, $answer, $_POST['flag'], $_POST['categ'], $_POST['id']]);
            Redirect::to(URLROOT . "/adminfaq");
        }
        // subACTION: editsect - edit a section
        if ($_GET['action'] == "editsect" && Validate::Id($_POST['id']) && $_POST['title'] != null && Validate::Int($_POST['flag'])) {
            $title = $_POST['title'];
            DB::run("UPDATE `faq` SET `question`=?, `answer`=?, `flag`=?, `categ`=? WHERE id=?", [$title, $_POST['flag'], '', 0, $_POST['id']]);
            Redirect::to(URLROOT . "/adminfaq");
        }
        $res = DB::run("SELECT * FROM `faq` WHERE `id`=? LIMIT 1", [$_GET['id']]);
        $res2 = DB::run("SELECT `id`, `question` FROM `faq` WHERE `type`='categ' ORDER BY `order` ASC");

        $data = [
            'title' => Lang::T("FAQ_MANAGEMENT"),
            'res' => $res,
            'res2' => $res2,
        ];
        View::render('faq/admin/edit', $data, 'admin');
    }

}