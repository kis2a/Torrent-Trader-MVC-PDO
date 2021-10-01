<table align="right">
<tr><td valign="top">
<form id='sort' action=''>
<b>Type:</b>
<select name="type" onchange="window.location='<?php echo $data['page']; ?>type='+this.options[this.selectedIndex].value+'&amp;completed='+document.forms['sort'].completed.options[document.forms['sort'].completed.selectedIndex].value">
    <option value="">All Types</option>
    <option value="user" <?php echo ($_GET['type'] == "user" ? " selected='selected'" : ""); ?>>Users</option>
    <option value="torrent" <?php echo ($_GET['type'] == "torrent" ? " selected='selected'" : ""); ?>>Torrents</option>
    <option value="comment" <?php echo ($_GET['type'] == "comment" ? " selected='selected'" : ""); ?>>Comments</option>
    <option value="forum" <?php echo ($_GET['type'] == "forum" ? " selected='selected'" : ""); ?>>Forum</option>
    </select>
    <b>Completed:</b>
    <select name="completed" onchange="window.location='<?php echo URLROOT; ?>/adminreports?completed='+this.options[this.selectedIndex].value+'&amp;type='+document.forms['sort'].type.options[document.forms['sort'].type.selectedIndex].value">
    <option value="0" <?php echo ($_GET['completed'] == 0 ? " selected='selected'" : ""); ?>>No</option>
    <option value="1" <?php echo ($_GET['completed'] == 1 ? " selected='selected'" : ""); ?>>Yes</option>
</select>
</form>
</td></tr>
</table><br />

<form id="reports" method="post" action="<?php echo URLROOT; ?>/adminreports/completed">
<table class='table table-striped table-bordered table-hover'><thead><tr>
<th class="table_head">Reported By</th>
<th class="table_head">Subject</th>
<th class="table_head">Type</th>
<th class="table_head">Reason</th>
<th class="table_head">Dealt With</th>
<th class="table_head"><input type="checkbox" name="checkall" onclick="checkAll(this.form.id);" /></th>
</tr><thead>
<?php
if ($data['res']->rowCount() <= 0): ?>
    <tr><td class="table_col1" colspan="6" align="center">No reports found.</td></tr>
    <?php
endif;

while ($row = $data['res']->fetch(PDO::FETCH_LAZY)):
    $dealtwith = '<b>No</b>';
    if ($row["dealtby"] > 0) {
        $r = DB::run("SELECT username FROM users WHERE id = '$row[dealtby]'")->fetch();
        $dealtwith = 'By <a href="' . URLROOT . '/profile?id=' . $row['dealtby'] . '">' . $r['username'] . '</a>';
    }

    $r = Reports::getname($row['type'], $row['votedfor']);

    var_dump($r);
    //$r = $q->fetch(PDO::FETCH_LAZY);
    if ($row["type"] == "user") {
        $link = "".URLROOT."/profile?id=$row[votedfor]";
    } else if ($row["type"] == "torrent") {
    $link = "".URLROOT."/torrent?id=$row[votedfor]";
    } else if ($row["type"] == "comment") {
        $link = "".URLROOT."/comments?type=" . ($r['news'] > 0 ? "news" : "torrent") . "&amp;id=" . ($r['news'] > 0 ? $r['news'] : $r['torrent']) . "#comment$row[votedfor]";
    } else if ($row["type"] == "forum") {
        $link = "".URLROOT."/forums/viewtopic&amp;topicid=$row[votedfor]&amp;page=last#post$row[votedfor_xtra]";
    }
    ?>
    <tr>
          <td class="table_col1" align="center" width="10%"><a href="<?php echo URLROOT; ?>/profile?id=<?php echo $row['addedby']; ?>"><?php echo Users::coloredname($row['username']); ?></a></td>
          <td class="table_col2" align="center" width="15%"><a href="<?php echo $link; ?>"><?php echo CutName($r['name'], 40); ?></a></td>
          <td class="table_col1" align="center" width="10%"><?php echo $row['type']; ?></td>
          <td class="table_col2" align="center" width="50%"><?php echo htmlspecialchars($row['reason']); ?></td>
          <td class="table_col1" align="center" width="10%"><?php echo $dealtwith; ?></td>
          <td class="table_col2" align="center" width="5%"><input type="checkbox" name="reports[]" value="<?php echo $row["id"]; ?>" /></td>
      </tr>
<?php
endwhile; ?>
</tbody></table>

<?php
if ($_GET["completed"] != 1): ?>
    <input type="submit" name="mark" value="Mark Completed" />
    <?php
endif;?>
<input type="submit" name="del" value="Delete" />
</form>
<?php
print $pagerbottom;