<?php
class Admintorrentlang extends Controller
{

    public function __construct()
    {
        $this->session = Auth::user(_MODERATOR, 2);
    }

    public function index()
    {
        $sql = DB::run("SELECT * FROM torrentlang ORDER BY sort_index ASC");
        $title = Lang::T("TORRENT_LANGUAGES");
        $data = [
            'title' => $title,
            'sql' => $sql,
        ];
        View::render('torrentlang/torrentlangview', $data, 'admin');
    }

    public function edit()
    {
        $id = (int) Input::get("id");
        if (!Validate::Id($id)) {
            Redirect::autolink(URLROOT . "/admintorrentlang", Lang::T("INVALID_ID"));
        }
        $res = DB::run("SELECT * FROM torrentlang WHERE id=$id");
        if ($res->rowCount() != 1) {
            Redirect::autolink(URLROOT . "/admintorrentlang", "No Language with ID $id.");
        }
        if ($_GET["save"] == '1') {
            $name = $_POST['name'];
            if ($name == "") {
                Redirect::autolink(URLROOT."/admintorrentlang/edit", "Language cat cannot be empty!");
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
            View::render('torrentlang/torrentlangedit', $data, 'admin');
        }
    }

    public function delete()
    {
        $id = (int) $_GET["id"];
        if ($_GET["sure"] == '1') {
            if (!Validate::Id($id)) {
                Redirect::autolink(URLROOT."/admintorrentlang/delete", "Invalid Language item ID");
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
            View::render('torrentlang/torrentlangdelete', $data, 'admin');
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
        View::render('torrentlang/torrentlangadd', $data, 'admin');
    }

}