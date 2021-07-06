<center><font color=#ffff00><b>Send message to Staff</b></font></center>
<div class="row justify-content-md-center">
    <div class="col-6 border border-warning">

<form method=post name=message action='<?php echo URLROOT; ?>/contactstaff/submit'>
<div class="form-group">

<div class="form-group">
	<label for="name">Subject: </label>
	<input id="name" type="text" class="form-control" name="sub" minlength="3" maxlength="75" required autofocus>
</div>
<div class="form-group">
    <label for="msg">Message: </label>
    <textarea class="form-control" id="msg" name="msg" rows="3"></textarea>
</div>
<div class="form-group">
<center>
	<button type="submit" class="btn btn-warning btn-sm"><?php echo Lang::T("Submit"); ?></button>
</center>
</div>

</div>
</form>
</div>
</div>