<?php

class Completed extends Controller {

    public function __construct()
    {
        $this->session = (new Auth)->user(0, 2);
        // $this->userModel = $this->model('User');
        $this->valid = new Validation();
    }
    
    public function index()
    {
        if ($this->session["view_torrents"] == "no") {
            Redirect::autolink(URLROOT, Lang::T("NO_TORRENT_VIEW"));
        }
        $id = (int) Input::get("id");
        $res = DB::run("SELECT name, external, banned FROM torrents WHERE id =?", [$id]);
        $row = $res->fetch(PDO::FETCH_ASSOC);
        if ((!$row) || ($row["banned"] == "yes" && $this->session["edit_torrents"] == "no")) {
            Redirect::autolink(URLROOT, Lang::T("TORRENT_NOT_FOUND"));
        }
        if ($row["external"] == "yes") {
            Redirect::autolink(URLROOT, Lang::T("THIS_TORRENT_IS_EXTERNALLY_TRACKED"));
        }
        $res = DB::run("SELECT users.id, users.username, users.uploaded, users.downloaded, users.privacy, completed.date FROM users LEFT JOIN completed ON users.id = completed.userid WHERE users.enabled = 'yes' AND completed.torrentid = '$id'");
        if ($res->rowCount() == 0) {
            Redirect::autolink(URLROOT, Lang::T("NO_DOWNLOADS_YET"));
        }
        $title = sprintf(Lang::T("COMPLETED_DOWNLOADS"), mb_substr($row["name"], 0, 40));
        $data = [
            'title' => $title,
            'res' => $res,
            'id' => $id,
        ];
        $this->view('torrent/completed', $data, 'user');
    }

}