<?php
class Nfo extends Controller
{

    public function __construct()
    {
        Auth::user();
        $this->torrentModel = $this->model('Torrents');
        $this->valid = new Validation();
        $this->logsModel = $this->model('Logs');
    }

    public function index()
    {
        $id = (int) Input::get("id");
        if ($_SESSION["view_torrents"] == "no") {
            Session::flash('info', "You do not have permission to view nfo's", URLROOT."/torrent?id=$id");
        }
        if (!$id) {
            Session::flash('info', Lang::T("ID_NOT_FOUND_MSG_VIEW"), URLROOT."/torrent?id=$id");
        }

        $query = DB::run("SELECT name, nfo FROM torrents WHERE id=?", [$id]);
        $res = $query->fetch(PDO::FETCH_ASSOC);
        if ($res["nfo"] != "yes") {
            Session::flash('info', Lang::T("NO_NFO"), URLROOT."/torrent?id=$id");
        }

        if ($res["nfo"] == "yes") {
            $char1 = 55; //cut length (cutname func is in header.php)
            $shortname = CutName(htmlspecialchars($res["name"]), $char1);
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
            Session::flash('info', Lang::T("NFO Found but error"), URLROOT."/torrent?id=$id");
        }
    }

    public function edit()
    {
        error_reporting(0);
        $id = (int) $this->valid->cleanstr($_REQUEST["id"]);
        $nfo = NFODIR . "/$id.nfo";
        if ($_SESSION["edit_torrents"] == "no") {
            Session::flash('info', Lang::T("NFO_PERMISSION"), URLROOT."/torrent?id=$id");
        }
        if ((!$this->valid->validId($id)) || (!$contents = file_get_contents($nfo))) {
            Session::flash('info', Lang::T("NFO_NOT_FOUND"), URLROOT."/torrent?id=$id");
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
        $id = $this->valid->cleanstr($_REQUEST["id"]);
        $nfo = NFODIR . "/$id.nfo";
        if ($_SESSION["edit_torrents"] == "no") {
            Session::flash('info', Lang::T("NFO_PERMISSION"), URLROOT."/torrent?id=$id");
        }
        if ((!$this->valid->validId($id)) || (!$contents = file_get_contents($nfo))) {
            Session::flash('info', Lang::T("NFO_NOT_FOUND"), URLROOT."/torrent?id=$id");
        }
        if (is_file($nfo)) {
            file_put_contents($nfo, $_POST['content']);
            Logs::write("NFO ($id) was updated by $_SESSION[username].");
            Session::flash('info', Lang::T("NFO_UPDATED"), URLROOT."/torrent?id=$id");
        }else {
            Session::flash('info', sprintf(Lang::T("Problem editing"), $id), URLROOT."/nfo/edit?id=$id");
        }
    }

    public function delete()
    {
        $id = (int) $this->valid->cleanstr($_REQUEST["id"]);
        $nfo = NFODIR . "/$id.nfo";
        if ($_SESSION["edit_torrents"] == "no") {
            Session::flash('info', Lang::T("NFO_PERMISSION"), URLROOT."/torrent?id=$id");
        }
        if ((!$this->valid->validId($id)) || (!$contents = file_get_contents($nfo))) {
            Session::flash('info', Lang::T("NFO_NOT_FOUND"), URLROOT."/torrent?id=$id");
        }
        $reason = htmlspecialchars($_POST["reason"]);
        if (get_row_count("torrents", "WHERE `nfo` = 'yes' AND `id` = $id")) {
            unlink($nfo);
            Logs::write("NFO ($id) was deleted by $_SESSION[username] $reason");
            DB::run("UPDATE `torrents` SET `nfo` = 'no' WHERE `id` = $id");
            Session::flash('info', Lang::T("NFO_DELETED"), URLROOT."/torrent?id=$id");
        } else {
            Session::flash('info', sprintf(Lang::T("NFO_NOT_EXIST"), $id), URLROOT."/torrent?id=$id");
        }
    }

}