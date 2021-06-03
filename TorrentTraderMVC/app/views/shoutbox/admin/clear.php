<font size ='3'><center><?php echo Lang::T("CLEAR_SHOUTBOX_TEXT"); ?></center></font>
<form enctype="multipart/form-data" method="post" action="<?php echo URLROOT; ?>/adminshoutbox/clear?do=delete">
<input type="hidden" name="action" value="clearshout" />
<input type="hidden" name="do" value="delete" />
<table class="f-border" cellspacing="0" cellpadding="5" width="100%" align="center">
    <tr><td colspan="2" align="center"><input type="submit" value="<?php echo Lang::T("CLEAR_SHOUTBOX"); ?>" /></td></tr>
</table></form>