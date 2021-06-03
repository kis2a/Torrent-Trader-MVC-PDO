<?php
class Adminblocks extends Controller
{

    public function __construct()
    {
        Auth::user();
        Auth::isStaff();
        // $this->userModel = $this->model('User');
        $this->logsModel = $this->model('Logs');
        $this->valid = new Validation();
    }

    public function index()
    {
        $enabled = DB::run("SELECT named, name, description, position, sort FROM blocks WHERE enabled=1 ORDER BY position, sort");
        $disabled = DB::run("SELECT named, name, description, position, sort FROM blocks WHERE enabled=0 ORDER BY position, sort");
        $data = [
            'title' => Lang::T("_BLC_MAN_"),
            'enabled' => $enabled,
            'disabled' => $disabled,
        ];
        $this->view('blocks/admin/index', $data, 'admin');
    }

    public function edit()
    {
        if ($_REQUEST["edit"] == "true") {
            $nextleft = DB::run("SELECT position FROM blocks WHERE position='left' AND enabled=1")->rowCount() + 1;
            $nextmiddle = DB::run("SELECT position FROM blocks WHERE position='middle' AND enabled=1")->rowCount() + 1;
            $nextright = DB::run("SELECT position FROM blocks WHERE position='right' AND enabled=1")->rowCount() + 1;

            # Prune Block Cache.
            $TTCache = new Cache();
            $TTCache->Delete("blocks_left");
            $TTCache->Delete("blocks_middle");
            $TTCache->Delete("blocks_right");
            // Delete
            
            if ($_POST["delete"]) {
            foreach ($_POST["delete"] as $delthis) {
            DB::run("DELETE FROM blocks WHERE id=" . sqlesc($delthis));
            }
            Block::resortleft();
            Block::resortmiddle();
            Block::resortright();
            }
            
            
            // Move to left
            if ($this->valid->validId($_GET["left"])) {
                DB::run("UPDATE blocks SET position = 'left', sort = $nextleft WHERE id = " . $_GET["left"]);
                Block::resortmiddle();
                Block::resortright();
            }
            // Move to center
            if ($this->valid->validId($_GET["middle"])) {
                DB::run("UPDATE blocks SET position = 'middle', sort = $nextmiddle WHERE id = " . $_GET["middle"]);
                Block::resortleft();
                Block::resortright();
            }
            // Move to right
            if ($this->valid->validId($_GET["right"])) {
                DB::run("UPDATE blocks SET position = 'right', sort = $nextright WHERE enabled=1 AND id = " . $_GET["right"]);
                Block::resortleft();
                Block::resortmiddle();
            }
            // Move upper
            if ($this->valid->validId($_GET["up"])) {
                $cur = DB::run("SELECT position, sort, id FROM blocks WHERE id = " . $_GET["up"]);
                $curent = $cur->fetch(PDO::FETCH_ASSOC);
                $sort = (int) $_GET["sort"];
                DB::run("UPDATE blocks SET sort = ? WHERE sort = ? AND id != ? AND position = ?", [$sort, $sort - 1, $_GET["up"], $_GET["position"]]);
                DB::run("UPDATE blocks SET sort = " . ($sort - 1) . " WHERE id = " . $_GET["up"]);
            }
            // Move lower
            if ($this->valid->validId($_GET["down"])) {
                $cur = DB::run("SELECT position, sort, id FROM blocks WHERE id = " . $_GET["down"]);
                $curent = $cur->fetch(PDO::FETCH_ASSOC);
                $sort = (int) $_GET["sort"];
                DB::run("UPDATE blocks SET sort = ? WHERE id = ?", [$sort + 1, $_GET["down"]]);
                DB::run("UPDATE blocks SET sort = " . $sort . " WHERE sort = ? AND id != ? AND position = ?", [$sort + 1, $_GET["down"], $_GET["position"]]);
            }
            // Update
            $res = DB::run("SELECT * FROM blocks ORDER BY id");
            if (!$_GET["up"] && !$_GET["down"] && !$_GET["right"] && !$_GET["left"] && !$_GET["middle"]) {
                $update = array();
                while ($upd = $res->fetch(PDO::FETCH_ASSOC)) {
                    $id = $upd["id"];
                    $update[] = "enabled = " . $_POST["enable_" . $upd["id"]];
                    $update[] = "named = '" . $_POST["named_" . $upd["id"]] . "'";
                    $update[] = "description = '" . $_POST["description_" . $upd["id"]] . "'";
                    if (($upd["enabled"] == 0) && ($upd["position"] == "left") && ($_POST["enable_" . $upd["id"]] == 1)) {
                        $update[] = "sort = " . $nextleft;
                    } elseif (($upd["enabled"] == 0) && ($upd["position"] == "middle") && ($_POST["enable_" . $upd["id"]] == 1)) {
                        $update[] = "sort = " . $nextmiddle;
                    } elseif (($upd["enabled"] == 0) && ($upd["position"] == "right") && ($_POST["enable_" . $upd["id"]] == 1)) {
                        $update[] = "sort = " . $nextright;
                    } elseif (($upd["enabled"] == 1) && ($upd["position"] == "left") && ($_POST["enable_" . $upd["id"]] == 0)) {
                        $update[] = "sort = 0";
                    } elseif (($upd["enabled"] == 1) && ($upd["position"] == "middle") && ($_POST["enable_" . $upd["id"]] == 0)) {
                        $update[] = "sort = 0";
                    } elseif (($upd["enabled"] == 1) && ($upd["position"] == "right") && ($_POST["enable_" . $upd["id"]] == 0)) {
                        $update[] = "sort = 0";
                    } else {
                        $update[] = "sort = " . $upd["sort"];
                    }
                    DB::run("UPDATE blocks SET " . implode(", ", $update) . " WHERE id=$id");
                }
            }
            Block::resortleft();
            Block::resortmiddle();
            Block::resortright();
        }

        $res = DB::run("SELECT * FROM blocks ORDER BY enabled DESC, position, sort");
        $data = [
            'title' => Lang::T("_BLC_MAN_"),
            'res' => $res,
        ];
        $this->view('blocks/admin/edit', $data, 'admin');
    }

    public function preview()
    {
        $name = $this->valid->cleanstr($_GET["name"]);
        if (!file_exists(APPROOT . "/views/blocks/{$name}_block.php")) {
            Session::flash('info', "Possible XSS attempt.", URLROOT . "/home");
        }
        $data = [
            'title' => Lang::T("_BLC_PREVIEW_"),
            'name' => $name,
        ];
        $this->view('blocks/admin/preview', $data, 'admin');
        die();
    }

    public function upload()
    {
        $exist = DB::run("SELECT name FROM blocks");
        while ($fileexist = $exist->fetch(PDO::FETCH_ASSOC)) {
            $indb[] = $fileexist["name"] . "_block.php";
        }
        if ($folder = opendir(APPROOT . '/views/blocks')) {
            while (false !== ($file = readdir($folder))) {
                if ($file != "." && $file != ".." && !in_array($file, $indb)) {
                    if (preg_match("/_block.php/i", $file)) {
                        $infolder[] = $file;
                    }

                }
            }
            closedir($folder);
        }

        $nextleft = DB::run("SELECT position FROM blocks WHERE position='left' AND enabled=1")->rowCount() + 1;
        $nextmiddle = DB::run("SELECT position FROM blocks WHERE position='middle' AND enabled=1")->rowCount() + 1;
        $nextright = DB::run("SELECT position FROM blocks WHERE position='right' AND enabled=1")->rowCount() + 1;

        if ($infolder) {
            $data = [
                'title' => Lang::T("_BLC_MAN_"),
                'indb' => $indb,
                'infolder' => $infolder,
            ];
            $this->view('blocks/admin/added', $data, 'admin');
        } else {
            Redirect::autolink(URLROOT . "/adminblocks", "Please upload block to app/views/blocks");
        }

    }

    public function submit()
    {
        if ($_POST["deletepermanent"]) {
            foreach ($_POST["deletepermanent"] as $delpthis) {
                unlink(APPROOT."/views/blocks/" . $delpthis);
                if (file_exists(APPROOT . "/views/blocks/" . $delpthis)) {
                    Session::flash('info', Lang::T("_FAIL_DEL_"), URLROOT . "/adminblocks");
                } else {
                    Session::flash('info', Lang::T("_SUCCESS_DEL_"), URLROOT . "/adminblocks");
                }
            }
        }

        if ($_POST["addnew"]) {
            foreach ($_POST["addnew"] as $addthis) {
                $i = $addthis;
                $addblock = $_POST["addblock_" . $i];
                $wantedname = sqlesc($_POST["wantedname_" . $i]);
                $name = sqlesc(str_replace("_block.php", "", $this->valid->cleanstr($addblock)));
                $description = sqlesc($_POST["wanteddescription_" . $i]);
                $bins = DB::run("INSERT INTO blocks (named, name, description, position, enabled, sort) VALUES ($wantedname, $name, $description, 'left', 0, 0)");
                if ($bins) {
                    Session::flash('info', Lang::T("_SUCCESS_ADD_"), URLROOT . "/adminblocks");
                } else {
                    Session::flash('info', Lang::T("_FAIL_ADD_"), URLROOT . "/adminblocks");
                }
            }
        }

    }

}
