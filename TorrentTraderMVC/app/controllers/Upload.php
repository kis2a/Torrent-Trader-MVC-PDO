<?php

class Upload extends Controller
{
    public function __construct()
    {
        Auth::user();
        $this->userModel = $this->model('User');
        $this->pdo = new Database();
        $this->valid = new Validation();
        $this->countriesModel = $this->model('Countries');
        $this->logsModel = $this->model('Logs');
    }

    public function index()
    {
        if ($_SESSION["can_upload"] == "no") {
            Session::flash('info', Lang::T("UPLOAD_NO_PERMISSION"), URLROOT . '/home');
        }
        if (UPLOADERSONLY && $_SESSION["class"] < 4) {
            Session::flash('info', Lang::T("UPLOAD_ONLY_FOR_UPLOADERS"), URLROOT . '/home');
        }
        $announce_urls = explode(",", strtolower(ANNOUNCELIST));
        $data = [
            'title' => Lang::T("UPLOAD"),
            'announce_urls' => $announce_urls,
        ];
        $this->view('torrent/upload', $data, 'user');
    }

    public function submit()
    {
        if ($_POST["takeupload"] == "yes") {
            // Check form data.
            if (!isset($_POST['type'], $_POST['name'])) {
                $message = Lang::T('MISSING_FORM_DATA');
            }
            $tupload = new Tupload('torrent');
            if (($num = $tupload->getError())) {
                Session::flash('info', Lang::T("UPLOAD_ERR[$num]"), URLROOT . '/upload');
            }
            if (!($fname = $tupload->getName())) {
                $message = Lang::T("EMPTY_FILENAME");
            }

            $nfo = 'no';
            if ($_FILES['nfo']['size'] != 0) {
                $nfofile = $_FILES['nfo'];
                if ($nfofile['name'] == '') {
                    $message = Lang::T("NO_NFO_UPLOADED");
                }
                if (!preg_match('/^(.+)\.nfo$/si', $nfofile['name'], $fmatches)) {
                    $message = Lang::T("UPLOAD_NOT_NFO");
                }
                if ($nfofile['size'] == 0) {
                    $message = Lang::T("NO_NFO_SIZE");
                }
                if ($nfofile['size'] > 65535) {
                    $message = Lang::T("NFO_UPLOAD_SIZE");
                }
                $nfofilename = $nfofile['tmp_name'];
                if (($num = $_FILES['nfo']['error'])) {
                    $message = Lang::T("UPLOAD_ERR[$num]");
                }
                $nfo = 'yes';
            }

            $descr = $_POST["descr"];
            if (!$descr) {
                $descr = Lang::T("UPLOAD_NO_DESC");
            }
            $vip = $_POST["vip"];
            $free = $_POST["free"];
            if (!$free) {
                $free = 0;
            }
            $langid = (int) $_POST["lang"];
            $catid = (int) $_POST["type"];

            if (!$this->valid->validId($catid)) {
                $message = Lang::T("UPLOAD_NO_CAT");
            }
            if (!empty($_POST['tube'])) {
                $tube = unesc($_POST['tube']);
            }
            if (!$this->valid->validFilename($fname)) {
                $message = Lang::T("UPLOAD_INVALID_FILENAME");
            }

            if (!preg_match('/^(.+)\.torrent$/si', $fname, $matches)) {
                $message = Lang::T("UPLOAD_INVALID_FILENAME_NOT_TORRENT");
            }

            $shortfname = $torrent = $matches[1];

            if (!empty($_POST["name"])) {
                $name = $_POST["name"];
            }
            if (!empty($_POST['imdb'])) {
                $imdb = $_POST['imdb'];
            }
            if ($message) { // remember to show msg lol
                Session::flash('warning', $message, URLROOT . '/upload');
            }
            if (!$message) {
                //parse torrent file
                $torrent_dir = TORRENTDIR;
                $nfo_dir = NFODIR;
                //if(!copy($f, "$torrent_dir/$fname"))
                if (!($tupload->move("$torrent_dir/$fname"))) {
                    Session::flash('info', Lang::T("ERROR") . ": " . Lang::T("UPLOAD_COULD_NOT_BE_COPIED") . " $torrent_dir - $fname", URLROOT . '/upload');
                }

                $torInfo = new Parse();
                $tor = $torInfo->torr("$torrent_dir/$fname");

                $announce = $tor[0];
                $infohash = $tor[1];
                $creationdate = $tor[2];
                $internalname = $tor[3];
                $torrentsize = $tor[4];
                $filecount = $tor[5];
                $annlist = $tor[6];
                $comment = $tor[7];
                $filelist = $tor[8];

                //if externals is turned off
                $external = $announce !== ANNOUNCELIST ? "yes" : "no";
                if (!ALLOWEXTERNAL && $external == 'yes') {
                    $message = Lang::T("UPLOAD_NO_TRACKER_ANNOUNCE");
                }

            }
            if ($_SESSION['message']) {
                @$tupload->remove();
                //@unlink($tmpname);
                @unlink("$nfo_dir/$nfofilename");
                Session::flash('info', Lang::T("UPLOAD_FAILED"), URLROOT . '/upload');
            }

            //release name check and adjust
            if ($name == "") {
                $name = $internalname;
            }
            $name = str_replace(".torrent", "", $name);
            $name = str_replace("_", " ", $name);

            //upload images
            $allowed_types = ALLOWEDIMAGETYPES;

            $inames = array();
            for ($x = 0; $x < 2; $x++) {
                if (!($_FILES['image' . $x]['name'] == "")) {
                    $y = $x + 1;
                    if ($_FILES['image$x']['size'] > IMAGEMAXFILESIZE) {
                        Session::flash('info', Lang::T("INVAILD_FILE_SIZE_IMAGE"), URLROOT . '/upload');
                    }
                    $uploaddir = TORRENTDIR . '/images/';
                    $ifile = $_FILES['image' . $x]['tmp_name'];
                    $im = getimagesize($ifile);
                    if (!$im[2]) {
                        Session::flash('info', sprintf(Lang::T("INVALID_IMAGE")), URLROOT . '/upload');
                    }
                    if (!array_key_exists($im['mime'], $allowed_types)) {
                        Session::flash('info', Lang::T("INVALID_FILETYPE_IMAGE"), URLROOT . '/upload');
                    }
                    $row = DB::run("SHOW TABLE STATUS LIKE 'torrents'")->fetch();
                    $next_id = $row['Auto_increment'];
                    $ifilename = $next_id . $x . $allowed_types[$im['mime']];
                    $copy = copy($ifile, $uploaddir . $ifilename);
                    if (!$copy) {
                        Session::flash('info', sprintf(Lang::T("IMAGE_UPLOAD_FAILED")), URLROOT . '/upload');
                    }
                    $inames[] = $ifilename;
                }

            }
            //end upload images

            //anonymous upload
            $anonyupload = $_POST["anonycheck"];
            if ($anonyupload == "yes") {
                $anon = "yes";
            } else {
                $anon = "no";
            }

            $filecounts = (int) $filecount;
            try {
                DB::run("
                    INSERT INTO torrents (filename, owner, name, vip, descr, image1, image2, category, tube, added, info_hash, size, numfiles, save_as, announce, external, nfo, torrentlang, anon, last_action, freeleech, imdb)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                    [$fname, $_SESSION['id'], $name, $vip, $descr, $inames[0], $inames[1], $catid, $tube, TimeDate::get_date_time(), $infohash, $torrentsize, $filecounts, $fname, $announce, $external, $nfo, $langid, $anon, TimeDate::get_date_time(), $free, $imdb]);
            } catch (PDOException $e) {
                rename("$torrent_dir/$fname", "$torrent_dir/duplicate.torrent"); // todo
                Redirect::to(URLROOT . '/exceptions');
            }
            $id = DB::lastInsertId();

            if ($id == 0) {
                unlink("$torrent_dir/$fname");
                Session::flash('info', Lang::T("UPLOAD_NO_ID"), URLROOT . "/upload");
            }

            rename("$torrent_dir/$fname", "$torrent_dir/$id.torrent");

            if (is_array($filelist)) {
                foreach ($filelist as $file) {
                    $dir = '';
                    $size = $file["length"];
                    $count = count($file["path"]);
                    for ($i = 0; $i < $count; $i++) {
                        if (($i + 1) == $count) {
                            $fname = $dir . $file["path"][$i];
                        } else {
                            $dir .= $file["path"][$i] . "/";
                        }

                    }
                    DB::run("INSERT INTO `files` (`torrent`, `path`, `filesize`) VALUES (?, ?, ?)", [$id, $fname, $size]);
                }
            } else {
                DB::run("INSERT INTO `files` (`torrent`, `path`, `filesize`) VALUES (?, ?, ?)", [$id, $internalname, $torrentsize]);
            }

            if ($nfo == 'yes') {
                move_uploaded_file($nfofilename, "$nfo_dir/$id.nfo");
            }

            Logs::write(sprintf(Lang::T("TORRENT_UPLOADED"), htmlspecialchars($name), $_SESSION["username"]));
            // Shout new torrent
            $msg_shout = "New Torrent: [url=" . URLROOT . "/torrent?id=" . $id . "]" . $torrent . "[/url] has been uploaded " . ($anon == 'no' ? "by [url=" . URLROOT . "/account-details.php?id=" . $_SESSION['id'] . "]" . $_SESSION['username'] . "[/url]" : "") . "";
            DB::run("INSERT INTO shoutbox (userid, date, user, message) VALUES(?,?,?,?)", [0, TimeDate::get_date_time(), 'System', $msg_shout]);

            //Uploaded ok message
            if ($external == 'no') {
                $message = sprintf(Lang::T("TORRENT_UPLOAD_LOCAL"), $name, $id, $id);
            } else {
                $message = sprintf(Lang::T("TORRENT_UPLOAD_EXTERNAL"), $name, $id);
                // scrape external
                $torrent = new Torrent(TORRENTDIR . "/$id.torrent");
                try {
                    $scraped = $torrent->scrape();
                } catch (Exception $e) {
                    $scraped = $torrent->errors();
                    exit();
                }
                $myarray = array_shift($scraped);

                $seeders = $leechers = $completed = 0;
                if ($myarray['seeders'] > 0) {
                    $seeders = $myarray['seeders'];
                }
                if ($myarray['leechers'] > 0) {
                    $leechers = $myarray['leechers'];
                }
                if ($myarray['completed'] > 0) {
                    $completed = $myarray['completed'];
                }

                if ($seeders !== null) {
                    // Update the Torrent
                    DB::run("
                    UPDATE torrents
                    SET leechers = ?, seeders = ?, times_completed = ?, last_action = ?, visible = ?
                    WHERE id = ?",
                    [$leechers, $seeders, $completed, TimeDate::get_date_time(), 'yes', $id]
                    );
                }
            }

            Session::flash('info', $message, URLROOT . "/torrent?id=$id");
            die();
        }
    }

}