<?php
class Admincategories extends Controller
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
        $query = "SELECT * FROM categories ORDER BY parent_cat ASC, sort_index ASC";
        $sql = DB::run($query);
        $data = [
            'title' => Lang::T("TORRENT_CATEGORIES"),
            'sql' => $sql,
        ];
        $this->view('cats/index', $data, 'admin');
    }

    public function edit()
    {
        $id = (int) $_GET["id"];
        if (!$this->valid->validId($id)) {
            show_error_msg(Lang::T("ERROR"), Lang::T("INVALID_ID"), 1);
        }
        $res = DB::run("SELECT * FROM categories WHERE id=?", [$id]);
        if ($res->rowCount() != 1) {
            show_error_msg(Lang::T("ERROR"), "No category with ID $id.", 1);
        }

        if ($_GET["save"] == '1') {
            $parent_cat = $_POST['parent_cat'];
            if ($parent_cat == "") {
                show_error_msg(Lang::T("ERROR"), "Parent Cat cannot be empty!", 1);
            }
            $name = $_POST['name'];
            if ($name == "") {
                show_error_msg(Lang::T("ERROR"), "Sub cat cannot be empty!", 1);
            }
            $sort_index = $_POST['sort_index'];
            $image = $_POST['image'];
            $parent_cat = $parent_cat;
            $name = $name;
            $sort_index = $sort_index;
            $image = $image;
            DB::run("UPDATE categories SET parent_cat=?, name=?, sort_index=?, image=? WHERE id=?", [$parent_cat, $name, $sort_index, $image, $id]);
            Redirect::autolink(URLROOT . "/admincategories", Lang::T("SUCCESS"), "category was edited successfully!");
        } else {
            $data = [
                'title' => Lang::T("TORRENT_CATEGORIES"),
                'res' => $res,
                'id' => $id,
            ];
            $this->view('cats/edit', $data, 'admin');
        }
    }

    public function delete()
    {
        $id = (int) $_GET["id"];
        if ($_GET["sure"] == '1') {
            if (!$this->valid->validId($id)) {
                Redirect::autolink(URLROOT . "/admincategories", Lang::T("CP_NEWS_INVAILD_ITEM_ID = $newsid"));
            }
            $newcatid = (int) $_POST["newcat"];
            DB::run("UPDATE torrents SET category=$newcatid WHERE category=$id"); //move torrents to a new cat
            DB::run("DELETE FROM categories WHERE id=?", [$id]); //delete old cat
            Redirect::autolink(URLROOT . "/admincategories", Lang::T("Category Deleted OK."));
        } else {
            $data = [
                'title' => Lang::T("TORRENT_CATEGORIES"),
                'id' => $id,
            ];
            $this->view('cats/delete', $data, 'admin');
        }
    }

    public function takeadd()
    {
        $name = $_POST['name'];
        if ($name == "") {
            show_error_msg(Lang::T("ERROR"), "Sub Cat cannot be empty!", 1);
        }
        $parent_cat = $_POST['parent_cat'];
        if ($parent_cat == "") {
            show_error_msg(Lang::T("ERROR"), "Parent Cat cannot be empty!", 1);
        }
        $sort_index = $_POST['sort_index'];
        $image = $_POST['image'];
        $ins = DB::run("INSERT INTO categories (name, parent_cat, sort_index, image) VALUES (?,?,?,?)", [$name, $parent_cat, $sort_index, $image]);
        if ($ins) {
            Redirect::autolink(URLROOT . "/admincategories", Lang::T("Category was added successfully."));
        } else {
            show_error_msg(Lang::T("ERROR"), "Unable to add category", 1);
        }

    }

    public function add()
    {
        $data = [
            'title' => Lang::T("TORRENT_CATEGORIES"),
        ];
        $this->view('cats/add', $data, 'admin');
    }

}