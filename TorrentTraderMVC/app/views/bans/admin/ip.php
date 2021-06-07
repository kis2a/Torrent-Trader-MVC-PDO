<p align="justify">This page allows you to prevent individual users or groups of users from accessing your tracker by placing a block on their IP or IP range.<br />
If you wish to temporarily disable an account, but still wish a user to be able to view your tracker, you can use the 'Disable Account' option which is found in the user's profile page.</p><br />
<?php
if ($data['count'] == 0) {
    print("<b>No Bans Found</b><br />\n");
} else { 
    echo $data['pagertop']; ?>
    <form id='ipbans' action='<?php echo URLROOT ?>/adminbans/ip?do=del' method='post'><table width='98%' cellspacing='0' cellpadding='5' align='center' class='table_table'>
    <tr>
    <th class='table_head'><?php echo Lang::T("DATE_ADDED") ?></th>
    <th class='table_head'>First IP</th>
    <th class='table_head'>Last IP</th>
    <th class='table_head'><?php echo Lang::T("ADDED_BY") ?></th>
    <th class='table_head'>Comment</th>
    <th class='table_head'><input type='checkbox' name='checkall' onclick='checkAll(this.form.id);' /></th>
    </tr>
    <?php
    while ($arr = $data['res']->fetch(PDO::FETCH_ASSOC)) { ?>
        <tr>
        <td align='center' class='table_col1'><?php echo date('d/m/Y H:i:s', TimeDate::utc_to_tz_time($arr["added"])) ?></td>
        <td align='center' class='table_col2'><?php echo $arr['first'] ?></td>
        <td align='center' class='table_col1'><?php echo $arr['last'] ?></td>
        <td align='center' class='table_col2'><a href='<?php echo URLROOT ?>/profile?id=<?php echo $arr['addedby'] ?>'><?php echo $arr['username'] ?></a></td>
        <td align='center' class='table_col1'><?php echo $arr['comment'] ?></td>
        <td align='center' class='table_col2'><input type='checkbox' name='delids[]' value='<?php echo $arr['id'] ?>' /></td>
        </tr>
        <?php
    }
    ?>
    </table><br /><center><input type='submit' value='Delete Checked' /></center></form><br />
    <?php
    echo $data['pagerbottom'];
} ?>
<br />
<form method='post' action='<?php echo URLROOT ?>/adminbans/ip?do=add'>
<table cellspacing='0' cellpadding='5' align='center' class='table_table' width='98%'>
<tr><th class='table_head' align='center'>Add Ban</th></tr>
<tr><td class='table_col1' align='center'>First IP:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='text' name='first' size='40' /></td></tr>
<tr><td class='table_col1' align='center'>Last IP:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='text' name='last' size='40' /></td></tr>
<tr><td class='table_col1' align='center'>Comment: <input type='text' name='comment' size='40' /></td></tr>
<tr><td class='table_head' align='center'><input type='submit' value='Okay' /></td></tr>
</table></form><br />