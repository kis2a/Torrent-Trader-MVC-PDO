<?php
// Error Reporting
error_reporting(0);
require '../app/config/config.php';
require '../app/libraries/DB.php';
require '../app/libraries/Announce.php';
// Register custom exception handler
include "../app/helpers/exception_helper.php";
set_exception_handler("handleUncaughtException");

// Use the correct content-type
header("Content-type: Text/Plain");

// Make sure we have something to use as a passkey
$passkey = $_GET['passkey'] ?? '';
// Tracker Request Parameters https://wiki.theory.org/BitTorrentSpecification
$info_hash = bin2hex($_GET['info_hash']);
$peerid = $_GET['peer_id'];
$port = $_GET['port'];
$downloaded = isset($_GET['downloaded']) && is_numeric($_GET['downloaded']) ? intval($_GET['downloaded']) : 0;
$uploaded = isset($_GET['uploaded']) && is_numeric($_GET['uploaded']) ? intval($_GET['uploaded']) : 0;
$left = isset($_GET['left']) && is_numeric($_GET['left']) ? intval($_GET['left']) : 0;
$compact = ($_GET["compact"] && $_GET["compact"] == 1) ? true : false;
$no_peer_id = $_GET["no_peer_id"];
$event = $_GET['event'] ?? '';
$ipadress = is_string($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : die("Weird ip adress");
$numwant = $_GET["numwant"] ? (int) $_GET["numwant"] : 50; // limit peers 50 ?

// Set Seeder/Client/Completed
$seeder = ($left == 0) ? "yes" : "no";
$user_agent = isset($_SERVER['HTTP_USER_AGENT']) && is_string($_SERVER['HTTP_USER_AGENT']) ? substr($_SERVER['HTTP_USER_AGENT'], 0, 80) : "N/A";
$completed = 0;

// Check Vars
Announce::Checks($port, $compact, $passkey, $user_agent);
Announce::Clientban($peerid);

// Get User/Torrent/Peer info
$user = Announce::UserCheck($passkey);
$torrent = Announce::TorrentCheck($info_hash);
$peer = Announce::PeerCheck($torrent['id'], $passkey, $user['maxslots']);

// Insert Or Update Peers Table
if (!$peer) {
    // Waitingtimes ??
    $sockres = @fsockopen($ipadress, $port, $errno, $errstr, 5);
    if (!$sockres) {
        $connectable = "no";
    } else {
        $connectable = "yes";
    }
    @fclose($sockres);
    Announce::InsertPeer($connectable, $torrent['id'], $peerid, $ipadress, $passkey, $port, $uploaded, $downloaded, $left, $seeder, $user['id'], $user_agent);
} elseif ($peer) {
    // Lets Calculate Any Changes & Update User $ Snatched Tables
    $elapsed = ($peer['seeder'] == 'yes') ? 50 - floor(($peer['ez'] - time()) / 60) : 0; //
    $upthis = max(0, $uploaded - $peer["uploaded"]);
    $downthis = max(0, $downloaded - $peer["downloaded"]);
    if (($upthis > 0 || $downthis > 0 || $elapsed > 0) && $user['id']) { // LIVE STATS!)
        Announce::UpdateUserAndSnatched($upthis, $downthis, $elapsed, $user['id'], $torrent['id'], $torrent['freeleech']);
    }
    // Now Update Peer Table
    Announce::UpdatePeer($ip, $passkey, $port, $uploaded, $downloaded, $left, $user_agent, $seeder, $torrent['id'], $peerid);
}

// Run Events Updates
Announce::Event($event, $torrent['id'], $peerid, $user['id'], $seeder, $torrent['freeleech']);

// Count Peers In Table
$count_peers = Announce::CountPeers($torrent['id']);
$reply[] = array($ipadress, $port, $peerid);
$seeders = 0;
$leechers = 0;
if ($r = $count_peers->fetch(PDO::FETCH_NUM)) {
    $seeders = $r[1];
    $leechers = $r[0];
}

// Update Torrent
Announce::UpdateTorrent($leechers, $seeders, $torrent['completed'] + $completed, $torrent['banned'], $torrent['id']);

// Print out response
die(Announce::track($reply, $seeders[0], $leechers[0]));