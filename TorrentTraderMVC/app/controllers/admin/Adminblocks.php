<?php
class Adminblocks extends Controller
{

    public function __construct()
    {
        $this->session = Auth::user(_ADMINISTRATOR, 2);
        $this->userModel = $this->model('User');
        $this->logsModel = $this->model('Logs');
        
    }

    public function index()
    {
        $enabled = Blocks::getblock(1);
        $disabled = Blocks::getblock(0);
        $data = [
            'title' => Lang::T("_BLC_MAN_"),
            'enabled' => $enabled,
            'disabled' => $disabled,
        ];
        View::render('blocks/admin/index', $data, 'admin');
    }

    public function edit()
    {
        if ($_REQUEST["edit"] == "true") {
            $nextleft = Blocks::getposition('left');
            $nextmiddle = Blocks::getposition('middle');
            $nextright = Blocks::getposition('right');

            # Prune Block Cache.
            $TTCache = new Cache();
            $TTCache->Delete("blocks_left");
            $TTCache->Delete("blocks_middle");
            $TTCache->Delete("blocks_right");
            // Delete
            
            if ($_POST["delete"]) {
            foreach ($_POST["delete"] as $delthis) {
                Blocks::delete($delthis);
            }
            Blocks::resortleft();
            Blocks::resortmiddle();
            Blocks::resortright();
            }
            
            
            // Move to left
            if (Validate::Id($_GET["left"])) {
                Blocks::update('left', $nextleft, $_GET["left"]);
                Blocks::resortmiddle();
                Blocks::resortright();
            }
            // Move to center
            if (Validate::Id($_GET["middle"])) {
                Blocks::update('left', $nextmiddle, $_GET["middle"]);
                Blocks::resortleft();
                Blocks::resortright();
            }
            // Move to right
            if (Validate::Id($_GET["right"])) {
                Blocks::update('right', $nextright, $_GET["right"]);
                Blocks::resortleft();
                Blocks::resortmiddle();
            }
            // Move upper
            if (Validate::Id($_GET["up"])) {
                $cur = DB::run("SELECT position, sort, id FROM blocks WHERE id = " . $_GET["up"]);
                $curent = $cur->fetch(PDO::FETCH_ASSOC);
                $sort = (int) $_GET["sort"];
                DB::run("UPDATE blocks SET sort = ? WHERE sort = ? AND id != ? AND position = ?", [$sort, $sort - 1, $_GET["up"], $_GET["position"]]);
                DB::run("UPDATE blocks SET sort = " . ($sort - 1) . " WHERE id = " . $_GET["up"]);
            }
            // Move lower
            if (Validate::Id($_GET["down"])) {
                $cur = Blocks::move($_GET["down"]);
                $curent = $cur->fetch(PDO::FETCH_ASSOC);
                $sort = (int) $_GET["sort"];
                DB::run("UPDATE blocks SET sort = ? WHERE id = ?", [$sort + 1, $_GET["down"]]);
                DB::run("UPDATE blocks SET sort = " . $sort . " WHERE sort = ? AND id != ? AND position = ?", [$sort + 1, $_GET["down"], $_GET["position"]]);
            }
            // Update
            $res = Blocks::getall();
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
            Blocks::resortleft();
            Blocks::resortmiddle();
            Blocks::resortright();
        }

        $res = Blocks::getorder();
        $data = [
            'title' => Lang::T("_BLC_MAN_"),
            'res' => $res,
        ];
        View::render('blocks/admin/edit', $data, 'admin');
    }

    public function preview()
    {
        $name = Validate::cleanstr($_GET["name"]);
        if (!file_exists(APPROOT . "/views/blocks/{$name}_block.php")) {
                Redirect::autolink(URLROOT, "Possible XSS attempt.");
        }
        $data = [
            'title' => Lang::T("_BLC_PREVIEW_"),
            'name' => $name,
        ];
        View::render('blocks/admin/preview', $data, 'admin');
        die();
    }

    public function upload()
    {
        $exist = Blocks::getname();
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
/*
        $nextleft = DB::run("SELECT position FROM blocks WHERE position='left' AND enabled=1")->rowCount() + 1;
        $nextmiddle = DB::run("SELECT position FROM blocks WHERE position='middle' AND enabled=1")->rowCount() + 1;
        $nextright = DB::run("SELECT position FROM blocks WHERE position='right' AND enabled=1")->rowCount() + 1;
*/
        if ($infolder) {
            $data = [
                'title' => Lang::T("_BLC_MAN_"),
                'indb' => $indb,
                'infolder' => $infolder,
            ];
            View::render('blocks/admin/added', $data, 'admin');
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
                        Redirect::autolink(URLROOT . '/adminblocks', Lang::T("_FAIL_DEL_"));
                } else {
                        Redirect::autolink(URLROOT . '/adminblocks', Lang::T("_SUCCESS_DEL_"));
                }
            }
        }

        if ($_POST["addnew"]) {
            foreach ($_POST["addnew"] as $addthis) {
                $i = $addthis;
                $addblock = $_POST["addblock_" . $i];
                $wantedname = sqlesc($_POST["wantedname_" . $i]);
                $name = sqlesc(str_replace("_block.php", "", Validate::cleanstr($addblock)));
                $description = sqlesc($_POST["wanteddescription_" . $i]);
                $bins = Blocks::insert($wantedname, $name, $description);
                if ($bins) {
                        Redirect::autolink(URLROOT . '/adminblocks', Lang::T("_SUCCESS_ADD_"));
                } else {
                        Redirect::autolink(URLROOT . '/adminblocks', Lang::T("_FAIL_ADD_"));
                }
            }
        }

    }

}
