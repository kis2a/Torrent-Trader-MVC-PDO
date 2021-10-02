<?php
if ((Config::TT()['INVITEONLY'] || Config::TT()['ENABLEINVITES']) && Users::has('loggedin') == true) {
   $invites = $_SESSION["invites"];
   Style::block_begin(Lang::T("INVITES"));
   ?>
   <div class="text-center">
	<?php printf(Lang::N("YOU_HAVE_INVITES", $invites), $invites);?> <br> <?php
   if ($invites > 0) {  ?>
      <a href="<?php echo URLROOT ?>/invite" class="btn ttbtn"><?php echo Lang::T("Send"); ?></a> <?php
   }
   if (Users::has("invitees") > 0) { ?>
      <a href="<?php echo URLROOT ?>/invite/invitetree" class="btn ttbtn"><?php echo Lang::T("Invited"); ?></a> <?php
   } ?>
   </div>
	<?php
   Style::block_end();
}