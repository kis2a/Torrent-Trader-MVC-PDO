<form method='post' action='<?php echo URLROOT ?>/adminbans/email?add=1'>
<div class="row justify-content-center">
<div class="col-md-6">
<center><?php echo Lang::T("EMAIL_BANS_INFO") ?><br>
     <b><?php echo Lang::T("ADD_EMAIL_BANS") ?></b>
</center>
<label for="mail_domain"><?php echo Lang::T("EMAIL_ADDRESS") . Lang::T("DOMAIN_BANS") ?></label>
	<input id="mail_domain" type="text" class="form-control" name="mail_domain" >
<label for="comment"><?php echo Lang::T("ADDCOMMENT") ?></label>
	<input id="comment" type="text" class="form-control" name="comment">
<input type='submit'  class='btn btn-sm ttbtn' value='<?php echo Lang::T("ADD_BAN") ?>' />
</div>
</div>
</form>

<br />
<center><b><?php echo Lang::T("EMAIL_BANS") ?> (<?php echo $data['count'] ?>)</b></center>
<?php
if ($data['count'] == 0) {
    print("<p align='center'><b>" . Lang::T("NOTHING_FOUND") . "</b></p><br />\n");
} else {
    echo $data['pagertop']; ?>
    <div class='table-responsive'> <table class='table table-striped'><thead><tr>
    <th>Added</th>
    <th>Mail Address Or Domain</th>
    <th>Banned By</th>
    <th>Comment</th>
    <th>Remove</th>
    </tr></thead>
    <?php
    while ($arr = $data['res']->fetch(PDO::FETCH_LAZY)) {
        $r2 = DB::run("SELECT username FROM users WHERE id=$arr[userid]");
        $a2 = $r2->fetch(PDO::FETCH_ASSOC);
        $r4 = DB::run("SELECT username,id FROM users WHERE id=$arr[addedby]");
        $a4 = $r4->fetch(PDO::FETCH_ASSOC);
        print("<tbody><tr>
        <td>" . TimeDate::utc_to_tz($arr['added']) . "</td>
        <td>$arr[mail_domain]</td>
        <td><a href='" . URLROOT . "/profile?id=$a4[id]'>$a4[username]" . "</a></td>
        <td>$arr[comment]</td>
        <td><a href='".URLROOT."/adminbans/email?remove=$arr[id]'>Remove</a></td>
        </tr></tbody>");
    }
    print("</table></div>");
    echo $data['pagerbottom'];
}