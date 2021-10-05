<?php
class Nfo
{

    public function __construct()
    {
        $this->session = Auth::user(0, 2);
    }

    public function checks($id, $edit = false)
    {
        if (Users::has("view_torrents") == "no") {
            Redirect::autolink(URLROOT."/torrent?id=$id", "You do not have permission to view nfo's");
        }
        if (!$id) {
            Redirect::autolink(URLROOT."/torrent?id=$id", Lang::T("ID_NOT_FOUND_MSG_VIEW"));
        }
        if ($edit) {
            if (Users::has("edit_torrents") == "no") {
                Redirect::autolink(URLROOT."/torrent?id=$id", Lang::T("NFO_PERMISSION"));
            }
        }
    }

    public function index()
    {
        $id = (int) Input::get("id");
        $this->checks($id);
        $res = Torrents::getTorrentNameNfo($id);
        if ($res["nfo"] != "yes") {
            Redirect::autolink(URLROOT."/torrent?id=$id", Lang::T("NO_NFO"));
        }
        if ($res["nfo"] == "yes") {
            $shortname = CutName(htmlspecialchars($res["name"]), 40);
            $nfofilelocation = UPLOADDIR."/nfos/$id.nfo";
            $filegetcontents = file_get_contents($nfofilelocation);
            $nfo = $filegetcontents;
        }
        if ($nfo) {
            $nfo = Helper::my_nfo_translate($nfo);
            $title = Lang::T("NFO_FILE_FOR") . ": $shortname";
            $data = [
                'id' => $id,
                'title' => $title,
                'nfo' => $nfo,
            ];
            View::render('nfo/index', $data, 'user');
        } else {
            Redirect::autolink(URLROOT."/torrent?id=$id", Lang::T("NFO Found but error"));
        }
    }

    public function edit()
    {
        $id = (int) Input::get("id");
        $this->checks($id, true);
        $res = Torrents::getTorrentNameNfo($id);
        if ($res["nfo"] != "yes") {
            Redirect::autolink(URLROOT."/torrent?id=$id", Lang::T("NO_NFO"));
        }
        if ($res["nfo"] == "yes") {
            $shortname = CutName(htmlspecialchars($res["name"]), 40);
            $nfofilelocation = UPLOADDIR."/nfos/$id.nfo";
            $filegetcontents = file_get_contents($nfofilelocation);
            $nfo = $filegetcontents;
        }
        if ($nfo) {
            $nfo = Helper::my_nfo_translate($nfo);
            $title = Lang::T("NFO_FILE_FOR") . ": <a href='" . URLROOT . "/torrent?id=$id'>$shortname</a>";
            $data = [
                'id' => $id,
                'title' => $title,
                'nfo' => $nfo,
            ];
            View::render('nfo/edit', $data, 'user');
        } else {
            Redirect::autolink(URLROOT."/torrent?id=$id", Lang::T("NFO Found but error"));
        }
    }

    public function submit()
    {
        $id = (int) Input::get("id");
        $this->checks($id, true);

        $nfo = UPLOADDIR."/nfos/$id.nfo";
        if ((!Validate::Id($id)) || (!$contents = file_get_contents($nfo))) {
            Redirect::autolink(URLROOT."/torrent?id=$id", Lang::T("NFO_NOT_FOUND"));
        }
        if (is_file($nfo)) {
            file_put_contents($nfo, $_POST['content']);
            Logs::write("NFO ($id) was updated by $_SESSION[username].");
            Redirect::autolink(URLROOT."/torrent?id=$id", Lang::T("NFO_UPDATED"));
        }else {
            Redirect::autolink(URLROOT."/torrent?edit=$id", sprintf(Lang::T("Problem editing"), $id));
        }
    }

    public function delete()
    {
        $id = (int) Input::get("id");
        $this->checks($id, true);

        $nfo = UPLOADDIR."/nfos/$id.nfo";
        if ((!Validate::Id($id)) || (!$contents = file_get_contents($nfo))) {
            Redirect::autolink(URLROOT."/torrent?id=$id", Lang::T("NFO_NOT_FOUND"));
        }
        $reason = htmlspecialchars(Input::get("reason"));
        if (get_row_count("torrents", "WHERE `nfo` = 'yes' AND `id` = $id")) {
            unlink($nfo);
            Logs::write("NFO ($id) was deleted by $_SESSION[username] $reason");
            DB::run("UPDATE `torrents` SET `nfo` = 'no' WHERE `id` = $id");
            Redirect::autolink(URLROOT."/torrent?id=$id", Lang::T("NFO_DELETED"));
        } else {
            Redirect::autolink(URLROOT."/torrent?id=$id", sprintf(Lang::T("NFO_NOT_EXIST"), $id));
        }
    }

}