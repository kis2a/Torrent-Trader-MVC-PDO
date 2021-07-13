<?php usermenu($data['id']); ?>
<div class="row justify-content-md-center">
    <div class="col-8 border ttborder">
<div class="form-group">
<form action="<?php echo URLROOT; ?>/account/email?id=<?php echo $data['id']; ?>" method="post">
    <div class="form-group">
	    <label for="name"><?php echo Lang::T("EMAIL"); ?>:</label>
        <input id="name" type="text" class="form-control" name="email" value='<?php echo htmlspecialchars($data["email"]); ?>' minlength="3" maxlength="25" required autofocus>
    </div>
    <div class="form-group">
    <center>
	    <button type="submit" class="btn ttbtn"><?php echo Lang::T("Submit"); ?></button>
    <center>
    </div>
</form>
</div>
</div>
</div>