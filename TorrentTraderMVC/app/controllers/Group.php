<?php
class Group extends Controller
{

    public function __construct()
    {
        $this->session = Auth::user(0, 2);
    }

    public function index()
    {
        Redirect::to(URLROOT);
    }

    public function members()
    {
        if ($_SESSION["view_users"] == "no") {
            Redirect::autolink(URLROOT, Lang::T("NO_USER_VIEW"));
        }

        $search = Input::get('search');
        $class = (int) Input::get('class');
        $letter = Input::get('letter');
        if (!$class) {
            unset($class);
        }
        $q = $query = null;
        if ($search) {
            $query = "username LIKE " . sqlesc("%$search%") . " AND status='confirmed'";
            if ($search) {
                $q = "search=" . htmlspecialchars($search);
            }
        } elseif ($letter) {
            if (strlen($letter) > 1) {
                unset($letter);
            }
            if ($letter == "" || strpos("abcdefghijklmnopqrstuvwxyz", $letter) === false) {
                unset($letter);
            } else {
                $query = "username LIKE '$letter%' AND status='confirmed'";
            }
            $q = "letter=$letter";
        }
        if (!$query) {
            $query = "status='confirmed'";
        }
        if ($class) {
            $query .= " AND class=$class";
            $q .= ($q ? "&amp;" : "") . "class=$class";
        }

        $res = Groups::getGroups();
        $data = [
            'title' => 'Members',
            'getgroups' => $res,
            'query1' => $query,
            'query2' => $q,
        ];
        View::render('groups/index', $data, 'user');
    }

    public function staff()
    {
        $dt = TimeDate::get_date_time(TimeDate::gmtime() - 180);
        $res = Groups::getStaff();
        $col = [];
        $table = [];
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $table[$row['class']] = ($table[$row['class']] ?? '') .
            "<td><img src='" . URLROOT . "/assets/images/button_o" . ($row["last_access"] > $dt ? "n" : "ff") . "line.png' alt='' /> " .
            "<a href='" . URLROOT . "/profile?id=" . $row["id"] . "'>" . Users::coloredname($row["username"]) . "</a> " .
                "<a href='" . URLROOT . "/messages/create?id=" . $row["id"] . "'><img src='" . URLROOT . "/assets/images/button_pm.gif' border='0' alt='' /></a></td>";
            $col[$row['class']] = ($col[$row['class']] ?? 0) + 1;
            if ($col[$row["class"]] <= 4) {
                $table[$row["class"]] = $table[$row["class"]] . "<td></td>";
            } else {
                $table[$row["class"]] = $table[$row["class"]] . "</tr><tr>";
                $col[$row["class"]] = 2;
            }
        }

        $where = null;
        if ($_SESSION["edit_users"] == "no") {
            $where = "AND `staff_public` = 'yes'";
        }

        $res = Groups::getStaffLevel($where);
        if ($res->rowCount() == 0) {
            Redirect::autolink(URLROOT, Lang::T("NO_STAFF_HERE"));
        }
        $title = Lang::T("STAFF");
        $data = [
            'title' => $title,
            'sql' => $res,
            'table' => $table,
        ];
        View::render('groups/staff', $data, 'user');
    }

}