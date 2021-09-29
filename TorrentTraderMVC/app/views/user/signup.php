<div class="row justify-content-center">
<div class="col-4">
<form method="post" action="<?php echo URLROOT; ?>/signup/submit">
<?php
if ($data['invite'] != 0) {?>
    <input type="hidden" name="invite" value="<?php echo $_GET["invite"]; ?>" />
    <input type="hidden" name="secret" value="<?php echo htmlspecialchars($_GET["secret"]); ?>" />
    <?php
} ?>

<p class="text-center"><?php echo Lang::T("COOKIES"); ?></p><br>

<div class="mb-3 row">
    <label for="wantusername" class="col-sm-3 col-form-label"><?php echo Lang::T("USERNAME"); ?>:</label>
    <div class="col-sm-9">
	<input id="wantusername" type="text" class="form-control" name="wantusername" minlength="3" maxlength="25" required autofocus>
     </div>
</div><br>

<div class="mb-3 row">
    <label for="wantpassword" class="col-sm-3 col-form-label"><?php echo Lang::T("PASSWORD"); ?>:</label>
    <div class="col-sm-9">
	<input id="wantpassword" type="password" class="form-control" name="wantpassword" minlength="6" maxlength="25" required autofocus>
    </div>
</div><br>

<div class="mb-3 row">
     <label for="passagain" class="col-sm-3 col-form-label"><?php echo Lang::T("CONFIRM"); ?>:</label>
     <div class="col-sm-9">
	<input id="passagain" type="password" class="form-control" name="passagain" minlength="6" maxlength="25" required autofocus>
     </div>
</div><br>

<?php
if ($data['invite'] == 0) { ?>
     <div class="mb-3 row">
       <label for="email" class="col-sm-3 col-form-label"><?php echo Lang::T("EMAIL"); ?>:</label>
       <div class="col-sm-9">
	   <input id="email" type="text" class="form-control" name="email" minlength="3" maxlength="25" required autofocus>
       </div>
     </div><br><?php
} ?>

<div class="mb-3 row">
    <label for="age" class="col-sm-3 col-form-label"><?php echo Lang::T("AGE"); ?>:</label>
    <div class="col-sm-9">
	<input id="age" type="text" class="form-control" name="age" minlength="2" required autofocus>
     </div>
</div><br>

<div class="mb-3 row">
    <label for="country" class="col-sm-3 col-form-label"><?php echo Lang::T("COUNTRY"); ?>:</label><br>
    <div class="col-sm-9">
       <select name="country" size="1">
	   <?php Countries::echoCountry(); ?>
	</select>
     </div>
</div><br>

<div class="mb-3 row">
   <label for="name" class="col-sm-3 col-form-label"><?php echo Lang::T("GENDER"); ?>:</label><br>
   <div class="col-sm-9">
      <input class="form-check-input" type="checkbox" name="gender" id="Male" value="Male">
      <label class="form-check-label" for="gender">Male</label>
      <input class="form-check-input" type="checkbox" name="gender" id="Female" value="Female">
      <label class="form-check-label" for="gender">Female</label>
   </div>
</div><br>

<div class="mb-3 row">
    <label for="client" class="col-sm-3 col-form-label"><?php echo Lang::T("PREF_BITTORRENT_CLIENT"); ?>:</label>
    <div class="col-sm-9">
	<input id="client" type="text" class="form-control" name="client" minlength="3" maxlength="25" required autofocus>
    </div>
</div><br>

<div class="text-center">
    <?php (new Captcha)->html(); ?>
    <button type="submit" class="btn ttbtn btn-block"><?php echo Lang::T("SIGNUP"); ?></button>
</div>
</form>
</div>
</div>