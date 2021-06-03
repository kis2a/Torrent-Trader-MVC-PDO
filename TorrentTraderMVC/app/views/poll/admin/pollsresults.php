<table class="table_table" align="center" cellpadding="0" cellspacing="0" width="95%">
<tr>
<th>Username</th>
<th>Question</th>
<th>Voted</th>
</tr>
<?php
while ($res = $data['poll']->fetch(PDO::FETCH_LAZY)) {
    $user = DB::run("SELECT username,id FROM users WHERE id =?", [$res['userid']])->fetch();
    $option = "option" . $res["selection"];
    if ($res["selection"] < 255) {
        $vote = DB::run("SELECT " . $option . " FROM polls WHERE id =?", [$res['pollid']])->fetch();
    } else {
        $vote["option255"] = "Blank vote";
    }
    $sond = DB::run("SELECT question FROM polls WHERE id =?", [$res['pollid']])->fetch();
    ?>
    <tr>
    <td><b><a href="<?php echo URLROOT; ?>/profile?id=<?php echo $user["id"]; ?>"><?php echo Users::coloredname($user['username']); ?></a></b></td>
    <td><?php echo $sond['question']; ?></td>
    <td><?php echo $vote["$option"]; ?></td>
    </tr>
    <?php
} ?>
</table>