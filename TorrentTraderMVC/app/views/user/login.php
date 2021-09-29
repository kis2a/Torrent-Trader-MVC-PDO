<?php
if (Config::TT()['MEMBERSONLY']) {?>
    <p class='text-center'><b><?php echo Lang::T("MEMBERS_ONLY"); ?></b></p> <?php
}?>
<div class="row justify-content-center">
    <div class="col-4">
    <form method="post" action="<?php echo URLROOT; ?>/login/submit" autocomplete="off">
    <input type="hidden" name="csrf_token" value="<?php echo $data['token'] ?>" />
    <div class="mb-3 row">
       <label for="username" class="col-sm-3 col-form-label"><?php echo Lang::T("USERNAME"); ?>:</label>
       <div class="col-sm-9">
       <input id="username" type="text" class="form-control" name="username" minlength="3" maxlength="25" required autofocus>
       </div>
    </div><br>
    <div class="mb-3 row">
       <label for="password" class="col-sm-3 col-form-label"><?php echo Lang::T("PASSWORD"); ?>:</label>
       <div class="col-sm-9">
       <input id="password" type="password" class="form-control" name="password" minlength="6" maxlength="16" required data-eye>
       </div>
    </div><br>
    <div class="text-center">
        <?php (new Captcha)->html(); ?>
        <button type="submit" class="btn ttbtn "><?php echo Lang::T("LOGIN"); ?></button><br><br>
        <p class='text-center'><i><?php echo Lang::T("COOKIES"); ?></i></p>
	</div>
    <div class="margin-top20 text-center">
        <a href="<?php echo URLROOT; ?>/signup"><?php echo Lang::T("SIGNUP"); ?></a> | 
        <a href="<?php echo URLROOT; ?>/recover"><?php echo Lang::T("RECOVER_ACCOUNT"); ?></a> | 
        <a href="<?php echo URLROOT ?>/contactstaff"><?php echo Lang::T("Contact Us"); ?></a>
	</div>
    </form>
    </div>
</div>