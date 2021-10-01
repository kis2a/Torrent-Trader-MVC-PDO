<?php
$test = DB::run("SELECT sub FROM forum_forums WHERE id = $data[forumid]")->fetch(); // sub forum mod
$test1 = DB::run("SELECT `name`,`id` FROM forum_forums WHERE id = $test[sub]")->fetch(); // sub forum mod

forumheader($data['forumname'], $test1['name'], $test1['id']);

$testz = DB::run("SELECT * FROM forum_forums WHERE sub = $data[forumid]")->fetchAll(PDO::FETCH_ASSOC); // sub forum mod
if ($testz) { 
?>
<div class="row frame-header">
<div class="col-md-8">
Sub Forums
</div>
<div class="col-md-1 d-none d-sm-block">
    Topics
</div>
<div class="col-md-1 d-none d-sm-block">
    Posts
</div>
<div class="col-md-2 d-none d-sm-block">
    Last post
</div>
</div>
<?php foreach ($testz as $testy) { ?>
<div class="row border ttborder">
    <div class="col-md-8">
    <a href='<?php echo URLROOT ?>/forums/viewforum&amp;forumid=<?php echo $testy['id'] ?>'><b><?php echo $testy['name'] ?></b></a>
    </div>
    <div class="col-md-1 d-none d-sm-block">
        <?php
    $topiccount = number_format(get_row_count("forum_topics", "WHERE forumid = $testy[id]"));
    echo $topiccount;
        ?>
    </div>
    <div class="col-md-1 d-none d-sm-block">
    <?php
    $postcount = number_format(get_row_count("forum_posts", "WHERE topicid IN (SELECT id FROM forum_topics WHERE forumid=$testy[id])"));
    echo $postcount;
        ?>
    </div>
    <div class="col-md-2 d-none d-sm-block">
    <?php
    $lastpostid = get_forum_last_post($testy['id']);
    // Get last post info in a array return img & lastpost
    $detail = lastpostdetails($lastpostid);
    echo $detail['lastpost'];
        ?>
    </div>
</div>
<?php } ?><br><?php

latestforumposts($data['forumid']); // mod

}

if ($_SESSION['loggedin'] == true) {
    ?>
    <div class="d-flex flex-row-reverse"><a href='<?php echo URLROOT; ?>/forums/newtopic&amp;forumid=<?php echo $data['forumid']; ?>'  class='btn btn-sm ttbtn'>New Post</a></div>
    <?php
}
if ($data['topicsres'] > 0) {
    ?>
    <div class="row">
    <div class="col-lg-12">
    <div class="wrapper wrapper-content animated fadeInRight">

    <div class="row frame-header">
    <div class="col-md-1">
    Read
    </div>
    <div class="col-md-4">
    Topic
    </div>
    <div class="col-md-1 d-none d-sm-block">
    Replies
    </div>
    <div class="col-md-1 d-none d-sm-block">
    Views
    </div>
    <div class="col-md-1 d-none d-sm-block">
    Author
    </div>
    <div class="col-md-2 d-none d-sm-block">
    Last Post
    </div>
    <?php
    if ($_SESSION["edit_forum"] == "yes" || $_SESSION["delete_forum"] == "yes") {
        ?>
        <div class="col-md-2 d-none d-sm-block">
        Moderate
        </div>
        <?php
    }
    print("</div>");

    foreach ($data['topicsres'] as $topicarr) {
        $topicid = $topicarr["id"];
        $topic_userid = $topicarr["userid"];
        $locked = $topicarr["locked"] == "yes";
        $moved = $topicarr["moved"] == "yes";
        $sticky = $topicarr["sticky"] == "yes";
        //---- Get reply count
        $res = DB::run("SELECT COUNT(*) FROM forum_posts WHERE topicid=$topicid");
        $arr = $res->fetch(PDO::FETCH_LAZY);
        $posts = $arr[0];
        $replies = max(0, $posts - 1);
        //---- Get userID and date of last post
        $res = DB::run("SELECT * FROM forum_posts WHERE topicid=$topicid ORDER BY id DESC LIMIT 1");
        $arr = $res->fetch(PDO::FETCH_ASSOC);
        $lppostid = $arr["id"];
        $lpuserid = (int) $arr["userid"];
        $lpadded = TimeDate::utc_to_tz($arr["added"]);
        //------ Get name of last poster
        if ($lpuserid > 0) {
            $res = DB::run("SELECT * FROM users WHERE id=$lpuserid");
            if ($res->rowCount() == 1) {
                $arr = $res->fetch(PDO::FETCH_ASSOC);
                $lpusername = "<a href='" . URLROOT . "/profile?id=$lpuserid'>" . Users::coloredname($arr['username']) . "</a>";
            } else {
                $lpusername = "Deluser";
            }
        } else {
            $lpusername = "Deluser";
        }
        //------ Get author
        if ($topic_userid > 0) {
            $res = DB::run("SELECT username FROM users WHERE id=$topic_userid");
            if ($res->rowCount() == 1) {
                $arr = $res->fetch(PDO::FETCH_ASSOC);
                $lpauthor = "<a href='" . URLROOT . "/profile?id=$topic_userid'>" . Users::coloredname($arr['username']) . "</a>";
            } else {
                $lpauthor = "Deluser";
            }
        } else {
            $lpauthor = "Deluser";
        }
        // Topic Views
        $viewsq = DB::run("SELECT views FROM forum_topics WHERE id=$topicid");
        $viewsa = $viewsq->fetch(PDO::FETCH_LAZY);
        $views = $viewsa[0];
        // End
        //---- Print row
        if ($_SESSION) {
            $r = DB::run("SELECT lastpostread FROM forum_readposts WHERE userid=$_SESSION[id] AND topicid=$topicid");
            $a = $r->fetch(PDO::FETCH_LAZY);
        }
        $new = !$a || $lppostid > $a[0];
        $topicpic = ($locked ? ($new ? "fa fa-lock fa-2x" : "fa fa-unlock fa-2x") : ($new ? "fa fa-file-text fa-2x" : "fa fa-file-text fa-2x"));
        $subject = ($sticky ? "<b>" . Lang::T("FORUMS_STICKY") . ": </b>" : "") . "<a href='" . URLROOT . "/forums/viewtopic&amp;topicid=$topicid'><b>" .
        encodehtml(stripslashes($topicarr["subject"])) . "</b></a>$topicpages";
        ?>
        <div class="row border ttborder">
        <div class="col-md-1 d-none d-sm-block">
        <i class='<?php echo $topicpic ?> tticon' title='Lock Topic'></i>
        </div>
        <div class="col-md-4">
        <?php echo $subject; ?>
        </div>
        <div class="col-md-1 d-none d-sm-block">
        <?php echo $replies; ?>
        </div>
        <div class="col-md-1 d-none d-sm-block">
        <?php echo $views; ?>
        </div>
        <div class="col-md-1 d-none d-sm-block">
        <?php echo $lpauthor; ?>
        </div>
        <div class="col-md-2">
        <span class='small'>by&nbsp;<?php echo $lpusername; ?><br /><span style='white-space: nowrap'><?php echo $lpadded; ?></span></span>
        </div>
        <?php
    
        if ($_SESSION["edit_forum"] == "yes" || $_SESSION["delete_forum"] == "yes") {
            print("<div class='col-md-2 d-none d-sm-block'>");
            if ($locked) {
                print("<a href='" . URLROOT . "/forums/unlocktopic&amp;forumid=$data[forumid]&amp;topicid=$topicid&amp;page=$page' title='Unlock'><i class='fa fa-unlock tticon-red' title='UnLock Topic'></i></a>\n");
            } else {
                print("<a href='" . URLROOT . "/forums/locktopic&amp;forumid=$data[forumid]&amp;topicid=$topicid&amp;page=$page' title='Lock'><i class='fa fa-lock tticon' title='Lock Topic'></i></a>\n");
            }
            print("<a href='" . URLROOT . "/forums/deletetopic&amp;topicid=$topicid&amp;sure=0' title='Delete'><i class='fa fa-trash-o tticon-red' title='Delete Topic'></i></a>\n");
            if ($sticky) {
                print("<a href='" . URLROOT . "/forums/unsetsticky&amp;forumid=$data[forumid]&amp;topicid=$topicid&amp;page=$page' title='UnStick'><i class='fa fa-exclamation tticon-red' title='Unstick Topic'></i></a>\n");
            } else {
                print("<a href='" . URLROOT . "/forums/setsticky&amp;forumid=$data[forumid]&amp;topicid=$topicid&amp;page=$page' title='Stick'><i class='fa fa-exclamation tticon' title='". Lang::T("FORUMS_STICKY") ."'></i>
                </a>\n");
            }
            print("</div>");
        }
        print("</div>");
    }

    print("</div></div></div>");
    print ($data['pagerbottom']);
} else {
    print("<p align='center'>No topics found</p>\n");
}

print("<table cellspacing='5' cellpadding='0'><tr valign='middle'>\n");
print("<td><i class='fa fa-file-text tticon-red' title='UnRead'></td><td >New posts</td>\n");
print("<td><i class='fa fa-file-text tticon' title='Read'>" .
    "</td><td>No New posts</td>\n");
print("<td><i class='fa fa-lock tticon' title='Lock'></i>
</td><td>" . Lang::T("FORUMS_LOCKED") . " </td></tr></tbody></table>\n");
print("<table cellspacing='0' cellpadding='0'><tr>\n");
print("</tr></table>\n");
insert_quick_jump_menu($data['forumid']);