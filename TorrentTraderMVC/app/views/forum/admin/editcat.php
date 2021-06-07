<form action="<?php echo URLROOT; ?>/adminforum/saveeditcat" method="post">
<input type="hidden" name="id" value="<?php echo $data['id']; ?>" />
<table class='f-border a-form' align='center' width='80%' cellspacing='2' cellpadding='5'>
<tr class='f-title'><td><?php echo Lang::T("CP_FORUM_NEW_NAME_CAT"); ?>:</td></tr>
<tr><td align='center'><input type="text" name="changed_forumcat" class="option" size="35" value="<?php echo $data["name"]; ?>" /></td></tr>
<tr><td align='center'<td><?php echo Lang::T("CP_FORUM_NEW_SORT_ORDER"); ?>:</td></tr>
<tr><td align='center'><input type="text" name="changed_sortcat" class="option" size="35" value="<?php echo $data["sort"]; ?>" /></td></tr>
<tr><td align='center'><input type="submit" class="button" value="Change" /></td></tr>
</table>
</form>