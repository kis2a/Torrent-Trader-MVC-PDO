<?php
class Adminteams
{

    public function __construct()
    {
        $this->session = Auth::user(_MODERATOR, 2);
    }

    public function index()
    {
        $sql = DB::run("SELECT * FROM teams");
        $data = [
            'title' => Lang::T("TEAMS_MANAGEMENT"),
            'sql' => $sql
        ];
        View::render('teams/index', $data, 'admin');
    }

    public function add()
    {
        $team_name = $_POST['team_name'];
        $team_image = $_POST['team_image'];
        $team_description = $_POST['team_description'];
        $teamownername = $_POST['team_owner'];
        $add = $_POST['add'];
        $added = TimeDate::get_date_time();

        if ($add == 'true') {
            if (!$team_name || !$teamownername || !$team_description) {
                    Redirect::autolink(URLROOT . '/adminteams', Lang::T("One or more fields left empty."));
                die;
            }
            $team_name = $team_name;
            $team_description = $team_description;
            $team_image = $team_image;
            $teamownername = $teamownername;
            $aa = DB::run("SELECT id FROM users WHERE username =?", [$teamownername]);
            $ar = $aa->fetch(PDO::FETCH_ASSOC);
            $team_owner = $ar["id"];
            if (!$team_owner) {
                    Redirect::autolink(URLROOT . '/adminteams', Lang::T("This user does not exist"));
                die;
            }
            $sql = DB::run("INSERT INTO teams SET name =?, owner =?, info =?, image =?, added =?", [$team_name, $team_owner, $team_description, $team_image, $added]);
            $tid = DB::lastInsertId();
            DB::run("UPDATE users SET team = $tid WHERE id= $team_owner");
        }
        Redirect::autolink(URLROOT . '/adminteams', Lang::T("Team Added"));
    }

    public function delete()
    {
        $sure = $_GET['sure'];
        $del = $_GET['del'];
        $team = htmlspecialchars($_GET['team']);
        if ($sure == "yes") {
            $sql = DB::run("UPDATE users SET team=? WHERE team=?", ['0', $del]);
            $sql = DB::run("DELETE FROM teams WHERE id=? LIMIT 1", [$del]);
            Logs::write($_SESSION['username'] . " has deleted team id:$del");
            Redirect::autolink(URLROOT . '/adminteams', Lang::T("Team Successfully Deleted!"));
            die();
        }
        if ($del > 0) {
            Redirect::autolink(URLROOT . '/adminteams', Lang::T("You and in the truth wish to delete team? ($team) ( <b><a href='".URLROOT."/adminteams/delete?del=$del&amp;team=$team&amp;sure=yes'>Yes!</a></b> / <b><a href='".URLROOT."/adminteams'>No!</a></b> )"));
            die();
        }
    }

    public function edit()
    {
        $edited = (int) $_GET['edited'];
        $id = (int) $_GET['id'];
        $team_name = $_GET['team_name'];
        $team_info = $_GET['team_info'];
        $team_image = $_GET['team_image'];
        $teamownername = $_GET['team_owner'];
        $editid = $_GET['editid'];
        $name = $_GET['name'];
        $image = $_GET['image'];
        $owner = $_GET['owner'];
        $info = $_GET['info'];

        // Post/Get
        if ($edited == 1) {
            if (!$team_name || !$teamownername || !$team_info) {
                    Redirect::autolink(URLROOT . '/adminteams', 'One or more fields left empty.');
                die;
            }
            $team_name = $team_name;
            $team_image = $team_image;
            $teamownername = $teamownername;
            $team_info = $team_info;
            $aa = DB::run("SELECT class, id FROM users WHERE username=?", [$teamownername]);
            $ar = $aa->fetch(PDO::FETCH_ASSOC);
            $team_owner = $ar["id"];
            $sql = DB::run("UPDATE teams SET name =?, info =?, owner =?, image =?  WHERE id=?", [$team_name, $team_info, $team_owner, $team_image, $id]);
            DB::run("UPDATE users SET team =? WHERE id=?", [$id, $team_owner]);
            if ($sql) {
                $mss = "<b>Successfully Edited</b>[<a href='".URLROOT."/adminteams'>Back</a>]";
                Logs::write($_SESSION['username'] . " has edited team ($team_name)");
                    Redirect::autolink(URLROOT . '/adminteams', $mss);
                die();
            }
        }

        if ($editid > 0) {
            $data = [
                'title' => Lang::T("Team Edit"),
                'editid' => $editid,
                'name' => $name,
                'image' => $image,
                'owner' => $owner,
                'info' => $info,
            ];
            View::render('teams/edit', $data, 'admin');
        }
	}

    public function members()
    {
        $teamid = $_GET['teamid'];
        $sql = DB::run("SELECT id,username,uploaded,downloaded FROM users WHERE team=$teamid");
        $data = [
            'title' => Lang::T("TEAMS_MANAGEMENT"),
            'sql' => $sql
        ];
        View::render('teams/members', $data, 'admin');
	}

}