<?php
class Admincategories
{

    public function __construct()
    {
        $this->session = Auth::user(_MODERATOR, 2);
    }

    public function index()
    {
        $query = "SELECT * FROM categories ORDER BY parent_cat ASC, sort_index ASC";
        $sql = DB::run($query);
        $data = [
            'title' => Lang::T("TORRENT_CATEGORIES"),
            'sql' => $sql,
        ];
        View::render('cats/index', $data, 'admin');
    }

    public function edit()
    {
        $id = (int) $_GET["id"];
        if (!Validate::Id($id)) {
            Redirect::autolink(URLROOT . "/admincategories", Lang::T("INVALID_ID"));
        }
        $res = DB::run("SELECT * FROM categories WHERE id=?", [$id]);
        if ($res->rowCount() != 1) {
            Redirect::autolink(URLROOT . "/admincategories", "No category with ID $id.");
        }

        if ($_GET["save"] == '1') {
            $parent_cat = $_POST['parent_cat'];
            if ($parent_cat == "") {
                Redirect::autolink(URLROOT . "/admincategories/edit", "Parent Cat cannot be empty!");
            }
            $name = $_POST['name'];
            if ($name == "") {
                Redirect::autolink(URLROOT . "/admincategories/edit", "Sub cat cannot be empty!");
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
            View::render('cats/edit', $data, 'admin');
        }
    }

    public function delete()
    {
        $id = (int) $_GET["id"];
        if ($_GET["sure"] == '1') {
            if (!Validate::Id($id)) {
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
            View::render('cats/delete', $data, 'admin');
        }
    }

    public function takeadd()
    {
        $name = $_POST['name'];
        if ($name == "") {
            Redirect::autolink(URLROOT . "/admincategories/add", "Sub Cat cannot be empty!");
        }
        $parent_cat = $_POST['parent_cat'];
        if ($parent_cat == "") {
            Redirect::autolink(URLROOT . "/admincategories/add", "Parent Cat cannot be empty!");
        }
        $sort_index = $_POST['sort_index'];
        $image = $_POST['image'];
        $ins = DB::run("INSERT INTO categories (name, parent_cat, sort_index, image) VALUES (?,?,?,?)", [$name, $parent_cat, $sort_index, $image]);
        if ($ins) {
            Redirect::autolink(URLROOT . "/admincategories", Lang::T("Category was added successfully."));
        } else {
            Redirect::autolink(URLROOT . "/admincategories/add", "Unable to add category");
        }

    }

    public function add()
    {
        $data = [
            'title' => Lang::T("TORRENT_CATEGORIES"),
        ];
        View::render('cats/add', $data, 'admin');
    }

}