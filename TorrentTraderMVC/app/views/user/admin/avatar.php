<?php echo ($data['pagertop']); ?>
<div class="row justify-content-md-center-2">
<div class='table-responsive'>
<table class='table table-striped'><thead><tr>
    <th><?php echo Lang::T("USER") ?></th>
    <th class="table_head">Avatar</th>
</tr></thead><tbody><?php
 while ($arr = $data['res']->fetch(PDO::FETCH_ASSOC)) {
    echo ("<tr><td class='table_col1'><b><a href=\"" . URLROOT . "/profile?id=" . $arr['id'] . "\">" . Users::coloredname($arr['username']) . "</a></b></td><td class='table_col2'>");
    if (!$arr['avatar']) {
        echo "<img width=\"80\" src=" . URLROOT . "/images/default_avatar.png' alt='' /></td>";
    } else {
        echo "<img width=\"80\" src=\"" . htmlspecialchars($arr["avatar"]) . "\" alt='' /></td></tr>";
    }
} ?>
</tbody></table>
</div>
</div>
<?php echo ($data['pagerbottom']);