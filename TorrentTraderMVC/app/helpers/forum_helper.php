<?php
// setup the forum header
function forumheader($location, $subforum = '', $subforumid = 0)
{
    echo "<div>
    <i class='fa fa-question tticon' title='Help'></i>&nbsp;<a href='" . URLROOT . "/faq'>" . Lang::T("FORUM_FAQ") . "</a>&nbsp; &nbsp;&nbsp;
    <i class='fa fa-search tticon' title='Search'></i>&nbsp;<a href='" . URLROOT . "/forums/search'>" . Lang::T("SEARCH") . "</a>&nbsp; &nbsp;
    <b>" . Lang::T("FORUM_CONTROL") . "</b>
    &middot; <a href='" . URLROOT . "/forums/viewunread'>" . Lang::T("FORUM_NEW_POSTS") . "</a>
    &middot; <a href='" . URLROOT . "/forums?do=catchup'>" . Lang::T("FORUM_MARK_READ") . "</a>
    </div><br />";
    if ($subforum == '') {
        print("<div>" . Lang::T("YOU_ARE_IN") . ": &nbsp;<a href='" . URLROOT . "/forums'>" . Lang::T("FORUMS") . "</a> <b style='vertical-align:middle'>/ $location</b></div>");
    } else {
        print("<div>" . Lang::T("YOU_ARE_IN") . ": &nbsp;<a href='" . URLROOT . "/forums'>" . Lang::T("FORUMS") . "</a>/<a href='" . URLROOT . "/forums/viewforum&forumid=$subforumid'>$subforum</a><b style='vertical-align:middle'>/ $location</b></div>");
    }
}

// Mark all forums as read
function catch_up()
{
    if (!$_SESSION['loggedin'] == true) {
        return;
    }
    $userid = $_SESSION["id"];
    $res = DB::run("SELECT id, lastpost FROM forum_topics");
    while ($arr = $res->fetch(PDO::FETCH_ASSOC)) {
        $topicid = $arr["id"];
        $postid = $arr["lastpost"];
        $r = DB::run("SELECT id,lastpostread FROM forum_readposts WHERE userid=? and topicid=?", [$userid, $topicid]);
        if ($r->rowCount() == 0) {
            DB::run("INSERT INTO forum_readposts (userid, topicid, lastpostread) VALUES(?, ?, ?)", [$userid, $topicid, $postid]);
        } else {
            $a = $r->fetch(PDO::FETCH_ASSOC);
            if ($a["lastpostread"] < $postid) {
                DB::run("UPDATE forum_readposts SET lastpostread=$postid WHERE id=?", [$a["id"]]);
            }
        }
    }
}

// Returns the minimum read/write class levels of a forum
function get_forum_access_levels($forumid)
{
    $res = DB::run("SELECT minclassread, minclasswrite FROM forum_forums WHERE id=?", [$forumid]);
    if ($res->rowCount() != 1) {
        return false;
    }
    $arr = $res->fetch(PDO::FETCH_ASSOC);
    return array("read" => $arr["minclassread"], "write" => $arr["minclasswrite"]);
}

// Returns the forum ID of a topic, or false on error
function get_topic_forum($topicid)
{
    $res = DB::run("SELECT forumid FROM forum_topics WHERE id=?", [$topicid]);
    if ($res->rowCount() != 1) {
        return false;
    }
    $arr = $res->fetch(PDO::FETCH_LAZY);
    return $arr[0];
}

// Returns the ID of the last post of a forum
function update_topic_last_post($topicid)
{
    $res = DB::run("SELECT id FROM forum_posts WHERE topicid=? ORDER BY id DESC LIMIT 1", [$topicid]);
    $arr = $res->fetch(PDO::FETCH_LAZY) or Redirect::autolink(URLROOT . '/forums', 'No post found');
    $postid = $arr[0];
    DB::run("UPDATE forum_topics SET lastpost=? WHERE id=?", [$postid, $topicid]);
}

// Returns The ID Of A Last Post In A Forum Or Otherwise 0
function get_forum_last_post($forumid)
{
    $res = DB::run("SELECT lastpost FROM forum_topics WHERE forumid=? ORDER BY lastpost DESC LIMIT 1", [$forumid]);
    $arr = $res->fetch(PDO::FETCH_LAZY);
    $postid = $arr[0];
    if ($postid) {
        return $postid;
    } else {
        return 0;
    }
}

// Top forum posts
function forumpostertable($res)
{
    print("<br /><div>");
    ?>
    <font><?php echo Lang::T("FORUM_RANK"); ?></font>
    <font><?php echo Lang::T("FORUM_USER"); ?></font>
    <font><?php echo Lang::T("FORUM_POST"); ?></font>
    <br>
    <?php
    $num = 0;
    while ($a = $res->fetch(PDO::FETCH_ASSOC)) {
        ++$num;
        print("$num &nbsp; <a href='" . URLROOT . "/profile?id=$a[id]'><b>$a[username]</b></a> $a[num]");
    }
    if ($num == 0) {
        print("<b>No Forum Posters</b>");
    }
    print("</div>");
}

// Inserts a quick jump menu
function insert_quick_jump_menu($currentforum = 0)
{
    print("<div style='text-align:right'><form method='get' action='?' name='jump'>\n");
    print("<input type='hidden' name='action' value='" . URLROOT . "/forums/viewforum' />\n");
    $res = DB::run("SELECT * FROM forum_forums ORDER BY name");
    if ($res->rowCount() > 0) {
        print(Lang::T("FORUM_JUMP") . ": ");
        print("<select class='styled' name='forumid' onchange='if(this.options[this.selectedIndex].value != -1){ forms[jump].submit() }'>\n");
        while ($arr = $res->fetch(PDO::FETCH_ASSOC)) {
            if ($_SESSION["class"] >= $arr["minclassread"] || (!$_SESSION && $arr["guest_read"] == "yes")) {
                print("<option value='" . $arr["id"] . "'" . ($currentforum == $arr["id"] ? " selected='selected'>" : ">") . $arr["name"] . "</option>\n");
            }

        }
        print("</select>\n");
        print("<button type='submit' class='btn btn-sm ttbtn'>" . Lang::T("GO") . "</button>\n");
    }
    print("</form>\n</div>");
}

// Inserts a compose frame
function insert_compose_frame($id, $newtopic = true)
{
    global $maxsubjectlength;
    if ($newtopic) {
        $res = DB::run("SELECT name FROM forum_forums WHERE id=$id");
        $arr = $res->fetch(PDO::FETCH_ASSOC) or Redirect::autolink(URLROOT . '/forums', Lang::T("FORUM_BAD_FORUM_ID"));
        $forumname = stripslashes($arr["name"]);
        print("<p align='center'><b>" . Lang::T("FORUM_NEW_TOPIC") . " <a href='" . URLROOT . "/forums/viewforum&amp;forumid=$id'>$forumname</a></b></p>\n");
    } else {
        $res = DB::run("SELECT * FROM forum_topics WHERE id=$id");
        $arr = $res->fetch(PDO::FETCH_ASSOC) or Redirect::autolink(URLROOT . '/forums', Lang::T("FORUMS_NOT_FOUND_TOPIC"));
        $subject = stripslashes($arr["subject"]);
        print("<p align='center'>" . Lang::T("FORUM_REPLY_TOPIC") . ": <a href='" . URLROOT . "/forums/viewtopic&amp;topicid=$id'>$subject</a></p>");
    }
    # Language Marker #
    print("<p align='center'>" . Lang::T("FORUM_RULES") . "\n");
    print("<br />" . Lang::T("FORUM_RULES2") . "<br /></p>\n");
    print("<div class=table>");
    print("<center><b>Compose Message</b></center>");
    // attachments todo
    print("<form name='Form' method='post' action='" . URLROOT . "/forums/submittopic' enctype='multipart/form-data'>\n");
    if ($newtopic) {
        print("<div><center><strong>Subject:</strong>&nbsp;<input type='text' size='30%' maxlength='$maxsubjectlength' name='subject' /></center>");
        print("<input type='hidden' name='forumid' value='$id' />\n");
    } else {
        print("<div><input type='hidden' name='topicid' value='$id' />\n"); // added div here
    }
    textbbcode("Form", "body");

    echo '<br><center><input type="file" name="upfile[]" multiple></center><br>';
    print("<center><button type='submit' class='btn btn-sm ttbtn'>" . Lang::T("SUBMIT") . "</button></center></div>");
    print("</form>\n");
    print("</div>");
    insert_quick_jump_menu();
}

// LASTEST FORUM POSTS
function latestforumposts($sub_id = 0)
{
    ?>
    <div class="row">
    <div class="col-lg-12">
    <div class="wrapper wrapper-content animated fadeInRight">

    <div class="row frame-header">
    <div class="col-md-5">
    Latest Topic Title
    </div>
    <div class="col-md-1 d-none d-sm-block">
    Replies
    </div>
    <div class="col-md-1 d-none d-sm-block">
    Views
    </div>
    <div class="col-md-2 d-none d-sm-block">
    Author
    </div>
    <div class="col-md-3 d-none d-sm-block">
    Last Post
    </div>
    </div>
    <?php
    // HERE GOES THE QUERY TO RETRIEVE DATA FROM THE DATABASE AND WE START LOOPING ///
    if ($sub_id != 0) {
        $for = DB::run("SELECT * FROM forum_topics WHERE forumid IN (SELECT id FROM forum_forums WHERE sub=$sub_id) ORDER BY lastpost DESC LIMIT 10");
    } else {
        $for = DB::run("SELECT * FROM forum_topics ORDER BY lastpost DESC LIMIT 5");
    }
    if ($for->rowCount() == 0) {
        print("<b>No Latest Topics</b>");
    }
    while ($topicarr = $for->fetch(PDO::FETCH_ASSOC)) {
        // Set minclass
        $res = DB::run("SELECT name,minclassread,guest_read FROM forum_forums WHERE id=$topicarr[forumid]");
        $forum = $res->fetch(PDO::FETCH_ASSOC);
        if ($forum && $_SESSION["class"] >= $forum["minclassread"] || $forum["guest_read"] == "yes") {
            $forumname = "<a href='" . URLROOT . "/forums/viewforum&amp;forumid=$topicarr[forumid]'><b>" . htmlspecialchars($forum["name"]) . "</b></a>";
            $topicid = $topicarr["id"];
            $topic_title = stripslashes($topicarr["subject"]);
            $topic_userid = $topicarr["userid"];
            // Topic Views
            $views = $topicarr["views"];
            // GETTING TOTAL NUMBER OF POSTS ///
            $res = DB::run("SELECT COUNT(*) FROM forum_posts WHERE topicid=?", [$topicid]);
            $arr = $res->fetch(PDO::FETCH_LAZY);
            $posts = $arr[0];
            $replies = max(0, $posts - 1);
            // GETTING USERID AND DATE OF LAST POST ///
            $res = DB::run("SELECT * FROM forum_posts WHERE topicid=? ORDER BY id DESC LIMIT 1", [$topicid]);
            $arr = $res->fetch(PDO::FETCH_ASSOC);
            $postid = 0 + $arr["id"];
            $userid = 0 + $arr["userid"];
            $added = TimeDate::utc_to_tz($arr["added"]);
            // GET NAME OF LAST POSTER ///
            $res = DB::run("SELECT id, username FROM users WHERE id=$userid");
            if ($res->rowCount() == 1) {
                $arr = $res->fetch(PDO::FETCH_ASSOC);
                $username = "<a href='" . URLROOT . "/profile?id=$userid'>" . Users::coloredname($arr['username']) . "</a>";
            } else {
                $username = "Unknown[$topic_userid]";
            }
            // GET NAME OF THE AUTHOR ///
            $res = DB::run("SELECT username FROM users WHERE id=?", [$topic_userid]);
            if ($res->rowCount() == 1) {
                $arr = $res->fetch(PDO::FETCH_ASSOC);
                $author = "<a href='" . URLROOT . "/profile?id=$topic_userid'>" . Users::coloredname($arr['username']) . "</a>";
            } else {
                $author = "Unknown[$topic_userid]";
            }
            // GETTING THE LAST INFO AND MAKE THE TABLE ROWS ///
            $r = DB::run("SELECT lastpostread FROM forum_readposts WHERE userid=$userid AND topicid=$topicid");
            $a = $r->fetch(PDO::FETCH_LAZY);
            $new = !$a || $postid > $a[0];
            $subject = "<a href='" . URLROOT . "/forums/viewtopic&amp;topicid=$topicid&amp;page=last#last'>" . stripslashes(encodehtml($topicarr["subject"])) . "</a>";
            ?>
            <div class="row border  ttborder">
            <div class="col-md-5 d-none d-sm-block">
            <b><?php echo $subject; ?></b>
            </div>
            <div class="col-md-1 d-none d-sm-block">
            <?php echo $replies; ?>
            </div>
            <div class="col-md-1 d-none d-sm-block">
            <?php echo $views; ?>
            </div>
            <div class="col-md-2 d-none d-sm-block">
            <b><center><?php echo $author; ?></center></b>
            </div>
            <div class="col-md-3">
            <small><b><?php echo $subject; ?></b>&nbsp;
            by&nbsp;<b><?php echo $username; ?></b></small><br><small style='white-space: nowrap'><b>
            <?php echo $added; ?></b></small>
            </div>
            </div>
            <?php
        }
    }
    print("</div></div></div><br>");
}

// Get last post info in a array return img & lastpost
function lastpostdetails($lastpostid)
{
    $post_res = DB::run("SELECT added,topicid,userid FROM forum_posts WHERE id=$lastpostid");
    if ($post_res->rowCount() == 1) {
        $post_arr = $post_res->fetch(PDO::FETCH_ASSOC) or Redirect::autolink(URLROOT . '/forums', "Bad forum last_post");
        $lastposterid = $post_arr["userid"];
        $lastpostdate = TimeDate::utc_to_tz($post_arr["added"]);
        $lasttopicid = $post_arr["topicid"];
        $user_res = DB::run("SELECT username FROM users WHERE id=$lastposterid");
        $user_arr = $user_res->fetch(PDO::FETCH_ASSOC);
        $lastposter = Users::coloredname($user_arr["username"]);
        $topic_res = DB::run("SELECT subject FROM forum_topics WHERE id=$lasttopicid");
        $topic_arr = $topic_res->fetch(PDO::FETCH_ASSOC);
        $lasttopic = stripslashes(htmlspecialchars($topic_arr['subject']));
        //cut last topic
        $latestleng = 10;
        $lastpost = "<small><a href='" . URLROOT . "/forums/viewtopic&amp;topicid=$lasttopicid&amp;page=last#last'><b>" . CutName($lasttopic, $latestleng) . "</b></a> by <a href='" . URLROOT . "/profile?id=$lastposterid'><b>$lastposter</b></a><br />$lastpostdate</small>";
        if ($_SESSION['loggedin'] == true) {
            $a = DB::run("SELECT lastpostread FROM forum_readposts WHERE userid=$_SESSION[id] AND topicid=$lasttopicid")->fetch();
        }
        //define the images for new posts or not on index
        if ($a && $a['lastpostread'] == $lastpostid) {
            $img = "<i class='fa fa-file-text tticon' title='Read'></i>";
        } else {
            $img = "<i class='fa fa-file-text tticon-red' title='UnRead'></i>";
        }
    } else {
        $lastpost = "<span class='small'>No Posts</span>";
        $img = "folder";
    }

    $detail = [
        'img' => $img,
        'lastpost' => $lastpost,
    ];
    return $detail;
}