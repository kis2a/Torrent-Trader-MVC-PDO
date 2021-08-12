<?php
if ($_SESSION['loggedin'] == true) {
    Style::block_begin(Lang::T("MOST_ACTIVE"));
    $where = "WHERE banned = 'no' AND visible = 'yes'";
    $TTCache = new Cache();
    $expires = 600; // Cache time in seconds
    if (($rows = $TTCache->Get("mostactivetorrents_block", $expires)) === false) {
        $res = DB::run("SELECT id, name, seeders, leechers FROM torrents $where ORDER BY seeders + leechers DESC, seeders DESC, added ASC LIMIT 10");

        $catsquery = DB::run("SELECT distinct parent_cat FROM categories ORDER BY parent_cat");
        $rows = array();
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $rows[] = $row;
        }

        $TTCache->Set("mostactivetorrents_block", $rows, $expires);
    }

    if ($rows) {
        foreach ($rows as $row) {
            $char1 = 40; //cut length
            $smallname = htmlspecialchars(substr($row["name"], 0, 30)) . "..."; ?>
            <a href='<?php echo URLROOT; ?>/torrent?id=<?php echo $row["id"]; ?>' title='<?php echo htmlspecialchars($row["name"]); ?>'><?php echo $smallname; ?></a>
                <span class="label label-success"> S: <?php echo number_format($row['seeders']); ?></span>
                <span class="label label-warning"> L: <?php echo number_format($row['leechers']); ?></span>
            <br>
		<?php }

    } else {
        ?>
	<p><?php echo Lang::T("NOTHING_FOUND"); ?></p>
<?php } ?>
	<!-- end content -->

<?php Style::block_end();
}