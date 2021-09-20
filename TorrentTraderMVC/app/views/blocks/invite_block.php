<?php
if ((Config::TT()['INVITEONLY'] || Config::TT()['ENABLEINVITES']) && Auth::permission('loggedin') == true) {
   $invites = $_SESSION["invites"];
   Style::block_begin(Lang::T("INVITES"));
   ?>
   <div class="text-center">
	<?php printf(Lang::N("YOU_HAVE_INVITES", $invites), $invites);?> <br> <?php
   if ($invites > 0) {  ?>
      <a href="<?php echo URLROOT ?>/invite"><?php echo Lang::T("SEND_AN_INVITE"); ?></a> <?php
   }
   if (Auth::permission("invitees") > 0) { ?>
      <a href="<?php echo URLROOT ?>/invite/invitetree"><?php echo Lang::T("Invite Tree"); ?></a> <?php
   } ?>
   </div>
	<?php
   Style::block_end();
}