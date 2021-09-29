<?php
class Adminpolls
{

    public function __construct()
    {
        $this->session = Auth::user(_MODERATOR, 2);
    }

    public function index()
    {
        $query = DB::run("SELECT id,question,added FROM polls ORDER BY added DESC");
        $data = [
            'title' => Lang::T("POLLS_MANAGEMENT"),
            'query' => $query,
        ];
        View::render('poll/admin/polls-view', $data, 'admin');
    }

    public function results()
    {
        $poll = DB::run("SELECT * FROM pollanswers ORDER BY pollid DESC");
        $data = [
            'title' => Lang::T("POLLS_MANAGEMENT"),
            'poll' => $poll,
        ];
        View::render('poll/admin/pollsresults', $data, 'admin');
    }

    public function delete()
    {
        $id = (int) $_GET["id"];
        if (!Validate::Id($id)) {
            Redirect::autolink(URLROOT."/adminpolls", sprintf(Lang::T("CP_NEWS_INVAILD_ITEM_ID").$id));
        }
        DB::run("DELETE FROM polls WHERE id=?", [$id]);
        DB::run("DELETE FROM pollanswers WHERE  pollid=?", [$id]);
        Redirect::autolink(URLROOT . "/adminpolls", Lang::T("Poll and answers deleted"));
    }

    public function add()
    {
        $pollid = (int) $_GET["pollid"];
        $res = DB::run("SELECT * FROM polls WHERE id =?", [$pollid]);
        $data = [
            'title' => Lang::T("POLLS_MANAGEMENT"),
            'res' => $res,
            'id' => $pollid
        ];
        View::render('poll/admin/pollsadd', $data, 'admin');
    }

    public function save()
    {
        $subact = $_POST["subact"];
        $pollid = (int) $_POST["pollid"];

        $question = $_POST["question"];
        $option0 = $_POST["option0"];
        $option1 = $_POST["option1"];
        $option2 = $_POST["option2"];
        $option3 = $_POST["option3"];
        $option4 = $_POST["option4"];
        $option5 = $_POST["option5"];
        $option6 = $_POST["option6"];
        $option7 = $_POST["option7"];
        $option8 = $_POST["option8"];
        $option9 = $_POST["option9"];
        $option10 = $_POST["option10"];
        $sort = (int) $_POST["sort"];

        if (!$question || !$option0 || !$option1) {
            Redirect::autolink(URLROOT."/adminpolls", Lang::T("MISSING_FORM_DATA") . "!");
        }
        if ($subact == "edit") {
            if (!Validate::Id($pollid)) {
                Redirect::autolink(URLROOT."/adminpolls", Lang::T("INVALID_ID"));
            }
            DB::run("UPDATE polls SET " .
                "question = ?, " .
                "option0 = ?, " .
                "option1 = ?, " .
                "option2 = ?, " .
                "option3 = ?, " .
                "option4 = ?, " .
                "option5 = ?, " .
                "option6 = ?, " .
                "option7 = ?, " .
                "option8 = ?, " .
                "option9 = ?, " .
                "option10 =?, " .
                "sort =? " .
                "WHERE id = $pollid", [$question, $option0, $option1, $option2, $option3, $option4, $option5,
                    $option6, $option7, $option8, $option9, $option10, $sort]);
        } else {
            DB::run("INSERT INTO polls (added,question,option0,option1,option2,option3,option4,option5,
                option6,option7,option8,option9,sort)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)", [TimeDate::get_date_time(), $question, $option0, $option1,
                $option2, $option3, $option4, $option5,
                $option6, $option7, $option8, $option9, $sort]);
        }
        Redirect::autolink(URLROOT . "/adminpolls", Lang::T("COMPLETE"));
    }

}