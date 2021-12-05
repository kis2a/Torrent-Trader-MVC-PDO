<?php
// Stop Errors Messing With Output
error_reporting(0); // E_ALL ^ E_NOTICE
// Set Some Config Settings (external are done with download - not announce)
define("_MEMBERSONLY", true);
define("_DEBUG", false);
define("_INTERVAL", 600);
define("_INTERVAL_MIN", 300);
// Classes
require '../app/config/config.php';
require '../app/libraries/DB.php';
require '../app/libraries/Announce.php';
// Register custom exception handler (Disable On Live)
include "../app/helpers/exception_helper.php";
set_exception_handler("handleUncaughtException");

// 1) Check Whats Connecting (no browsers allowed)
$agent = Announce::checkagent($_SERVER["HTTP_USER_AGENT"]) ?? 'n/a';

// 2) Check Passkey
$passkey = Announce::checkpasskey($_GET['passkey']);

// 3) Get Respones From Client
$client = Announce::checkClientFields();

// 4) Check If Seeder
$seeder = ($client['left'] == 0) ? "yes" : "no";

// 5) Check User
$user['id'] = 0;
if (_MEMBERSONLY){
	$user = Announce::UserCheck($passkey);
}

// 6) Check Torrent
$torrent = Announce::TorrentCheck($client['info_hash']);

// 7) Completed So Lets Record    
if ($client['event'] == "completed") {
    if ( _MEMBERSONLY ) {
        Announce::Completed($user['id'], $torrent['id']);
        $torrent['times_completed'] = $torrent['times_completed'] + 1;
    }
}

// 8) Check If Peer Already In Database
$peer = Announce::CheckIfPeer($torrent['id'], $client['peer_id'], $passkey);

// 9) Is It New Or Exsisting Peer
if (!$peer) {
    // If New Peer stopped Return Empty Response
    if ($client['event'] == "stopped") {
        die(Announce::response(array(), 0, 0));
    }
    // Check Max Download Slots
    Announce::MaxSlots($user);
    // Use Client To Insert New Peer - wait times / fsock / max connections would go before here
	Announce::InsertPeer($passkey, $seeder, $user['id'], $agent, $torrent, $client);
	// Not Seeder So Insert Snatch
	if ( (_MEMBERSONLY) && (($seeder == 'no' && $torrent['freeleech'] == 0)) ) {
        Announce::InsertSnatched($user['id'], $torrent['id']);
    }
} else {
	// Use Client To Update User/Snatched Details
	$elapsed = ($peer['seeder'] == 'yes') ? _INTERVAL - floor(($peer['ez'] - time()) / 60) : 0;
    $upthis = max(0, $client['uploaded'] - $peer["uploaded"]);
    // $downthis = max(0, $client['downloaded'] - $peer["downloaded"]); 
	$downthis = $user['class'] == _VIP ? 0 : max(0, $client['downloaded'] - $peer["downloaded"]);
    if ($upthis > 0 || $downthis > 0 || $elapsed > 0){
		if ($torrent["freeleech"] == 1){
			Announce::UpdateUser($user['id'], $upthis, false);
		}else{
            Announce::UpdateUser($user['id'], $upthis, $downthis);
            Announce::UpdateSnatched($userid, $torrentid, $elapsed, $downthis, $upthis);
        }
    }
    // If Peer stopped Delete & Return Empty Response
    if ($client['event'] == "stopped") {
        Announce::DeletePeer($torrent['id'], $client['peer_id']);
        die(Announce::response(array(), 0, 0));
    }
    // Now Update Peer
    Announce::UpdatePeer($passkey, $agent, $seeder, $torrent['id'], $client);
}

// 10) Now Lets Get Details For Response
$response = DB::run("SELECT peer_id, ip, port FROM peers WHERE torrent = ?", [$torrent['id']])->fetchAll();
$reply = array(); // To be encoded and sent to the client
foreach($response as $resp) { // Runs for every client with the same torrentid/infohash
	$reply[] = array($resp['ip'], $resp['port'], $resp['peer_id']); //ip, port, peerid
}
$seeders = DB::run("SELECT COUNT(*) FROM peers WHERE seeder=? AND torrent = ?", ['yes', $torrent['id']])->fetchColumn();
$leechers = DB::run("SELECT COUNT(*) FROM peers WHERE seeder=? AND torrent = ?", ['no', $torrent['id']])->fetchColumn();

// 11) Update Torrent
if ($seeder == "yes") {
    Announce::UpdateTorrent($leechers, $seeders, $torrent['times_completed'], $torrent);
}

// 12) Send Response Back
die(Announce::response($reply, $seeders, $leechers));