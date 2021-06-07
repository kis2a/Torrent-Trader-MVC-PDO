<center><b>You must have at least one censored word</b></center>
<form action='<?php echo URLROOT ?>/admincensor&to=write' method="post" enctype="multipart/form-data">
<table width="100%" align="center">
<tr>
    <td align="center"><?php echo $LANG['ACP_CENSORED_NOTE'] ?></td>
</tr>
<tr>
    <td align="center"><textarea name="badwords" rows="20" cols="60"><?php echo $data['badwords'] ?></textarea></td>
</tr>
<tr>
    <td align="center">
    <input type="submit" name="write" value="<?php echo Lang::T("CONFIRM") ?>" />&nbsp;&nbsp;
    <input type="submit" name="write" value="<?php echo Lang::T("CANCEL") ?>" />
    </td>
</tr>
</table>
</form><br />