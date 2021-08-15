<div class="row justify-content-center">
<div class="col-4">
<form method="post" action="<?php echo URLROOT; ?>/recover/submit">
<p class='text-center'><?php echo Lang::T("USE_FORM_FOR_ACCOUNT_DETAILS"); ?></p><br>
<div class="mb-6 row">
    <label for="name" class="col-sm-4 col-form-label"><?php echo Lang::T("EMAIL_ADDRESS"); ?>:</label>
    <div class="col-sm-8">
	<input id="name" type="text" class="form-control" name="email" minlength="3" maxlength="25" required autofocus>
    </div>
</div><br>
<div class="text-center">
    <?php (new Captcha)->html(); ?>
	<button type="submit" class="btn ttbtn"><?php echo Lang::T("Submit"); ?></button>
</div>
</form>
</div>
</div>