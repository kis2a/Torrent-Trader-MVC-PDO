<?php
if (Auth::permission('loggedin') == true || !Config::TT()['MEMBERSONLY']) {
    Style::block_begin(Lang::T("LATEST_TORRENTS"));
    $expire = 900; // time in seconds
    $TTCache = new Cache();
    if (($latestuploadsrecords = $TTCache->Get("latestuploadsblock", $expire)) === false) {
        $latestuploadsquery = DB::run("SELECT id, name, size, seeders, leechers FROM torrents WHERE banned='no' AND visible = 'yes' ORDER BY id DESC LIMIT 5");
        $latestuploadsrecords = array();
        while ($latestuploadsrecord = $latestuploadsquery->fetch(PDO::FETCH_ASSOC)) {
            $latestuploadsrecords[] = $latestuploadsrecord;
        }
        $TTCache->Set("latestuploadsblock", $latestuploadsrecords, $expire);
    }

    if ($latestuploadsrecords) {
        foreach ($latestuploadsrecords as $row) {
            $smallname = htmlspecialchars(CutName($row['name'], 20)) . "..."; ?>
			<a href='<?php echo URLROOT; ?>/torrent?id=<?php echo $row["id"]; ?>' title='<?php echo htmlspecialchars($row["name"]); ?>'><?php echo $smallname; ?></a><br>
			<span><?php echo Lang::T("SIZE"); ?>: <?php echo mksize($row["size"]); ?></span>
		<?php
        }
    } else { ?>
		<p calss="text-center"><?php echo Lang::T("NOTHING_FOUND"); ?></p>
	    <?php
    } ?>
    <?php
    Style::block_end();
}