<?php
Style::begin(Lang::T("Warning"));
?>
Hey <?php echo Users::has('username'); ?>,
you have <b></b><?php echo  $data['count']; ?></b> Hit and Run!&nbsp; 
View the recordings in <a href='<?php echo URLROOT; ?>/snatched/user?id=<?php echo Users::has('id'); ?>'><b>Your Snatch List</b></a>.
You must to keep seeding or you can <a href='<?php echo URLROOT; ?>/bonus/trade'><b>Trade to Delete</b></a>
<?
Style::end();