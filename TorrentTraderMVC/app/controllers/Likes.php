<?php
class Likes extends Controller
{

    public function __construct()
    {
        $this->session = Auth::user(0, 2);
    }

    // Thanks on index
    public function index()
    {
        $id = (int) Input::get('id');
        if (!Validate::Id($id)) {
            Redirect::autolink(URLROOT, "No ID");
        }
        DB::run("INSERT INTO thanks (user, thanked, added, type) VALUES (?, ?, ?, ?)", [$_SESSION['id'], $id, TimeDate::get_date_time(), 'torrent']);
        Redirect::autolink(URLROOT, "Thanks you for you appreciation.");
    }
    // Thanks on details
    public function details()
    {
        $id = (int) Input::get('id');
        if (!Validate::Id($id)) {
            Redirect::autolink(URLROOT, "No ID");
        }
        DB::run("INSERT INTO thanks (user, thanked, added, type) VALUES (?, ?, ?, ?)", [$_SESSION['id'], $id, TimeDate::get_date_time(), 'torrent']);
        Redirect::autolink(URLROOT."/torrent?id=$id", "Thanks you for you appreciation.");
    }

    public function liketorrent()
    {
        $id = (int) Input::get('id');
        if (!Validate::Id($id)) {
            Redirect::autolink(URLROOT, "No ID");
        }
        DB::run("INSERT INTO likes (user, liked, added, type, reaction) VALUES (?, ?, ?, ?, ?)", [$_SESSION['id'], $id, TimeDate::get_date_time(), 'torrent', 'like']);
        Redirect::autolink(URLROOT."/torrent?id=$id", "Thanks you for you appreciation.");
    }

    public function unliketorrent()
    {
        $id = (int) Input::get('id');
        if (!Validate::Id($id)) {
            Redirect::autolink(URLROOT, "No ID");
        }
        DB::run("DELETE FROM likes WHERE user=? AND liked=? AND type=?", [$_SESSION['id'], $id, 'torrent']);
        Redirect::autolink(URLROOT."/torrent?id=$id", "Unliked.");
    }

    public function likeforum()
    {
        $id = (int) Input::get('id');
        if (!Validate::Id($id)) {
            Redirect::autolink(URLROOT, "No ID");
        }
        DB::run("INSERT INTO thanks (user, thanked, added, type) VALUES (?, ?, ?, ?)", [$_SESSION['id'], $id, TimeDate::get_date_time(), 'forum']);
        Redirect::autolink(URLROOT."/forums/viewtopic&topicid=$id", "Thanks you for you appreciation.");
    }

}