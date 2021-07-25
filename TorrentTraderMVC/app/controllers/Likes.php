<?php
class Likes
{

    public function __construct()
    {
        $this->session = Auth::user(0, 2);
    }

    // Likes
    public function index()
    {
        $id = (int) Input::get('id');
        $type = Input::get('type');
        if (!Validate::Id($id)) {
            Redirect::autolink(URLROOT, Lang::T("INVALID_ID"));
        }
        if (!$type) {
            Redirect::autolink(URLROOT, "No Type");
        }
        $this->likeswitch($id, $type);
    }

    public function likeswitch($id, $type)
    {
        switch ($type) {
            case 'liketorrent':
                DB::run("INSERT INTO likes (user, liked, added, type, reaction) VALUES (?, ?, ?, ?, ?)", [$_SESSION['id'], $id, TimeDate::get_date_time(), 'torrent', 'like']);
                Redirect::autolink(URLROOT."/torrent?id=$id", "Thanks you for you appreciation.");
                break;
            case 'unliketorrent':
                DB::run("DELETE FROM likes WHERE user=? AND liked=? AND type=?", [$_SESSION['id'], $id, 'torrent']);
                Redirect::autolink(URLROOT."/torrent?id=$id", "Unliked.");
                break;
            default:
                Redirect::autolink(URLROOT, "Thanks you for you appreciation.");
                break;
        }
    }

    public function thanks()
    {
        $id = (int) Input::get('id');
        $type = Input::get('type');
        if (!Validate::Id($id)) {
            Redirect::autolink(URLROOT, Lang::T("INVALID_ID"));
        }
        if (!$type) {
            Redirect::autolink(URLROOT, "No ID");
        }
        $this->thankswitch($id, $type);
    }

    public function thankswitch($id, $type)
    {
        switch ($type) {
            case 'torrent':
                DB::run("INSERT INTO thanks (user, thanked, added, type) VALUES (?, ?, ?, ?)", [$_SESSION['id'], $id, TimeDate::get_date_time(), 'torrent']);
                Redirect::autolink(URLROOT."/torrent?id=$id", "Thanks you for you appreciation.");
                break;
            case 'thanksforum':
                DB::run("INSERT INTO thanks (user, thanked, added, type) VALUES (?, ?, ?, ?)", [$_SESSION['id'], $id, TimeDate::get_date_time(), 'forum']);
                Redirect::autolink(URLROOT."/forums/viewtopic&topicid=$id", "Thanks you for you appreciation.");
                break;
            default:
                Redirect::autolink(URLROOT, "Thanks you for you appreciation.");
                break;
        }
    }

}