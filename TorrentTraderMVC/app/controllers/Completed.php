<?php

class Completed {

    public function __construct()
    {
        $this->session = Auth::user(0, 2);
    }
    
    public function index()
    {
        if (Users::has("view_torrents") == "no") {
            Redirect::autolink(URLROOT, Lang::T("NO_TORRENT_VIEW"));
        }
        $id = (int) Input::get("id");
        $row = Torrents::getNameExternalBanned($id);
        if ((!$row) || ($row["banned"] == "yes" && $_SESSION["edit_torrents"] == "no")) {
            Redirect::autolink(URLROOT, Lang::T("TORRENT_NOT_FOUND"));
        }
        if ($row["external"] == "yes") {
            Redirect::autolink(URLROOT, Lang::T("THIS_TORRENT_IS_EXTERNALLY_TRACKED"));
        }
        $res = Complete::completedUser($id);
        if ($res->rowCount() == 0) {
            Redirect::autolink(URLROOT, Lang::T("NO_DOWNLOADS_YET"));
        }
        $title = sprintf(Lang::T("COMPLETED_DOWNLOADS"), CutName($row["name"], 40));
        $data = [
            'title' => $title,
            'res' => $res,
            'id' => $id,
        ];
        View::render('torrent/completed', $data, 'user');
    }

}