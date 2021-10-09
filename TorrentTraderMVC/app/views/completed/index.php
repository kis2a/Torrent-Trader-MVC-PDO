<table cellpadding="3" cellspacing="0" align="center" class="table_table">
<tr>
    <th class="table_head"><?php echo Lang::T("USERNAME"); ?></th>
    <th class="table_head"><?php echo Lang::T("CURRENTLY_SEEDING"); ?></th>
    <th class="table_head"><?php echo Lang::T("DATE_COMPLETED"); ?></th>
    <th class="table_head"><?php echo Lang::T("RATIO"); ?></th>
</tr>
<?php
while ($row = $data['res']->fetch(PDO::FETCH_ASSOC)) {

    if (($row["privacy"] == "strong") && (Users::has("edit_users") == "no")) {
    continue;
    }

    if ($row['downloaded'] > 0)
    {
        $ratio = $row['uploaded'] / $row['downloaded'];
        $ratio = number_format($ratio, 2);
        $color = get_ratio_color($ratio);
        if ($color)
            $ratio = "<font color=#ff0000>$ratio</font>";
    } else if ($row['uploaded'] > 0)
        $ratio = 'Inf.';
    else
        $ratio = '---';
    $comdate = date("d.M.Y<\\b\\r><\\s\\m\\a\\l\\l>H:i</\\s\\m\\a\\l\\l>", TimeDate::utc_to_tz_time($row["date"]));
    $peers = (get_row_count("peers", "WHERE torrent = '$id' AND userid = '$row[id]'")) ? "<font color='#27B500'><b>".Lang::T("YES")."</b></font>" : "<font color='#FF1200'><b>".Lang::T("NO")."</b></font>";
    $res2 = DB::run("SELECT uload, dload, stime, utime, ltime, hnr FROM snatched WHERE tid = '$id' AND uid = '$row[id]'");
    $row2 = $res2->fetch(PDO::FETCH_ASSOC);
    if ($row2['dload'] > 0)
    {
        $tratio = $row2['uload'] / $row2['dload'];
        $tratio = number_format($tratio, 2);
        $color = get_ratio_color($tratio);
        if ($color)
            $tratio = "<font color=#ff0000>$tratio</font>";
    } else if ($row2['uload'] > 0)
        $tratio = 'Inf.';
    else
        $tratio = '---';
    $startdate = TimeDate::utc_to_tz(TimeDate::get_date_time($row2['stime']));
    $lastaction = TimeDate::utc_to_tz(TimeDate::get_date_time($row2['utime']));
    $upload = "<font color='#27B500'><b>".mksize($row2["uload"])."</b></font>";
    $download = "<font color='#FF1200'><b>".mksize($row2["dload"])."</b></font>";
    $seedtime = $row2['ltime'] ? TimeDate::mkprettytime($row2['ltime']) : '---';
    if ($row2['hnr'] != "yes") { $hnr = "<font color='#27B500'><b>".Lang::T("NO")."</b></font>";  } else { $hnr = "<font color='#FF1200'><b>".Lang::T("YES")."</b></font>"; }

?>
    <tr>
        <?php /*
    <td class="table_col1"><a href="<?php echo URLROOT; ?>/profile?id=<?php echo $row["id"]; ?>"><?php echo Users::coloredname($row['username']); ?></a></td>
    <td class="table_col2"><?php echo $peers; ?></td>
    <td class="table_col1"><?php echo TimeDate::utc_to_tz($row["date"]); ?></td>
    <td class="table_col2"><?php echo number_format($ratio, 2); ?></td>
*/ ?>

    <td class="table_col1"><a href="account-details.php?id=<?php echo $row["id"]; ?>"><b><?php echo $row["username"]; ?></b></a> | <b><?php echo $ratio; ?></b></td>
    <td class="table_col2" align="center"><?php echo date('d.M.Y<\\b\\r>H:i', TimeDate::sql_timestamp_to_unix_timestamp($startdate));?></td>
    <td class="table_col1" align="center"><?php echo $comdate; ?></td>
    <td class="table_col2" align="center"><?php echo date('d.M.Y<\\b\\r>H:i', TimeDate::sql_timestamp_to_unix_timestamp($lastaction));?></td>
    <td class="table_col1" align="center"><?php echo $upload; ?></td>
    <td class="table_col2" align="center"><?php echo $download; ?></td>
    <td class="table_col1" align="center"><b><?php echo $tratio; ?></b></td>
    <td class="table_col2" align="center"><?php echo $seedtime; ?></td>
    <td class="table_col1" align="center"><?php echo $peers; ?></td>
    <td class="table_col2" align="center"><b><?php echo $hnr; ?></b></td>
    </tr>
    <?php
} ?>
</table>
<center><a href="<?php echo URLROOT; ?>/torrent?id=<?php echo $data['id']; ?>"><?php echo Lang::T("BACK_TO_DETAILS"); ?></a></center>