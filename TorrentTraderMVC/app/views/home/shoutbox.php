<?php
Style::begin(Lang::T("SHOUTBOX"));
?>
<p id="shoutbox"></p>
<form name='shoutboxform' action='<?php echo URLROOT ?>/shoutbox/add' method='post'>
<div class="row">
    <div class="col-md-12">
        <?php
        echo shoutbbcode("shoutboxform", "message");
        ?>
    </div>
</div>
</form>
<?php
Style::end();