<div class="text-center">
    Add Group
</div>
<form action="<?php echo URLROOT; ?>/admingroups/addnew" name="level" method="post">
<div class="row justify-content-md-center">
    <div class="col-6 ttborder">
        Group Name:</br>
        <input type="text" name="gname" value="" size="40" /></br>
        Group colour:</br>
        <input type="text" name="gcolor" value="" size="10" /></br>
        Copy Settings From:</br>
        <select name="getlevel" size="1">
            <?php
            while ($level = $data['rlevel']->fetch(PDO::FETCH_ASSOC)) {
                print("\n<option value='" . $level["group_id"] . "'>" . htmlspecialchars($level["level"]) . "</option>");
            }
            ?>
        </select></br>

        <div class="text-center">
        <input type="submit" name="confirm" value="Confirm">
        </div>
    </div>
</div>
</form><br />