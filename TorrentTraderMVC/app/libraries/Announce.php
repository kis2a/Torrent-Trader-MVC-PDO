<?php
class Announce
{

    public static function Checks($port, $compact, $passkey, $agent)
    {
        if (!ctype_digit($port) || $port < 1 || $port > 65535) {
            die(Announce::track('Invalid client port'));
        }
        if (!$compact) {
            die(Announce::track("Your client doesn't support compact, please update your client"));
        }
        //if (preg_match("/^Mozilla|^Opera|^Links|^Lynx/i", $user_agent)) { die("No");}
        if (strlen($passkey) != 32) {
            die(Announce::track("Invalid passkey (" . strlen($passkey) . " - $passkey)"));
        }
        // BLOCK ACCESS WITH WEB BROWSERS
        if (preg_match("/^Mozilla|^Opera|^Links|^Lynx/i", $agent)) {
            die("No Browser Access");
        }
        // check infohash strlen
    }

    public static function Clientban($peerid)
    {
        $stmt = DB::run("SELECT agent_name FROM clients");
        $agentarray = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $useragent = substr($peerid, 0, 8);
        foreach ($agentarray as $bannedclient) {
            if (@strpos($useragent, $bannedclient) !== false) {
                die(Announce::track('Client is banned'));
            }
        }
    }

    public static function UserCheck($passkey)
    {
        $stmt = DB::run("SELECT u.id, u.class, u.uploaded, u.downloaded, u.ip, u.passkey, g.can_download, g.maxslots
                         FROM users u
                         INNER JOIN `groups` g
                         ON u.class = g.group_id
                        WHERE u.passkey=? AND u.enabled = ? AND u.status = ? LIMIT 1", [$passkey, 'yes', 'confirmed']);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            die(Announce::track("Cannot locate a user with that passkey!"));
        }
        if ($user["can_download"] == "no") {
            die(Announce::track("You do not have permission to download."));
        }
        if (!$user["passkey"] == $passkey) {
            die(Announce::track("Can NOT find user passkey."));
        }

        return $user;
    }

    public static function TorrentCheck($info_hash)
    {
        $stmt = DB::run("SELECT id, info_hash, banned, freeleech, seeders + leechers
                         AS numpeers, UNIX_TIMESTAMP(added)
                         AS ts, seeders, leechers, times_completed
                         FROM torrents WHERE info_hash=?", [$info_hash]);
        $torrent = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$torrent) {
            die(Announce::track("Torrent not found on this Announce::tracker - hash = " . $info_hash));
        }
        if ($torrent["banned"] == 'yes') {
            die(Announce::track("Torrent has been banned - hash = " . $info_hash));
        }

        return $torrent;
    }

    public static function PeerCheck($id, $passkey, $maxslots)
    {
        $stmt = DB::run("SELECT seeder, UNIX_TIMESTAMP(last_action) AS ez, peer_id, ip, port, uploaded, downloaded, userid, passkey
                         FROM peers
                         WHERE torrent = $id LIMIT 50");
        $peer = $stmt->fetch(PDO::FETCH_ASSOC);

        $sql = DB::run("SELECT COUNT(*) FROM peers WHERE torrent=? AND passkey=?", [$id, $passkey]);
        $valid = $sql->rowCount();
        /*
        if ($valid >= 1 && $seeder == 'no') {
        die(Announce::track("Connection limit exceeded! You may only leech from one location at a time."));
        }
        if ($valid[0] >= 3 && $seeder == 'yes') {
        die(Announce::track("Connection limit exceeded!"));
        }
         */
        $countslot = DB::run("SELECT DISTINCT torrent
                              FROM peers WHERE userid =? AND seeder=?", [$id, 'no']);
        $slot = $countslot->rowCount();
        if ($slot >= $maxslots) {
            die(Announce::track("Maximum Slot exceeded! You may only download $slot torrent at a time."));
        }

        return $peer;
    }

    public static function InsertPeer($connectable, $torrentid, $peer_id, $ip, $passkey, $port, $uploaded, $downloaded, $left, $seeder, $userid, $agent)
    {
        $stmt = DB::run("INSERT INTO peers (connectable, torrent, peer_id, ip, passkey, port, uploaded, downloaded, to_go, started, last_action, seeder, userid, client)
                         VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)", [$connectable, $torrentid, $peer_id, $ip, $passkey, $port, $uploaded, $downloaded, $left, self::datetime(), self::datetime(), $seeder, $userid, $agent]);
    }

    public static function UpdateUserAndSnatched($upthis, $downthis, $elapsed, $userid, $torrentid, $freeleech = 0)
    {
        if ($freeleech == 1) {
            DB::run("UPDATE users SET uploaded = uploaded + $upthis WHERE id=$userid") or die(Announce::track("Tracker error: Unable to update stats"));
        } else {
            DB::run("UPDATE users SET uploaded = uploaded + $upthis, downloaded = downloaded + $downthis WHERE id=$userid") or die(Announce::track("Tracker error: Unable to update stats"));
            // snatch
            DB::run("UPDATE LOW_PRIORITY `snatched` SET `uload` = `uload` + '$upthis', `dload` = `dload` + '$downthis', `utime` = '" . Announce::datetime() . "', `ltime` = `ltime` + '$elapsed' WHERE `tid` = '$torrentid' AND `uid` = '$userid'");
        }
    }

    public static function UpdatePeer($ip, $passkey, $port, $uploaded, $downloaded, $left, $agent, $seeder, $torrentid, $peer_id)
    {
        // Count the peers from to_go for seeders/leechers
        $count_peers = DB::run("UPDATE peers SET ip = ?, passkey = ?, port = ?, uploaded = ?, downloaded = ?, to_go = ?, last_action = ?, client = ?, seeder = ? WHERE torrent = ? AND peer_id = ?",
            [$ip, $passkey, $port, $uploaded, $downloaded, $left, self::datetime(), $agent, $seeder, $torrentid, $peer_id]);
    }

    public static function Event($event, $torrentid, $peerid, $userid, $seeder, $freeleech)
    {
        switch ($event) {

            case 'stopped': // If stopped Correctly by user
                DB::run("DELETE FROM peers WHERE torrent = ? AND peer_id = ?", [$torrentid, $peerid]);
                break;

            case 'completed':
                DB::run("INSERT INTO completed (userid, torrentid, date) VALUES (?,?,?)", [$userid, $torrentid, self::datetime()]);
                DB::run("UPDATE LOW_PRIORITY `snatched` SET `completed` = '1' WHERE `tid` = '$torrentid' AND `uid` = '$userid' AND `utime` = '" . self::datetime() . "'");
                $completed = 1;
                return $completed;
                break;

            case 'started':
                if (($seeder == 'no' && $freeleech == 0)) {
                    DB::run("INSERT INTO `snatched` (`uid`, `tid`, `stime`, `utime`) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE `utime` = ?", [$userid, $torrentid, self::datetime(), self::datetime(), self::datetime()]);
                }
                break;

            default:
                // no event so update ???
                break;
        }
    }

    public static function CountPeers($id)
    {
        // Count the peers from to_go for seeders/leechers
        $count_peers = DB::run('SELECT IFNULL(SUM(peers.to_go > 0), 0) AS leech, IFNULL(SUM(peers.to_go = 0), 0) AS seed '
            . 'FROM peers '
            . "WHERE peers.torrent = ? "
            . 'AND peers.last_action >= DATE_SUB(UTC_TIMESTAMP(), INTERVAL ' . (50 + 50) . ' SECOND) '
            . 'GROUP BY `peers`.`torrent`', [$id]);
        return $count_peers;
    }

    public static function UpdateTorrent($leechers, $seeders, $completed, $banned, $torrentid)
    {
        if ($banned != "yes") {
            $visible = 'no';
        } else {
            $visible = 'yes';
        }
        DB::run("UPDATE torrents SET last_action = ?, leechers = ?, seeders = ?, times_completed = ?, visible = ?  WHERE id=?", [Announce::datetime(), $leechers, $seeders, $completed, $visible, $torrentid]);
    }

    public static function track($list, $c = 0, $i = 0)
    {
        if (is_string($list)) { //Did we get a string? Return an error to the client
            return 'd14:failure reason' . strlen($list) . ':' . $list . 'e';
        }
        $p = ''; //Peer directory
        foreach ($list as $d) { //Runs for each client
            $pid = '';
            if (!isset($_GET['no_peer_id'])) { //Send out peer_ids in the reply
                $real_id = hex2bin($d[2]);
                $pid = '7:peer id' . strlen($real_id) . ':' . $real_id;
            }
            $p .= 'd2:ip' . strlen($d[0]) . ':' . $d[0] . $pid . '4:porti' . $d[1] . 'ee';
        }
        //Add some other paramters in the dictionary and merge with peer list
        $r = 'd8:intervali' . Config::TT()['ANNOUNCEINTERVAL'] . 'e12:min intervali' . Config::TT()['ANNOUNCEINTERVAL'] . 'e8:completei' . $c . 'e10:incompletei' . $i . 'e5:peersl' . $p . 'ee';
        return $r;
    }

    public static function datetime($timestamp = 0)
    {
        if ($timestamp) {
            return date("Y-m-d H:i:s", $timestamp);
        } else {
            return gmdate("Y-m-d H:i:s");
        }
    }

    public static function getip()
    {
        if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
            return $ip;
            foreach (array(
                'HTTP_CLIENT_IP',
                'HTTP_X_FORWARDED_FOR',
                'HTTP_X_FORWARDED',
                'HTTP_X_CLUSTER_CLIENT_IP',
                'HTTP_FORWARDED_FOR',
                'HTTP_FORWARDED',
                'HTTP_CF_CONNECTING_IP',
                'REMOTE_ADDR',
            ) as $key) {
                if (array_key_exists($key, $_SERVER) === true) {
                    foreach (array_map('trim', explode(',', $_SERVER[$key])) as $ip) {
                        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                            return $ip;
                        }
                    }
                }
            }
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
            return $ip;
        }
    }
}