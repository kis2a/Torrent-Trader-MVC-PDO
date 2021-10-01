<form method='post' action='<?php echo URLROOT; ?>/admintorrentlang/delete?id=<?php echo $data['id']; ?>&amp;sure=1'>
<center><table border='0' cellspacing='0' cellpadding='5'>
<tr><td align='left'><b>Language ID to move all Languages To: </b><input type='text' name='newlangid' /> (Lang ID)</td></tr>
</table></center>
<div class="text-center">
    <input type='submit' class='btn btn-sm ttbtn' value='<?php echo Lang::T("SUBMIT"); ?>' />
</div>
</form>