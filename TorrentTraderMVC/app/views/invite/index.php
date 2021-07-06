<div class="row justify-content-md-center">
<div class="col-6 border border-warning">
<form method="post" action="<?php echo URLROOT; ?>/invite/submit?take=1">
<div class="form-group">
	<label for="email"><?php echo Lang::T("EMAIL_ADDRESS"); ?>:</label>
	<input id="email" type="text" class="form-control" name="email" minlength="7" maxlength="25" required autofocus>
</div>
<?php echo Lang::T("EMAIL_ADDRESS_VALID_MSG"); ?><br><br>
<div class="form-group">
	<label for="mess"><?php echo Lang::T("MESSAGE"); ?>:</label><br>
	<textarea name="mess" class="form-control" id="mess" rows="7"></textarea>
</div> 
<div class="form-group">
<center>
	<button type="submit" class="btn btn-warning btn-sm"><?php echo Lang::T("SEND_AN_INVITE"); ?></button>
</center>
</div>
</form>
</div>
</div>