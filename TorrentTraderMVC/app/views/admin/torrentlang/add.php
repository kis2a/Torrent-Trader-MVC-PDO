<center>
<form method='post' action='<?php echo  URLROOT; ?>/admintorrentlang/takeadd'>
<input type='hidden' name='action' value='torrentlangs' />
<input type='hidden' name='do' value='takeadd' />
<table border='0' cellspacing='0' cellpadding='5'>
<tr><td><b>Name:</b> <input type='text' name='name' /></td></tr>
<tr><td><b>Sort:</b> <input type='text' name='sort_index' /></td></tr>
<tr><td><b>Image:</b> <input type='text' name='image' /></td></tr>
</table>
<div class="text-center">
    <input type='submit' class='btn btn-sm ttbtn' value='<?php echo Lang::T("SUBMIT"); ?>' />
</div>
</form>
</center>