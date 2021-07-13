<?php echo Lang::T("EMAIL_BANS_INFO") ?><br />
<br /><b><?php echo Lang::T("ADD_EMAIL_BANS") ?></b>
<form method='post' action='<?php echo URLROOT ?>/adminbans/email?add=1'>
<table border='0' cellspacing='0' cellpadding='5' align='center'>
<tr><td align='right'><?php echo Lang::T("EMAIL_ADDRESS") . Lang::T("DOMAIN_BANS") ?></td><td><input type='text' name='mail_domain' size='40' /></td></tr>
<tr><td align='right'><?php echo Lang::T("ADDCOMMENT") ?></td><td><input type='text' name='comment' size='40' /></td></tr>
<tr><td colspan='2' align='center'><input type='submit' value='<?php echo Lang::T("ADD_BAN") ?>' /></td></tr>
</table></form><br />
<br /><b><?php echo Lang::T("EMAIL_BANS") ?> (<?php echo $data['count'] ?>)</b>
<?php
if ($data['count'] == 0) {
    print("<p align='center'><b>" . Lang::T("NOTHING_FOUND") . "</b></p><br />\n");
} else {
    echo $data['pagertop'];
    print("<table border='0' cellspacing='0' cellpadding='5' width='90%' align='center' class='table_table'>\n");
    print("<tr><th class='table_head'>Added</th><th class='table_head'>Mail Address Or Domain</th><th class='table_head'>Banned By</th><th class='table_head'>Comment</th><th class='table_head'>Remove</th></tr>\n");
    while ($arr = $data['res']->fetch(PDO::FETCH_LAZY)) {
        $r2 = DB::run("SELECT username FROM users WHERE id=$arr[userid]");
        $a2 = $r2->fetch(PDO::FETCH_ASSOC);
        $r4 = DB::run("SELECT username,id FROM users WHERE id=$arr[addedby]");
        $a4 = $r4->fetch(PDO::FETCH_ASSOC);
        print("<tr><td class='table_col1'>" . TimeDate::utc_to_tz($arr['added']) . "</td><td align='left' class='table_col2'>$arr[mail_domain]</td><td align='left' class='table_col1'><a href='" . URLROOT . "/profile?id=$a4[id]'>$a4[username]" . "</a></td><td align='left' class='table_col2'>$arr[comment]</td><td class='table_col1'><a href='".URLROOT."/adminbans/email?remove=$arr[id]'>Remove</a></td></tr>\n");
    }
    print("</table>\n");
    echo $data['pagerbottom'];
    echo "<br />";
}