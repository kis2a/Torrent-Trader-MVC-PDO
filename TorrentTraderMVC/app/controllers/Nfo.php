<?php
class Nfo extends Controller
{

    public function __construct()
    {
        $this->session = (new Auth)->user(0, 2);
        $this->torrentModel = $this->model('Torrents');
        $this->valid = new Validation();
        $this->logsModel = $this->model('Logs');
    }

    public function index()
    {
        $id = (int) Input::get("id");
        if ($this->session["view_torrents"] == "no") {
            Redirect::autolink(URLROOT."/torrent?id=$id", "You do not have permission to view nfo's");
        }
        if (!$id) {
            Redirect::autolink(URLROOT."/torrent?id=$id", Lang::T("ID_NOT_FOUND_MSG_VIEW"));
        }

        $res = $this->torrentModel->getTorrentNameNfo($id);
        if ($res["nfo"] != "yes") {
            Redirect::autolink(URLROOT."/torrent?id=$id", Lang::T("NO_NFO"));
        }

        if ($res["nfo"] == "yes") {
            $shortname = mb_substr(htmlspecialchars($res["name"]), 0, 50);
            $nfo_dir = NFODIR;
            $nfofilelocation = "$nfo_dir/$id.nfo";
            $filegetcontents = file_get_contents($nfofilelocation);
            $nfo = $filegetcontents;
        }
        if ($nfo) {
            $nfo = Helper::my_nfo_translate($nfo);
            $titleedit = Lang::T("NFO_FILE_FOR") . ": <a href='" . URLROOT . "/torrent?id=$id'>$shortname</a> - <a href='".URLROOT."/nfo/edit?id=$id'>" . Lang::T("NFO_EDIT") . "</a>";
            $title = Lang::T("NFO_FILE_FOR") . ": $shortname";
            $data = [
                'id' => $id,
                'title' => $title,
                'titleedit' => $titleedit,
                'nfo' => $nfo,
            ];
            $this->view('nfo/index', $data, 'user');
        } else {
            Redirect::autolink(URLROOT."/torrent?id=$id", Lang::T("NFO Found but error"));
        }
    }

    public function edit()
    {
        $id = (int) Input::get("id");
        $nfo = NFODIR . "/$id.nfo";
        if ($this->session["edit_torrents"] == "no") {
            Redirect::autolink(URLROOT."/torrent?id=$id", Lang::T("NFO_PERMISSION"));
        }
        if ((!$this->valid->validId($id)) || (!$contents = file_get_contents($nfo))) {
            Redirect::autolink(URLROOT."/torrent?id=$id", Lang::T("NFO_NOT_FOUND"));
        }
        $data = [
            'id' => $id,
            'title' => "Edid NFO",
            'contents' => $contents,
        ];
        $this->view('nfo/edit', $data, 'user');
    }

    public function submit()
    {
        $id = (int) Input::get("id");
        $nfo = NFODIR . "/$id.nfo";
        if ($this->session["edit_torrents"] == "no") {
            Redirect::autolink(URLROOT."/torrent?id=$id", Lang::T("NFO_PERMISSION"));
        }
        if ((!$this->valid->validId($id)) || (!$contents = file_get_contents($nfo))) {
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
        $nfo = NFODIR . "/$id.nfo";
        if ($this->session["edit_torrents"] == "no") {
            Redirect::autolink(URLROOT."/torrent?id=$id", Lang::T("NFO_PERMISSION"));
        }
        if ((!$this->valid->validId($id)) || (!$contents = file_get_contents($nfo))) {
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