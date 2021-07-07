<center>We have <?php echo $data['count1'] ?>peers</center>
<center><a href='<?php echo URLROOT; ?>/peers/dead'>All Dead Torrents</a></center>
<?php echo $data['pagertop'];
if ($data['result']->rowCount() != 0) { ?>
    <center><table width="100%" border="0" cellspacing="0" cellpadding="3" class="table_table">
    <tr>
    <th class="table_head">User</th>
    <th class="table_head">Torrent</th>
    <th class="table_head">IP</th>
    <th class="table_head">Port</th>
    <th class="table_head">Upl.</th>
    <th class="table_head">Downl.</th>
    <th class="table_head">Peer-ID</th>
    <th class="table_head">Conn.</th>
    <th class="table_head">Seeding</th>
    <th class="table_head">Started</th>
    <th class="table_head">Last<br />Action</th>
    </tr> <?php
    while ($row = $data['result']->fetch(PDO::FETCH_ASSOC)) {
        if (MEMBERSONLY) {
            $sql1 = "SELECT id, username FROM users WHERE id = $row[userid]";
            $row1 = DB::run($sql1)->fetch();
        }
        if ($row1['username']) {
            print '<tr><td class="table_col1"><a href="' . URLROOT . '/users/profile?id=' . $row['userid'] . '">' . User::coloredname($row1['username']) . '</a></td>';
        } else {
            print '<tr><td class="table_col1">' . $row["ip"] . '</td>';
        }
        $sql2 = "SELECT id, name FROM torrents WHERE id = $row[torrent]";
        $result2 = DB::run($sql2);
        while ($row2 = $result2->fetch(PDO::FETCH_ASSOC)) {
            $smallname = CutName(htmlspecialchars($row2["name"]), 40);
            print '<td class="table_col2"><a href="torrent?id=' . $row['torrent'] . '">' . $smallname . '</a></td>';
            print '<td align="center" class="table_col1">' . $row['ip'] . '</td>';
            print '<td align="center" class="table_col2">' . $row['port'] . '</td>';
            if ($row['uploaded'] < $row['downloaded']) {
                print '<td align="center" class="table_col1"><font class="error">' . mksize($row['uploaded']) . '</font></td>';
            } elseif ($row['uploaded'] == '0') {
                print '<td align="center" class="table_col1">' . mksize($row['uploaded']) . '</td>';
            } else {
                print '<td align="center" class="table_col1"><font color="green">' . mksize($row['uploaded']) . '</font></td>';
            }
            print '<td align="center" class="table_col2">' . mksize($row['downloaded']) . '</td>';
            print '<td align="center" class="table_col1">' . substr($row["peer_id"], 0, 8) . '</td>';
            if ($row['connectable'] == 'yes') {
                print '<td align="center" class="table_col2"><font color="green">' . $row['connectable'] . '</font></td>';
            } else {
                print '<td align="center" class="table_col2"><font class="error">' . $row['connectable'] . '</font></td>';
            }
            if ($row['seeder'] == 'yes') {
                print '<td align="center" class="table_col1"><font color="green">' . $row['seeder'] . '</font></td>';
            } else {
                print '<td align="center" class="table_col1"><font class="error">' . $row['seeder'] . '</font></td>';
            }
                print '<td align="center" class="table_col2">' . TimeDate::utc_to_tz($row['started']) . '</td>';
                print '<td align="center" class="table_col1">' . TimeDate::utc_to_tz($row['last_action']) . '</td>';
                print '</tr>';
        }
    }
    print '</table>';
    print("$data[pagerbottom]</center>");
} else {
    print '<center><b>No Peers</b></center><br />';
}