<?php
Style::begin(Lang::T("Warning")); ?>
<table><tr>
Hey <?php echo Auth::permission('username'); ?>,
you have <?php echo  $hnr; ?> recording <?php echo ($hnr2 > 1 ? "s" : "") ?> for Hit and Run!&nbsp; 
View the recordings into <a href='<?php echo URLROOT; ?>/snatched'><b>Your Snatch List</b></a>
You must to keep seeding or you can <a href='<?php echo URLROOT; ?>/bonus/trade'><b>Trade to Delete</b></a>
<?php echo  ($hnr2 > 1 ? "these recordings" : "this recording") ?> with Seed Bonus or Upload
</td></tr></table> <?
Style::end();