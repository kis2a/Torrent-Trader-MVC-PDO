<?php

class Import
{

    public function __construct()
    {
        $this->session = Auth::user(_UPLOADER, 2);
    }

    public static function gettorrentfiles()
    {
        $files = array();
        $dh = opendir(IMPORT . "/");
        while (false !== ($file = readdir($dh))) {
            if (preg_match("/\.torrent$/i", $file)) {
                $files[] = $file;
            }
        }
        closedir($dh);
        return $files;
    }

    public function index()
    {
        //ini_set("upload_max_filesize",$max_torrent_size);
        $files = self::gettorrentfiles();
        // check access and rights
        if (Auth::permission("edit_torrents") != "yes") {
            Redirect::autolink(URLROOT, Lang::T("ACCESS_DENIED"));
        }
        $data = [
            'title' => Lang::T("UPLOAD"),
            'files' => $files,
        ];
        View::render('torrent/import', $data, 'user');
    }

    public function submit()
    {
        $files = self::gettorrentfiles();
        //generate announce_urls[] from config.php
        $announce_urls = explode(",", strtolower(ANNOUNCELIST));
        set_time_limit(0);
        // check access and cat id
        if (Auth::permission("edit_torrents") != "yes") {
            Redirect::autolink(URLROOT, Lang::T("ACCESS_DENIED"));
        }
        $catid = (int) Input::get("type");
        if (!Validate::Id($catid)) {
            $message = Lang::T("UPLOAD_NO_CAT");
        }

        if (empty($message)) {
            $r = DB::run("SELECT name, parent_cat FROM categories WHERE id=$catid")->fetch();

            Style::header(Lang::T("UPLOAD_COMPLETE"));
            Style::begin(Lang::T("UPLOAD_COMPLETE"));
            echo "<center>";
            echo "<b>Category:</b> " . htmlspecialchars($r[1]) . " -> " . htmlspecialchars($r[0]) . "<br />";
            for ($i = 0; $i < count($files); $i++) {
                $fname = $files[$i];
                $descr = Lang::T("UPLOAD_NO_DESC");
                $langid = (int) $_POST["lang"];
                preg_match('/^(.+)\.torrent$/si', $fname, $matches);
                $shortfname = $torrent = $matches[1];

                //parse torrent file
                $torrent_dir = TORRENTDIR;
                $torInfo = new Parse();
                $tor = $torInfo->torr(IMPORT."/$fname");

                $announce = strtolower($tor[0]);
                $infohash = $tor[1];
                $creationdate = $tor[2];
                $internalname = $tor[3];
                $torrentsize = $tor[4];
                $filecount = $tor[5];
                $annlist = $tor[6];
                $comment = $tor[7];

                $message = "<br /><br /><hr /><br /><b>$internalname</b><br /><br />fname: " . htmlspecialchars($fname) . "<br />message: ";
                //check announce url is local or external
                if (!in_array($announce, $announce_urls, 1)) {
                    $external = 'yes';
                } else {
                    $external = 'no';
                }

                if (!Config::TT()['ALLOWEXTERNAL'] && $external == 'yes') {
                    $message .= Lang::T("UPLOAD_NO_TRACKER_ANNOUNCE");
                    echo $message;
                    continue;
                }

                $name = $internalname;
                $name = str_replace(".torrent", "", $name);
                $name = str_replace("_", " ", $name);

                //anonymous upload
                $anonyupload = $_POST["anonycheck"];
                if ($anonyupload == "yes") {
                    $anon = "yes";
                } else {
                    $anon = "no";
                }

                $ret = DB::run("INSERT INTO torrents (filename, owner, name, descr, category, added, info_hash, size, numfiles, save_as, announce, external, torrentlang, anon, last_action)
                          VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
                    [$fname, $_SESSION['id'], $name, $descr, $catid, TimeDate::get_date_time(), $infohash, $torrentsize, $filecount, $fname, $announce, $external, $langid, $anon, TimeDate::get_date_time()]);
                $id = DB::lastInsertId();

                if ($ret->errorCode() == 1062) {
                    $message .= Lang::T("UPLOAD_ALREADY_UPLOADED");
                    echo $message;
                    continue;
                }

                if ($id == 0) {
                    $message .= Lang::T("UPLOAD_NO_ID");
                    echo $message;
                    continue;
                }

                copy(IMPORT."/$files[$i]", "$torrent_dir/$id.torrent");

                //EXTERNAL SCRAPE
                if ($external == 'yes' && Config::TT()['UPLOADSCRAPE']) {
                    Tscraper::ScrapeId($id);
                }

                Logs::write("Torrent $id ($name) was Uploaded by $_SESSION[username]");
                $message .= "<br /><b>" . Lang::T("UPLOAD_OK") . "</b><br /><a href='" . URLROOT . "/torrent?id=" . $id . "'>" . Lang::T("UPLOAD_VIEW_DL") . "</a><br /><br />";
                echo $message;
                @unlink(IMPORT."/$fname");
            }
            echo "</center>";
            Style::end();
            Style::footer();
            die;

        } else {
            Redirect::autolink(URLROOT, $message);
        }

    }
}
