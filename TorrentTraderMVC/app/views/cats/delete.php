<form method='post' action='<?php echo  URLROOT ?>/admincategories/delete?id=<?php echo $data['id'] ?>&amp;sure=1'>
<center><table border='0' cellspacing='0' cellpadding='5'>
<tr><td align='left'><b>Category ID to move all Torrents To: </b><input type='text' name='newcat' /> (Cat ID)</td></tr>
<tr><td align='center'><input type='submit' value='<?php echo Lang::T("SUBMIT") ?>' /></td></tr>
</table></center>
</form>