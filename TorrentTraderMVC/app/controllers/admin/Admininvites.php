<?php
class Admininvites
{

    public function __construct()
    {
        $this->session = Auth::user(_MODERATOR, 2);
    }

    public function index()
    {
        $do = $_GET['do']; // todo
        if ($do == "del") {
            if (!@count($_POST["users"])) {
                Redirect::autolink(URLROOT . '/admininvites', "Nothing Selected.");
            }
            $ids = array_map("intval", $_POST["users"]);
            $ids = implode(", ", $ids);
            $res = DB::run("SELECT u.id, u.invited_by, i.invitees FROM users u LEFT JOIN users i ON u.invited_by = i.id WHERE u.status = 'pending' AND u.invited_by != '0' AND u.id IN ($ids)");
            while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
                # We remove the invitee from the inviter and give them back there invite.
                $invitees = str_replace("$row[id] ", "", $row["invitees"]);
                DB::run("UPDATE `users` SET `invites` = `invites` + 1, `invitees` = '$invitees' WHERE `id` = '$row[invited_by]'");
                Users::deleteuser($row['id']);
            }
            Redirect::autolink(URLROOT . "/Admininvites", "Entries Deleted");
        }

        $count = DB::run("SELECT COUNT(*) FROM users WHERE status = 'confirmed' AND invited_by != '0'")->fetchColumn();
        list($pagertop, $pagerbottom, $limit) = pager(25, $count, 'Admininvites?');
        $res = DB::run("SELECT u.id, u.username, u.email, u.added, u.last_access, u.class, u.invited_by, i.username as inviter FROM users u LEFT JOIN users i ON u.invited_by = i.id WHERE u.status = 'confirmed' AND u.invited_by != '0' ORDER BY u.added DESC $limit");
        $data = [
            'title' => Lang::T("Invited Users"),
            'count' => $count,
            'pagerbottom' => $pagerbottom,
            'res' => $res,
        ];
        View::render('invite/admin/invited', $data, 'admin');
    }
    
    public function pending()
    {
        $do = $_POST['do']; // todo
        if ($do == "del") {
            if (!@count($_POST["users"])) {
                Redirect::autolink(URLROOT . '/admininvites/pending', "Nothing Selected.");
            }
            $ids = array_map("intval", $_POST["users"]);
            $ids = implode(", ", $ids);
            $res = DB::run("SELECT u.id, u.invited_by, i.invitees FROM users u LEFT JOIN users i ON u.invited_by = i.id WHERE u.status = 'pending' AND u.invited_by != '0' AND u.id IN ($ids)");
            while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
                # We remove the invitee from the inviter and give them back there invite.
                $invitees = str_replace("$row[id] ", "", $row["invitees"]);
                DB::run("UPDATE `users` SET `invites` = `invites` + 1, `invitees` = '$invitees' WHERE `id` = '$row[invited_by]'");
                DB::run("DELETE FROM `users` WHERE `id` = '$row[id]'");
            }
            Redirect::autolink(URLROOT . "/Admininvites/pending", "Entries Deleted");
        }
        $count = DB::run("SELECT COUNT(*) FROM users WHERE status = 'pending' AND invited_by != '0'")->fetchColumn();
        list($pagertop, $pagerbottom, $limit) = pager(25, $count, '/Admininvites/pending?');

        $res = DB::run("SELECT u.id, u.username, u.email, u.added, u.invited_by, i.username as inviter FROM users u LEFT JOIN users i ON u.invited_by = i.id WHERE u.status = 'pending' AND u.invited_by != '0' ORDER BY u.added DESC $limit");
        $data = [
            'title' => Lang::T("Invited Pending Users"),
            'count' => $count,
            'pagerbottom' => $pagerbottom,
            'res' => $res,
        ];
        View::render('invite/admin/pendinginvite', $data, 'admin');
    }

}