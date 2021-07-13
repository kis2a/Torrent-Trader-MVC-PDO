<center><a href='<?php echo URLROOT; ?>/adminrules/addsect'>Add New Rules Section</a></center>
<?php
while ($arr = $data['res']->fetch(PDO::FETCH_LAZY)) { ?>
    <div class="border ttborder">
    <table width='100%' cellspacing='0' class='table_table'><tr>
    <th><?php echo  $arr["title"]; ?></th>
    </tr>
    <tr><td>
    <form method='post' action='<?php echo URLROOT; ?>/adminrules/edit'>
    <?php echo format_comment($arr["text"]); ?>
    </td></tr>
    <tr><td><input type='hidden' value=<?php echo $arr['id']; ?> name='id' />
    <center><input type='submit' value='Edit' /></center></form>
    </td>
    </tr></table>
    </div><br>
<?php
}