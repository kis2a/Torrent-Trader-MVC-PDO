<center><b><?php echo Lang::T("ACCOUNT_SEND_MSG"); ?></b></center>
<div class="row justify-content-md-center">
    <div class="col-6 border ttborder">

<form method=post name=message action='<?php echo URLROOT; ?>/contactstaff/submit'>
<div class="form-group">

<div class="form-group">
	<label for="name"><?php echo Lang::T("FORUMS_SUBJECT"); ?>: </label>
	<input id="name" type="text" class="form-control" name="sub" minlength="3" maxlength="200" required autofocus>
</div>
<div class="form-group">
    <label for="msg"><?php echo Lang::T("MESSAGE"); ?>: </label>
    <textarea class="form-control" id="msg" name="msg" rows="15"></textarea>
</div>
<div class="text-center">
    <?php (new Captcha)->html(); ?>
	<button type="submit" class="btn ttbtn btn-sm"><?php echo Lang::T("Submit"); ?></button>
</div>

</div>
</form>
</div>
</div>