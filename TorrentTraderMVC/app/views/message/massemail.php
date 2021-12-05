<center>
This page allows you to send a mass-email to all members, in the usergroups you choose.
</center>
<br />
<form id="massmail" name="massmail" method="post" action="<?php echo URLROOT; ?>/adminmassemail/send">
<input type="hidden" name="do" value="send" />
<table border="0" cellpadding="3" cellspacing="0" width="100%" align="center">
    <tr><td align="center">Subject: <input type="text" name="subject" size="40" /></td></tr>
    <tr><td align="center">To:
        <?php
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {?>
            <input type="checkbox" name="groups[]" value="<?php echo $row["group_id"]; ?>" /> <?php echo $row["level"]; ?>
            <?php
        } ?>
        <input type="checkbox" name="checkall" onclick="checkAll(this.form.id)" /> All
    </td></tr>
    <tr><td align="center"><?php echo textbbcode("massmail", "body"); ?></td></tr>
    <tr>
    <td colspan="2" align="center">
    <input type="submit" value="Send" />
    <input type="reset" value="Reset" />
    </td>
    </tr>
</table>
</form>