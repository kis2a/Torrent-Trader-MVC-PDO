<?php
if ($_SESSION['loggedin']) {

    $avatar = htmlspecialchars($_SESSION["avatar"]);
    if (!$avatar) {
        $avatar = URLROOT . "/assets/images/default_avatar.png";
    }

    $userdownloaded = mksize($_SESSION["downloaded"]);
    $useruploaded = mksize($_SESSION["uploaded"]);
    $privacylevel = Lang::T($_SESSION["privacy"]);
    $countslot = DB::run("SELECT DISTINCT torrent FROM peers WHERE userid =?  AND seeder=?", [$_SESSION['id'], 'yes']);
    $maxslotdownload = $countslot->rowCount();
    $slots = number_format($_SESSION["maxslots"]) . "/" . number_format($maxslotdownload);

    if ($_SESSION["uploaded"] > 0 && $_SESSION["downloaded"] == 0) {
        $userratio = '<span class="label label-success pull-right">Inf.</span>';
    } elseif ($_SESSION["downloaded"] > 0) {
        $userratio = '<span class="label label-info pull-right">' . number_format($_SESSION["uploaded"] / $_SESSION["downloaded"] , 2). '</span>';
    } else {
        $userratio = '<span class="label label-info pull-right">---</span>';
    }

    Style::block_begin(User::coloredname($_SESSION["username"]));
    ?>

        <img class="embed-responsive" src="<?php echo $avatar; ?>" alt="Avatar"  />
	<ul class="list-group">
		<li class="list-group-item"><?php echo Lang::T("DOWNLOADED"); ?> : <span class="label label-danger pull-right"><?php echo $userdownloaded; ?></span></li>
		<li class="list-group-item"><?php echo Lang::T("UPLOADED"); ?>: <span class="label label-success pull-right"><?php echo $useruploaded; ?></span></li>
		<li class="list-group-item"><?php echo Lang::T("CLASS"); ?>: <div class="pull-right"><?php echo Lang::T($_SESSION["level"]); ?></div></li>
		<li class="list-group-item"><?php echo Lang::T("ACCOUNT_PRIVACY_LVL"); ?>: <div class="pull-right"><?php echo $privacylevel; ?></div></li>
		<li class="list-group-item"><?php echo Lang::T("Seed Bonus"); ?>: <a href="<?php echo URLROOT; ?>/bonus"><div class="pull-right"><?php echo $_SESSION['seedbonus']; ?></div></a></span></li>
		<li class="list-group-item"><?php echo Lang::T("RATIO"); ?>: <?php echo $userratio; ?></span></li>
		<li class="list-group-item"><?php echo Lang::T("Available Slots"); ?>: <div class="pull-right"><?php echo $slots; ?></div></span></li>
    </ul>
    <br />
	<div class="text-center">
	<a href='<?php echo URLROOT; ?>/profile?id=<?php echo $_SESSION["id"]; ?>'><button class="btn btn-warning"><?php echo Lang::T("ACCOUNT"); ?></button></a>
		<?php if ($_SESSION["control_panel"] == "yes") {?>
		<a href="<?php echo URLROOT; ?>/admincp" class="btn btn-warning"><?php echo Lang::T("STAFFCP"); ?></a>
		<?php }?>
	</div>
	<br />
        <!-- end content -->

    <?php Style::block_end();
}/* else {
    Style::block_begin('Login');
    ?>
<form method="post" action="<?php echo URLROOT ?>/login/submit">
<input type="hidden" name="csrf_token" value="<?php echo Cookie::csrf_token(); ?>" />
<table border="0" width="100%" cellspacing="0" cellpadding="0">
	<tr><td>
		<table border="0" cellpadding="1" align="center">
			<tr>
			<td align="center"><font face="verdana" size="1"><b><?php echo Lang::T("USERNAME"); ?>:</b></font></td>
			</tr><tr>
			<td align="center"><input type="text" size="12" name="username" /></td>
			</tr><tr>
			<td align="center"><font face="verdana" size="1"><b><?php echo Lang::T("PASSWORD"); ?>:</b></font></td>
			</tr><tr>
			<td align="center"><input type="password" size="12" name="password"  /></td>
			</tr><tr>
			<td align="center">
			<button type='submit' class='btn btn-sm btn-warning' value='Login'><?php echo Lang::T("LOGIN"); ?></button>
			</td>
		</table>
		</td>
		</tr>
	<tr>
<td align="center">[<a href="<?php echo URLROOT ?>/signup"><?php echo Lang::T("SIGNUP");?></a>]<br />[<a href="<?php echo URLROOT ?>/recover"><?php echo Lang::T("RECOVER_ACCOUNT");?></a>]</td> </tr>
	</table>
    </form> 
    <?php Style::block_end();
}*/