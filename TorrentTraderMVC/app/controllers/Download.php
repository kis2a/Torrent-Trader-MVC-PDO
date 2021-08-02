<?php
class Download
{

    public function __construct()
    {
        $this->session = Auth::user(0, 2);
    }

    public function index()
    {
        if ($_SESSION['loggedin']) {
            if ($_SESSION["can_download"] == "no") {
                Redirect::autolink(URLROOT, Lang::T("NO_PERMISSION_TO_DOWNLOAD"));
            }
            if ($_SESSION["downloadbanned"] == "yes") {
                Redirect::autolink(URLROOT, Lang::T("DOWNLOADBAN"));
            }
        }

        $id = (int) $_GET["id"];
        if (!$id) {
            Redirect::autolink(URLROOT, Lang::T("ID_NOT_FOUND_MSG_DL"));
        }

        $fn = TORRENTDIR . "/$id.torrent";
        $row = Torrents::isAvailableToDownload($id);
        $vip = $row['vip'];
        if ($_SESSION['class'] < _VIP && $vip == "yes") {
            Redirect::autolink($_SERVER['HTTP_REFERER'], Lang::T("VIPTODOWNLOAD"));
        }
        if (!$row) {
            Redirect::autolink(URLROOT . '/home', Lang::T("ID_NOT_FOUND"));
        }
        if ($row["banned"] == "yes") {
            Redirect::autolink($_SERVER['HTTP_REFERER'], Lang::T("BANNED_TORRENT"));
        }
        if (!is_file($fn)) {
            Redirect::autolink($_SERVER['HTTP_REFERER'], Lang::T("FILE_NOT_FILE"));
        }
        if (!is_readable($fn)) {
            Redirect::autolink($_SERVER['HTTP_REFERER'], Lang::T("FILE_UNREADABLE"));
        }
        $name = $row['filename'];
        $friendlyurl = str_replace("http://", "", URLROOT);
        $friendlyname = str_replace(".torrent", "", $name);
        $friendlyext = ".torrent";
        $name = $friendlyname . "[" . $friendlyurl . "]" . $friendlyext;
        // LIKE MOD
        if (Config::TT()['FORCETHANKS']) {
            if ($_SESSION["id"] != $row["owner"]) {
                $data = DB::run("SELECT user FROM thanks WHERE thanked = ? AND type = ? AND user = ?", [$id, 'torrent', $_SESSION['id']]);
                $like = $data->fetch(PDO::FETCH_ASSOC);
                if (!$like) {
                    Redirect::autolink($_SERVER['HTTP_REFERER'], Lang::T("PLEASE_THANK"));
                }
            }
        }
        Torrents::updateHits($id);
        
        // if user dont have a passkey generate one, only if current member
        if ($_SESSION['loggedin']) {
            if (strlen($_SESSION['passkey']) != 32) {
                $rand = array_sum(explode(" ", microtime()));
                $_SESSION['passkey'] = md5($_SESSION['username'] . $rand . $_SESSION['secret'] . ($rand * mt_rand()));
                Users::setpasskey($_SESSION['passkey'], $_SESSION['id']);
            }
        }

        // if not external and current member, note - it was Config::TT()['MEMBERSONLY']
        if ($row["external"] != 'yes' && $_SESSION['loggedin']) { // local torrent so add passkey
            // Bencode
            $dict = Bencode::decode(file_get_contents($fn));
            $dict['announce'] = sprintf(PASSKEYURL, $_SESSION["passkey"]);
            unset($dict['announce-list']);
            $data = Bencode::encode($dict);
            header('Content-Disposition: attachment; filename="' . $name . '"');
            header("Content-Type: application/x-bittorrent");
            print $data;
        } else {
            header('Content-Disposition: attachment; filename="' . $name . '"');
            header('Content-Length: ' . filesize($fn));
            header("Content-Type: application/x-bittorrent");
            readfile($fn);
        }
    }

    public function attachment()
    {
        $id = (int) $_GET["id"];
        $hash = $_GET["hash"];
        $filename = $hash;
        $fn = TORRENTDIR . "/attachment/$filename.data";
        $sql = DB::run("SELECT * FROM attachments WHERE  id=?", [$id])->fetch(PDO::FETCH_ASSOC);
        $extension = substr($sql['filename'], -3);
        if (!file_exists($fn)) {
            Redirect::autolink($_SERVER['HTTP_REFERER'], "The file $filename does not exists");
        } else {
            header('Content-Disposition: attachment; filename="' . $sql['filename'] . '"');
            header('Content-Length: ' . filesize($fn));
            header("Content-Type: application/$extension");
            readfile($fn);
        }
    }

}