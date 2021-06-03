<p id="shoutboxstaff"></p>
<form name='shoutboxform' action='<?php echo URLROOT ?>/shoutbox/add' method='post'>
<div class="row">
    <div class="col-md-12">
    <?php
    include APPROOT . '/helpers/bbcode_helper.php';
    echo shoutbbcode("shoutboxform", "message");
    ?>
    </div>
</div>
</form>