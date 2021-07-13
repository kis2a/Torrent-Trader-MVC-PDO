<?php usermenu($data['id']); ?>
<div class="row justify-content-md-center">
    <div class="col-6 border ttborder">
<div class="form-group">
    <form method="post" action="<?php echo URLROOT; ?>/account/changepw?id=<?php echo $data['id']; ?>">
    <input type="hidden" name="do" value="newpassword" />
    <div class="form-group">
	    <label for="name"><?php echo Lang::T("NEW_PASSWORD"); ?>:</label>
        <input id="name" type="password" class="form-control" name="chpassword" minlength="3" maxlength="25" required autofocus>
    </div>
    <div class="form-group">
	    <label for="name"><?php echo Lang::T("REPEAT"); ?>:</label>
        <input id="name" type="password" class="form-control" name="passagain" minlength="3" maxlength="25" required autofocus>
    </div>
    <div class="form-group">
    <center>
	    <button type="submit" class="btn ttbtn"><?php echo Lang::T("Submit"); ?></button>
    </center>
    </div>
    </form>
</div>
    </div>
</div>