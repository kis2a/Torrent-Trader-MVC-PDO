<form name='masspm' method='post' action='<?php echo URLROOT ?>/adminmessages/send'>
<table border='0' cellspacing='0' cellpadding='5' align='center' width='90%'>
<tr><td><b>Send to:</b></td></tr>
<?php
while ($row = $data['res']->fetch(PDO::FETCH_LAZY)) { ?>
    <tr><td><input type='checkbox' name='clases[]' value='<?php echo $row['group_id']; ?>?' /><?php echo $row['level']; ?><br /></td></tr>
   <?php
} ?>
<tr>
<td><b>Subject:</b><br /><input type="text" name="subject" size="30" /></td>
</tr><tr>
<td><b>Message: </b><br /><textarea name='msg' rows='13' cols="90"></textarea></td>
</tr><tr>
<td><b><?php echo Lang::T("SENDER"); ?></b>
<?php echo $_SESSION['username'] ?> <input name="sender" type="radio" value="self" checked="checked" />
System <input name="sender" type="radio" value="system" /></td>
</tr>
</table>
<div class="text-center">
    <input type="submit" class='btn btn-sm ttbtn' value="Send" />
</div>
</form>