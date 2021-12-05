<?php
class Torrent
{
    public function __construct()
    {
        $this->session = Auth::user(0, 1);
    }

    public function index()
    {
        //check permissions
        if ($_SESSION["view_torrents"] != "yes" && Config::TT()['MEMBERSONLY']) {
            Redirect::autolink(URLROOT, Lang::T("NO_TORRENT_VIEW"));
        }
        $id = (int) $_GET["id"];
        if (!Validate::Id($id)) {
            Redirect::autolink(URLROOT, Lang::T("THATS_NOT_A_VALID_ID"));
        }
        //GET ALL MYSQL VALUES FOR THIS TORRENT
        $res = DB::run("SELECT torrents.anon, torrents.seeders, torrents.tube, torrents.banned, torrents.leechers, torrents.info_hash, torrents.filename, torrents.nfo, torrents.last_action, torrents.numratings, torrents.name, torrents.tmdb, torrents.tmdb, torrents.owner, torrents.save_as, torrents.descr, torrents.visible, torrents.size, torrents.added, torrents.views, torrents.hits, torrents.times_completed, torrents.id, torrents.type, torrents.external, torrents.image1, torrents.image2, torrents.announce, torrents.numfiles, torrents.freeleech, torrents.vip, IF(torrents.numratings < 2, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, torrents.numratings, categories.name AS cat_name, torrentlang.name AS lang_name, torrentlang.image AS lang_image, categories.parent_cat as cat_parent, users.username, users.privacy FROM torrents LEFT JOIN categories ON torrents.category = categories.id LEFT JOIN torrentlang ON torrents.torrentlang = torrentlang.id LEFT JOIN users ON torrents.owner = users.id WHERE torrents.id = $id");
        $row = $res->fetch(PDO::FETCH_ASSOC);
        //DECIDE IF TORRENT EXISTS
        if (!$row || ($row["banned"] == "yes" && $_SESSION["edit_torrents"] == "no")) {
            Redirect::autolink(URLROOT, Lang::T("TORRENT_NOT_FOUND"));
        }

        if(!empty($row["tmdb"]) && in_array($row["cat_parent"], SerieCats)) {
            $id_tmdb = TMDBS::getId($row["tmdb"]);
            $total = DB::run("SELECT count(*) FROM tmdb WHERE id_tmdb = ? AND type = ?", [$id_tmdb, 'show'])->fetchColumn();
            if($total == 0) {
                TMDBS::createSerie($id_tmdb, $id, $row["tmdb"]);
            }
        } elseif(!empty($row["tmdb"]) && in_array($row["cat_parent"], MovieCats)) {
            $id_tmdb = TMDBS::getId($row["tmdb"]);
            $total = DB::run("SELECT count(*) FROM tmdb WHERE id_tmdb = ? AND type = ?", [$id_tmdb, 'movie'])->fetchColumn();
            if($total == 0) {
                TMDBS::createFilm($id_tmdb, $id, $row["tmdb"]);
            }
        }

        // vip
        $vip = $row["vip"];
        if ($vip == "yes") {
            $vip = "<b>Yes</b>";
        } else {
            $vip = "<b>No</b>";
        }
        // freeleech
        $freeleech = $row["freeleech"];
        if ($freeleech == 1) {
            $freeleech = "<font color=green><b>Yes</b></font>";
        } else {
            $freeleech = "<font color=red><b>No</b></font>";
        }
        //torrent is availiable so do some stuff
        if ($_GET["hit"]) {
            DB::run("UPDATE torrents SET views = views + 1 WHERE id = $id");
            Redirect::to(URLROOT . "/torrent?id=$id");
            die;
        }
        if ($_GET["bump"]) {
            DB::run("UPDATE torrents SET added = NOW() WHERE id = $id");
            Redirect::autolink(URLROOT . "/torrent?id=$id", Lang::T("Bumped Torrent"));
        }

        $ts = TimeDate::modify('date', $row['last_action'], '+2 day');
        if ($ts > TT_DATE) {
            $scraper = "<br>
            <br><b>" . Lang::T("EXTERNAL_TORRENT") . "</b>
            <font  size='4' color=#ff9900><b>Stats Recently Updated</b></font>";
        } else {
            $scraper = "
            <br><b>" . Lang::T("EXTERNAL_TORRENT") . "</b>
            <form action='" . URLROOT . "/scrape/external?id=" . $id . "' method='post'>
            <button type='submit' class='btn ttbtn center-block' value=''>" . Lang::T("Update Stats") . "</button>
            </form>";
        }

        if ($_SESSION["id"] == $row["owner"] || $_SESSION["edit_torrents"] == "yes") {
            $owned = 1;
        } else {
            $owned = 0;
        }
        $char1 = 50; //cut length
        $shortname = CutName(htmlspecialchars($row["name"]), $char1);
        // Calculate local torrent speed test
        if ($row["leechers"] >= 1 && $row["seeders"] >= 1 && $row["external"] != 'yes') {
            $speedQ = DB::run("SELECT (SUM(p.downloaded)) / (UNIX_TIMESTAMP('" . TimeDate::get_date_time() . "') - UNIX_TIMESTAMP(added)) AS totalspeed FROM torrents AS t LEFT JOIN peers AS p ON t.id = p.torrent WHERE p.seeder = 'no' AND p.torrent = '$id' GROUP BY t.id ORDER BY added ASC LIMIT 15");
            $a = $speedQ->fetch(PDO::FETCH_ASSOC);
            $totalspeed = mksize($a["totalspeed"]) . "/s";
        } else {
            $totalspeed = Lang::T("NO_ACTIVITY");
        }
        $torrent1 = Torrents::getAll($id);
        $title = Lang::T("DETAILS_FOR_TORRENT") . " \"" . $row["name"] . "\"";
        $data = [
            'title' => $title,
            'row' => $row,
            'owned' => $owned,
            'shortname' => $shortname,
            'speed' => $totalspeed,
            'id' => $id,
            'selecttor' => $torrent1,
            'scraper' => $scraper,
        ];
        View::render('torrent/read', $data, 'user');
    }

    public function edit()
    {
        $id = (int) $_REQUEST["id"];
        if (!Validate::Id($id)) {
            Redirect::autolink(URLROOT, Lang::T("INVALID_ID"));
        }
        $row = DB::run("SELECT `owner` FROM `torrents` WHERE id=?", [$id])->fetch();
        if ($_SESSION["edit_torrents"] == "no" && $_SESSION['id'] != $row['owner']) {
            Redirect::autolink(URLROOT . "/torrent?id=$id", Lang::T("NO_TORRENT_EDIT_PERMISSION"));
        }

        //GET DATA FROM DB
        $row = DB::run("SELECT * FROM torrents WHERE id =?", [$id])->fetch();
        if (!$row) {
            Redirect::autolink(URLROOT . "/torrent?id=$id", Lang::T("TORRENT_ID_GONE"));
        }
        //UPDATE CATEGORY DROPDOWN
        $catdropdown = "<select name=\"type\">\n";
        $cats = Catagorie::genrelist();
        foreach ($cats as $catdropdownubrow) {
            $catdropdown .= "<option value=\"" . $catdropdownubrow["id"] . "\"";
            if ($catdropdownubrow["id"] == $row["category"]) {
                $catdropdown .= " selected=\"selected\"";
            }
            $catdropdown .= ">" . htmlspecialchars($catdropdownubrow["parent_cat"]) . ": " . htmlspecialchars($catdropdownubrow["name"]) . "</option>\n";
        }
        $catdropdown .= "</select>\n";
        //UPDATE TORRENTLANG DROPDOWN
        $langdropdown = "<select name=\"language\"><option value='0'>Unknown</option>\n";
        $lang = Lang::langlist();
        foreach ($lang as $lang) {
            $langdropdown .= "<option value=\"" . $lang["id"] . "\"";
            if ($lang["id"] == $row["torrentlang"]) {
                $langdropdown .= " selected=\"selected\"";
            }
            $langdropdown .= ">" . htmlspecialchars($lang["name"]) . "</option>\n";
        }
        $langdropdown .= "</select>\n";
        $char1 = 55;
        $shortname = CutName(htmlspecialchars($row["name"]), $char1);
        if ($_GET["edited"]) { // todo
            Redirect::autolink(URLROOT . "/torrent?id=$id", Lang::T("TORRENT_EDITED_OK"));
        }
        $torrent1 = Torrents::getAll($id);
        $title = Lang::T("EDIT_TORRENT") . " \"$shortname\"";
        $data = [
            'title' => $title,
            'row' => $row,
            'catdrop' => $catdropdown,
            'shortname' => $shortname,
            'langdrop' => $langdropdown,
            'id' => $id,
            'selecttor' => $torrent1,
        ];
        View::render('torrent/edit', $data, 'user');
    }

    public function submit()
    {
        $id = (int) $_GET["id"];
        if (!Validate::Id($id)) {
            Redirect::autolink(URLROOT, Lang::T("INVALID_ID"));
        }
        $row = DB::run("SELECT `owner` FROM `torrents` WHERE id=?", [$id])->fetch();
        if ($_SESSION["edit_torrents"] == "no" && $_SESSION['id'] != $row['owner']) {
            Redirect::autolink(URLROOT, Lang::T("NO_TORRENT_EDIT_PERMISSION"));
        }
        //GET DATA FROM DB
        $row = DB::run("SELECT * FROM torrents WHERE id =?", [$id])->fetch();
        if (!$row) {
            Redirect::autolink(URLROOT . "/torrent?id=$id", Lang::T("TORRENT_ID_GONE"));
        }
        $torrent_dir = UPLOADDIR."/torrents";
        $nfo_dir = UPLOADDIR."/nfos";
        //DO THE SAVE TO DB HERE
        if (Input::exist()) {
            $updateset = array();
            $nfoaction = $_POST['nfoaction'];
            if ($nfoaction == "update") {
                $nfofile = $_FILES['nfofile'];
                if (!$nfofile) {
                    die("No data " . var_dump($_FILES));
                }
                if ($nfofile['size'] > 65535) {
                    Redirect::autolink(URLROOT . "/torrent?id=$id", "NFO is too big! Max 65,535 bytes.");
                }
                $nfofilename = $nfofile['tmp_name'];
                if (@is_uploaded_file($nfofilename) && @filesize($nfofilename) > 0) {
                    @move_uploaded_file($nfofilename, "$nfo_dir/$id.nfo");
                    $updateset[] = "nfo = 'yes'";
                } //success
            } elseif ($nfoaction == "delete") {
                unlink(UPLOADDIR."/nfos/$id");
                $updateset[] = "nfo = 'no'";
            }
            if (!empty($_POST["name"])) {
                $updateset[] = "name = " . sqlesc($_POST["name"]);
            }
            // TMDB
            if ($_POST['tmdb'] != $row['tmdb']) {
                $updateset[] = "tmdb = " . sqlesc($_POST["tmdb"]);
                $TTCache = new Cache();
                $TTCache->Delete("tmdb/film/$id");
            }
            $updateset[] = "descr = " . sqlesc($_POST["descr"]);
            $updateset[] = "category = " . (int) $_POST["type"];
            if ($_SESSION["class"] >= 5) { // lowest class to make torrent sticky.
                if ($_POST["sticky"] == "yes") {
                    $updateset[] = "sticky = 'yes'";
                } else {
                    $updateset[] = "sticky = 'no'";
                }
            }
            $updateset[] = "torrentlang = " . (int) $_POST["language"];

            if ($_SESSION["edit_torrents"] == "yes") {
                if ($_POST["banned"]) {
                    $updateset[] = "banned = 'yes'";
                    $_POST["visible"] = 0;
                } else {
                    $updateset[] = "banned = 'no'";
                }
            }

            $updateset[] = "visible = '" . ($_POST["visible"] ? "yes" : "no") . "'";

            // youtube
            if (!empty($_POST['tube'])) {
                $tube = $_POST['tube'];
            }

            $updateset[] = "tube = " . sqlesc($tube);

            if ($_SESSION["edit_torrents"] == "yes") {
                $updateset[] = "freeleech = '" . ($_POST["freeleech"] ? "1" : "0") . "'";
            }

            $updateset[] = "vip = '" . ($_POST["vip"] ? "yes" : "no") . "'";
            $updateset[] = "anon = '" . ($_POST["anon"] ? "yes" : "no") . "'";

            //update images
            $img1action = $_POST['img1action'];
            if ($img1action == "update") {
                $updateset[] = "image1 = " . sqlesc(uploadimage(0, $row["image1"], $id));
            }

            if ($img1action == "delete") {
                if ($row['image1']) {
                    $del = unlink(UPLOADDIR . "/images/$row[image1]");
                    $updateset[] = "image1 = ''";
                }
            }

            $img2action = $_POST['img2action'];
            if ($img2action == "update") {
                $updateset[] = "image2 = " . sqlesc(uploadimage(1, $row["image2"], $id));
            }

            if ($img2action == "delete") {
                if ($row['image2']) {
                    $del = unlink(UPLOADDIR . "/images/$row[image2]");
                    $updateset[] = "image2 = ''";
                }
            }

            DB::run("UPDATE torrents SET " . join(",", $updateset) . " WHERE id = $id");
            Logs::write("Torrent $id (" . htmlspecialchars($_POST["name"]) . ") was edited by $_SESSION[username]");
            Redirect::to(URLROOT . "/torrent?id=$id");
            die();
        }
    }

    public function delete()
    {
        $id = (int) $_GET["id"];
        if (!Validate::Id($id)) {
            Redirect::autolink(URLROOT . "/torrent?id=$id", Lang::T("INVALID_ID"));
        }
        $row = DB::run("SELECT `owner` FROM `torrents` WHERE id=?", [$id])->fetch();
        if ($_SESSION["delete_torrents"] == "no" && $_SESSION['id'] != $row['owner']) {
            Redirect::autolink(URLROOT . "/torrent?id=$id", Lang::T("NO_TORRENT_DELETE_PERMISSION"));
        }
        $owner = $row['owner'];
        //GET DATA FROM DB
        $row = DB::run("SELECT * FROM torrents WHERE id =?", [$id])->fetch();
        if (!$row) {
            Redirect::autolink(URLROOT . "/torrent?id=$id", Lang::T("TORRENT_ID_GONE"));
        }
        $torrname = $row['owner'];
        $char1 = 55;
        $shortname = CutName(htmlspecialchars($row["name"]), $char1);
        $title = Lang::T("DELETE_TORRENT") . " \"$shortname\"";
        $data = [
            'title' => $title,
            'owner' => $owner,
            'id' => $id,
            'name' => $torrname,
        ];
        View::render('torrent/delete', $data, 'user');
    }

    public function deleteok()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $torrentid = (int) $_POST["torrentid"];
            $delreason = $_POST["delreason"];
            $torrentname = $_POST["torrentname"];
            if (!Validate::Id($torrentid)) {
                Redirect::autolink(URLROOT . "/torrent/delete?id=$torrentid", Lang::T("INVALID_TORRENT_ID"));
            }
            if (!$delreason) {
                Redirect::autolink(URLROOT . "/torrent/delete?id=$torrentid", Lang::T("MISSING_FORM_DATA"));
            }
            Torrents::deletetorrent($torrentid);
            DB::run("SELECT `owner` FROM `torrents` WHERE id=?", [$torrentid])->fetch();
            Logs::write($_SESSION['username'] . " has deleted torrent: ID:$torrentid - " . htmlspecialchars($torrentname) . " - Reason: " . htmlspecialchars($delreason));
            if ($_SESSION['id'] != $torrentid) {
                $delreason = $_POST["delreason"];
                DB::run("INSERT INTO messages (sender, receiver, added, subject, msg, unread, location)
                         VALUES(?,?,?,?,?,?,?)",
                    [0, $torrentid, TimeDate::get_date_time(), 'Your torrent ' . $torrentname . ' has been deleted by ' . $_SESSION['username'], $torrentname . ' was deleted by ' . $_SESSION['username'] . ' Reason: $delreason', 'yes', 'in']);
            }
            Redirect::autolink(URLROOT . "/peers/uploaded?id=$_SESSION[id]", htmlspecialchars($torrentname) . " " . Lang::T("HAS_BEEN_DEL_DB"));
            die;
        }
    }

    public function torrentfilelist()
    {
        $id = (int) $_GET["id"];
        if (!Validate::Id($id)) {
            Redirect::autolink(URLROOT, Lang::T("THATS_NOT_A_VALID_ID"));
        }
        //check permissions
        if ($_SESSION["view_torrents"] == "no") {
            Redirect::autolink(URLROOT, Lang::T("NO_TORRENT_VIEW"));
        }
        //GET ALL MYSQL VALUES FOR THIS TORRENT
        $res = DB::run("SELECT torrents.anon, torrents.seeders, torrents.banned, torrents.leechers, torrents.info_hash, torrents.filename, torrents.nfo, torrents.last_action, torrents.numratings, torrents.name, torrents.owner, torrents.save_as, torrents.descr, torrents.visible, torrents.size, torrents.added, torrents.views, torrents.hits, torrents.times_completed, torrents.id, torrents.type, torrents.external, torrents.image1, torrents.image2, torrents.announce, torrents.numfiles, torrents.freeleech, IF(torrents.numratings < 2, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, torrents.numratings, categories.name AS cat_name, torrentlang.name AS lang_name, torrentlang.image AS lang_image, categories.parent_cat as cat_parent, users.username, users.privacy FROM torrents LEFT JOIN categories ON torrents.category = categories.id LEFT JOIN torrentlang ON torrents.torrentlang = torrentlang.id LEFT JOIN users ON torrents.owner = users.id WHERE torrents.id = $id");
        $row = $res->fetch(PDO::FETCH_ASSOC);
        $fres = DB::run("SELECT * FROM `files` WHERE `torrent` = $id ORDER BY `path` ASC");
        $char1 = 50; //cut length
        $shortname = CutName(htmlspecialchars($row["name"]), $char1);
        $title = Lang::T("TORRENT_DETAILS_FOR") . " \"" . $shortname . "\"";
        $data = [
            'title' => $title,
            'row' => $row,
            'shortname' => $shortname,
            'id' => $id,
            'name' => $row["name"],
            'size' => $row["size"],
            'fres' => $fres,
        ];
        View::render('torrent/filelist', $data, 'user');
    }

    public function torrenttrackerlist()
    {
        $id = (int) $_GET["id"];
        if (!Validate::Id($id)) {
            Redirect::autolink(URLROOT . "/torrent?id=$id", Lang::T("THATS_NOT_A_VALID_ID"));
        }
        if ($_SESSION["view_torrents"] == "no") {
            Redirect::autolink(URLROOT, Lang::T("NO_TORRENT_VIEW"));
        }
        //GET ALL MYSQL VALUES FOR THIS TORRENT
        $res = DB::run("SELECT torrents.anon, torrents.seeders, torrents.banned, torrents.leechers, torrents.info_hash, torrents.filename, torrents.nfo, torrents.last_action, torrents.numratings, torrents.name, torrents.owner, torrents.save_as, torrents.descr, torrents.visible, torrents.size, torrents.added, torrents.views, torrents.hits, torrents.times_completed, torrents.id, torrents.type, torrents.external, torrents.image1, torrents.image2, torrents.announce, torrents.numfiles, torrents.freeleech, IF(torrents.numratings < 2, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, torrents.numratings, categories.name AS cat_name, torrentlang.name AS lang_name, torrentlang.image AS lang_image, categories.parent_cat as cat_parent, users.username, users.privacy FROM torrents LEFT JOIN categories ON torrents.category = categories.id LEFT JOIN torrentlang ON torrents.torrentlang = torrentlang.id LEFT JOIN users ON torrents.owner = users.id WHERE torrents.id = $id");
        $title = Lang::T("Tracker List");
        $data = [
            'title' => $title,
            'id' => $id,
            'res' => $res,
        ];
        View::render('torrent/trackerlist', $data, 'user');
    }

    public function reseed()
    {
        if ($_SESSION["view_torrents"] == "no") {
            Redirect::autolink(URLROOT, Lang::T("NO_TORRENT_VIEW"));
        }
        $id = (int) $_GET["id"];
        if (isset($_COOKIE["reseed$id"])) {
            Redirect::autolink(URLROOT, Lang::T("RESEED_ALREADY_ASK"));
        }
        $res = DB::run("SELECT `owner`, `banned`, `external` FROM `torrents` WHERE `id` = $id");
        $row = $res->fetch(PDO::FETCH_ASSOC);
        if (!$row || $row["banned"] == "yes" || $row["external"] == "yes") {
            Redirect::autolink(URLROOT, Lang::T("TORRENT_NOT_FOUND"));
        }
        $res2 = DB::run("SELECT users.id FROM completed LEFT JOIN users ON completed.userid = users.id WHERE users.enabled = 'yes' AND users.status = 'confirmed' AND completed.torrentid = $id");
        $message = sprintf(Lang::T('RESEED_MESSAGE'), $_SESSION['username'], URLROOT, $id);
        while ($row2 = $res2->fetch(PDO::FETCH_ASSOC)) {
            DB::run("INSERT INTO `messages` (`subject`, `sender`, `receiver`, `added`, `msg`) VALUES (?,?,?,?,?)", [Lang::T("RESEED_MES_SUBJECT"), $_SESSION['id'], $row2['id'], TimeDate::get_date_time(), $message]);
        }
        if ($row["owner"] && $row["owner"] != $_SESSION["id"]) {
            DB::run("INSERT INTO `messages` (`subject`, `sender`, `receiver`, `added`, `msg`) VALUES (?,?,?,?,?)", ['Torrent Reseed Request', $_SESSION['id'], $row['owner'], TimeDate::get_date_time(), $message]);
        }
        setcookie("reseed$id", $id, time() + 86400, '/');
        Redirect::autolink(URLROOT, Lang::T("RESEED_SENT"));
    }

}