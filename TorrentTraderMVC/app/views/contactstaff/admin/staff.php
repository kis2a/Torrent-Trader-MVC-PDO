<form method=post action="<?php echo URLROOT; ?>/admincontactstaff/takecontactanswered">
<table class='table table-striped table-bordered table-hover'><thead>
    <tr>
    <td>Subject</td>
    <td>Sender</td>
    <td>Added</td>
    <td>Answered</td>
    <td>Set Answered</td>
    <td>Del</td>
    </tr>
</thead><tbody>
<?php
while ($arr = $data['res']->fetch(PDO::FETCH_ASSOC)) {
    if ($arr['answered']) {
        $res3 = DB::run("SELECT username FROM users WHERE id=$arr[answeredby]");
        $arr3 = $res3->fetch(PDO::FETCH_ASSOC);
        $answered = "<font color=green><b>Yes - <a href=".URLROOT."/profile?id=$arr[answeredby]><b>" . Users::coloredname($arr3['username']) . "</b></a> (<a href=".URLROOT."/admincontactstaff/viewanswer?pmid=$arr[id]>View Answer</a>)</b></font>";
    } else {
        $answered = "<font color=red><b>No</b></font>";
    }
    $pmid = $arr["id"]; ?>
    <tr>
    <td><a href='<?php echo URLROOT; ?>/admincontactstaff/viewpm?pmid=<?php echo $pmid; ?>'><b><?php echo $arr['subject']; ?></b></td>
    <td><a href='<?php echo URLROOT; ?>/profile?id=$arr[sender]'><b><?php echo Users::coloredname($arr['username']); ?></b></a></td>
    <td><?php echo $arr['added']; ?></td><td align=left><?php echo $answered; ?></td>
    <td><input type="checkbox" name="setanswered[]" value="<?php echo $arr['id']; ?>"></td>
    <td><a href='<?php echo URLROOT; ?>/admincontactstaff/deletestaffmessage?id=<?php echo $arr['id']; ?>''>Del</a></td>
    </tr></tbody>
    <?php
} ?>
</table>

<div class="form-group">
    <button type="submit" class="btn btn-warning btn-sm"><?php echo Lang::T("Confirm"); ?></button>
</div>
</form>