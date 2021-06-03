<center>
<form method='post' action='<?php echo  URLROOT; ?>/admintorrentlang/takeadd'>
<input type='hidden' name='action' value='torrentlangs' />
<input type='hidden' name='do' value='takeadd' />
<table border='0' cellspacing='0' cellpadding='5'>
<tr><td><b>Name:</b> <input type='text' name='name' /></td></tr>
<tr><td><b>Sort:</b> <input type='text' name='sort_index' /></td></tr>
<tr><td><b>Image:</b> <input type='text' name='image' /></td></tr>
<tr><td><input type='submit' value='<?php echo Lang::T("SUBMIT"); ?>' /></td></tr>
</table></form>
</center>