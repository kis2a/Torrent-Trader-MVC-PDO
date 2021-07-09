<center><a href='<?php echo URLROOT; ?>/adminnews/add'><b><?php echo Lang::T("CP_NEWS_ADD_ITEM"); ?></b></a></center><br />
<?php
if ($data['sql']->rowCount() > 0) {
    while ($arr = $data['sql']->fetch(PDO::FETCH_ASSOC)) {
        $newsid = $arr["id"];
        $body = format_comment($arr["body"]);
        $title = $arr["title"];
        $userid = $arr["userid"];
        $added = $arr["added"] . " GMT (" . (TimeDate::get_elapsed_time(TimeDate::sql_timestamp_to_unix_timestamp($arr["added"]))) . " ago)";
        $arr2 = DB::run("SELECT username FROM users WHERE id =?", [$userid])->fetch();
        $postername = Users::coloredname($arr2["username"]);
        if ($postername == "") {
            $by = "Unknown";
        } else {
            $by = "<a href='" . URLROOT . "/profile?id=$userid'><b>$postername</b></a>";
        }
        ?>
        <table border='0' cellspacing='0' cellpadding='0'><tr><td>
        <?php echo $added; ?>&nbsp;---&nbsp;by&nbsp;<?php echo $by; ?>
         - [<a href='<?php echo  URLROOT; ?>/adminnews/edit?newsid=<?php echo $newsid; ?>'><b><?php echo Lang::T("EDIT"); ?></b></a>]
         - [<a href='<?php echo URLROOT; ?>/adminnews/newsdelete?newsid=<?php echo $newsid; ?>'><b><?php echo Lang::T("DEL"); ?></b></a>]
        </td></tr>
        <tr valign='top'><td><b><?php echo $title; ?></b><br /><?php echo $body; ?></td></tr></table><br />
        <?php
        }
} else {
    echo "No News Posted";
}