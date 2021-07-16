<?php usermenu($_SESSION['id']);
 include APPROOT.'/views/message/messagenavbar.php'; ?><br>
<center><b><?php echo $data['subject']; ?></b></center><br>
<div class='table'><table class='table table-striped'><thead><tr>
    <th width='150'><?php echo Users::coloredname(Users::getUsernameById($data['sender'])); ?></th>
    <th align='left'><small>Posted at <?php echo $data['added']; ?> </small></th>
</tr></thead><tbody><tr valign='top'>
    <td width='20%' align='left'><center><?php echo $data['button']; ?></center></td>
    <td><br /><?php echo format_comment($data['msg']); ?></td>
</tr><tbody></table></div>