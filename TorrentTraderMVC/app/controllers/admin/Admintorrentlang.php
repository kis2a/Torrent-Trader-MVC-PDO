<?php
class Admintorrentlang extends Controller
{

    public function __construct()
    {
        $this->session = (new Auth)->user(_MODERATOR, 2);
        // $this->userModel = $this->model('User');
        $this->logsModel = $this->model('Logs');
        $this->valid = new Validation();
    }

    public function index()
    {
        $sql = DB::run("SELECT * FROM torrentlang ORDER BY sort_index ASC");
        $title = Lang::T("TORRENT_LANGUAGES");
        $data = [
            'title' => $title,
            'sql' => $sql,
        ];
        $this->view('torrentlang/torrentlangview', $data, 'admin');
    }

    public function edit()
    {
        $id = (int) Input::get("id");
        if (!$this->valid->validId($id)) {
            Redirect::autolink(URLROOT . "/admintorrentlang", Lang::T("INVALID_ID"));
        }
        $res = DB::run("SELECT * FROM torrentlang WHERE id=$id");
        if ($res->rowCount() != 1) {
            Redirect::autolink(URLROOT . "/admintorrentlang", "No Language with ID $id.");
        }
        if ($_GET["save"] == '1') {
            $name = $_POST['name'];
            if ($name == "") {
                show_error_msg(Lang::T("ERROR"), "Language cat cannot be empty!", 1);
            }
            $sort_index = $_POST['sort_index'];
            $image = $_POST['image'];
            $name = $name;
            $sort_index = $sort_index;
            $image = $image;
            DB::run("UPDATE torrentlang SET name=?, sort_index=?, image=? WHERE id=?", [$name, $sort_index, $image, $id]);
            Redirect::autolink(URLROOT . "/admintorrentlang/torrentlang", Lang::T("Language was edited successfully."));
        } else {
            $title = Lang::T("TORRENT_LANGUAGES");
            $data = [
                'title' => $title,
                'id' => $id,
                'res' => $res,
            ];
            $this->view('torrentlang/torrentlangedit', $data, 'admin');
        }
    }

    public function delete()
    {
        $id = (int) $_GET["id"];
        if ($_GET["sure"] == '1') {
            if (!$this->valid->validId($id)) {
                show_error_msg(Lang::T("ERROR"), "Invalid Language item ID", 1);
            }
            $newlangid = (int) $_POST["newlangid"];
            DB::run("UPDATE torrents SET torrentlang=$newlangid WHERE torrentlang=$id"); //move torrents to a new cat
            DB::run("DELETE FROM torrentlang WHERE id=$id"); //delete old cat
            Redirect::autolink(URLROOT . "/admintorrentlang", Lang::T("Language Deleted OK."));
        } else {
            $title = Lang::T("TORRENT_LANGUAGES");
            $data = [
                'title' => $title,
                'id' => $id,
            ];
            $this->view('torrentlang/torrentlangdelete', $data, 'admin');
        }
    }

    public function takeadd()
    {
        $name = $_POST['name'];
        if ($name == "") {
            Redirect::autolink(URLROOT . "/admintorrentlang/add", "Name cannot be empty!");
        }
        $sort_index = $_POST['sort_index'];
        $image = $_POST['image'];
        $name = $name;
        $sort_index = $sort_index;
        $image = $image;
        $ins = DB::run("INSERT INTO torrentlang (name, sort_index, image) VALUES (?, ?, ?)", [$name, $sort_index, $image]);
        if ($ins) {
            Redirect::autolink(URLROOT . "/admintorrentlang", Lang::T("Language was added successfully."));
        } else {
            Redirect::autolink(URLROOT . "/admintorrentlang/add", "Unable to add Language");
        }
    }

    public function add()
    {
        $title = Lang::T("TORRENT_LANGUAGES");
        $data = [
            'title' => $title,
        ];
        $this->view('torrentlang/torrentlangadd', $data, 'admin');
    }

}