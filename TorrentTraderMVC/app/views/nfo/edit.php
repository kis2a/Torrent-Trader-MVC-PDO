<div class="row justify-content-md-center">
    <div class="col-md-6">

<form method="post" action="<?php echo URLROOT; ?>/nfo/submit">
    <input type="hidden" name="id" value="<?php echo $data['id']; ?>" />
    <input type="hidden" name="do" value="update" />
    <textarea class="nfo" name="content" cols="60%" rows="20"><?php echo stripslashes($data['contents']); ?></textarea><br />
    <center>
    <input type="reset" value="<?php echo Lang::T("RESET"); ?>" />
    <button type='submit' class='btn btn-sm btn-warning'><?php echo Lang::T("SAVE"); ?></button>
    </center>
</form>

<form method="post" action="<?php echo URLROOT; ?>/nfo/delete">
    <input type="hidden" name="id" value="<?php echo $data['id']; ?>" />
    <br><b>Delete NFO</b><br>
    <input type="hidden" name="do" value="delete" />
    <center>
    <b><?php echo Lang::T("NFO_REASON"); ?>:</b> <input type="text" name="reason" size="40" />
    <button type='submit' class='btn btn-sm btn-warning'><?php echo Lang::T("DEL"); ?></button>
    </center>
</form>
    </div>
</div>