<?php
class Forums
{

    public function __construct()
    {
        $this->session = Auth::user(0, 1);
    }

    /**
     * Lets show the validation in one place so we dont have to repeat :)
     */
    private function validForumUser($extra = false)
    {
        if (!Config::TT()['FORUMS']) {
            Redirect::autolink(URLROOT, Lang::T("FORUM_AVAILABLE"));
        }
        if (!Config::TT()['FORUMS_GUESTREAD'] && !$_SESSION['loggedin']) {
            Redirect::autolink(URLROOT, Lang::T("NO_PERMISSION"));
        }
        if ($_SESSION["forumbanned"] == "yes" || $_SESSION["view_forum"] == "no") {
            Redirect::autolink(URLROOT, Lang::T("FORUM_BANNED"));
        }
    }

    /**
     * View Forum Index.
     */
    public function index()
    {
        $this->validForumUser();
        if ($_GET["do"] == 'catchup') {
            catch_up();
        }

        // Action: SHOW MAIN FORUM INDEX
        $forums_res = Forum::getIndex();
        if ($forums_res->rowCount() == 0) {
            Redirect::autolink(URLROOT, Lang::T("FORUM_AVAILABLE"));
        }

        $subforums_res = Forum::getsub();

        //topic count and post counts
        $postcount = number_format(get_row_count("forum_posts"));
        $topiccount = number_format(get_row_count("forum_topics"));

        $data = [
            'title' => 'Forums',
            'mainquery' => $forums_res,
            'mainsub' => $subforums_res,
            'postcount' => $postcount,
            'topiccount' => $topiccount,
        ];
        View::render('forum/index', $data, 'user');
    }

    /**
     * Post New Topic
     */
    public function newtopic()
    {
        $this->validForumUser();
        $forumid = $_GET["forumid"];
        if (!Validate::Id($forumid)) {
            Redirect::autolink(URLROOT . "/forums", "No Forum ID $forumid");
        }
        $data = [
            'id' => $forumid,
            'title' => 'New Post',
        ];
        View::render('forum/newtopic', $data, 'user');
        die;
    }

    /**
     * Search Forum.
     */
    public function search()
    {
        $this->validForumUser();
        $data = [
            'title' => Lang::T("Search Forums"),
        ];
        View::render('forum/search', $data, 'user');
    }

    /**
     * Search Results.
     */
    public function result()
    {
        $this->validForumUser();
        $keywords = Input::get("keywords");
        $type = $_GET['type'];
        if (!$keywords == '') {
            $keys = explode(" ", $keywords);
            foreach ($keys as $k) {
                $searcha[] = " forum_topics.subject LIKE '%$k%' ";
                $searchb[] = " forum_posts.body LIKE '%$k%' ";
                $searchc[] = " forum_topics.subject LIKE '%$k%' ";
                $searchd[] = " forum_posts.body LIKE '%$k%' ";
            }
            $whereasearch = '(' . implode(' OR ', $searcha) . ')';
            $wherebsearch = '(' . implode(' OR ', $searchb) . ')';
            $wherecsearch = implode(' OR ', $searchc);
            $wheredsearch = implode(' OR ', $searchd);
            $wherea = $whereasearch;
            $whereb = $wherebsearch;
            $wherec = $wherecsearch;
            $whered = $wheredsearch;
            if ($type == 'deep') {
                $res2 = DB::run("SELECT COUNT(*), ( $whereb ) as count_words
                                 FROM forum_posts
                                 JOIN forum_topics
                                 ON forum_posts.topicid=forum_topics.id
                                 WHERE ($whered) ")->fetchColumn();
                $count = $res2;
                list($pagertop, $pagerbottom, $limit) = pager(30, $count, URLROOT . "/forums/result?keywords=$keywords&type=deep");
                $res = DB::run("SELECT *, ( $wherea + $whereb ) as count_words
                                FROM forum_posts
                                JOIN forum_topics
                                ON forum_posts.topicid=forum_topics.id
                                WHERE ($whered)
                                ORDER BY count_words DESC")->fetchAll();
            } else {
                $res2 = DB::run("SELECT COUNT(DISTINCT subject), ( $wherea ) as count_words
                                 FROM forum_topics
                                 JOIN forum_posts
                                 ON forum_posts.topicid=forum_topics.id
                                 WHERE ($wherec) ")->fetchColumn();
                $count = $res2;
                list($pagertop, $pagerbottom, $limit) = pager(30, $count, URLROOT . "/forums/result?keywords=$keywords&");

                $res = DB::run("SELECT DISTINCT subject, forumid, topicid, forum_posts.userid, ( $wherea ) as count_words
                                FROM forum_topics
                                JOIN forum_posts
                                ON forum_posts.topicid=forum_topics.id
                                WHERE ($wherec)
                                ORDER BY count_words DESC")->fetchAll();
            }

            if ($count > 0) {
                $data = [
                    'res' => $res,
                    'keywords' => $keywords,
                    'title' => 'Forums',
                    'count' => $count,
                    'pagerbottom' => $pagerbottom,
                ];
                View::render('forum/result', $data, 'user');
            } else {
                Redirect::autolink(URLROOT . '/forums/search', Lang::T("NOTHING_FOUND"));
            }

        } else {
            Redirect::autolink(URLROOT . '/forums/search', Lang::T("YOU_DID_NOT_ENTER_ANYTHING"));
        }
    }

    /**
     * View Unread Topics.
     */
    public function viewunread()
    {
        $this->validForumUser();
        $res = Forum::viewunread();
        $data = [
            'res' => $res,
            'n' => 0,
            'title' => 'Forums',
        ];
        View::render('forum/viewunread', $data, 'user');
        die;
    }

    /**
     * View Forum.
     */
    public function viewforum()
    {
        $this->validForumUser();
        $forumid = Input::get("forumid");
        if (!Validate::Id($forumid)) {
            Redirect::autolink(URLROOT . "/forums", Lang::T("FORUMS_DENIED"));
        }
        // Get forum name
        $res = Forum::getMinRead($forumid);
        $arr = $res->fetch(PDO::FETCH_ASSOC);
        $forumname = $arr["name"];
        if (!$forumname || $_SESSION['class'] < $arr["minclassread"] && $arr["guest_read"] == "no") {
            Redirect::autolink(URLROOT . "/forums", Lang::T("FORUMS_NOT_PERMIT"));
        }
        // Pagination
        $count = get_row_count("forum_topics", "WHERE forumid=$forumid");
        list($pagertop, $pagerbottom, $limit) = pager(25, $count, URLROOT . "/forums/viewforum&forumid=$forumid&");
        // Get topics data and display category
        $topicsres = Forum::getForumTopic($forumid, $limit);
        $data = [
            'title' => 'Forums',
            'topicsres' => $topicsres,
            'forumname' => $forumname,
            'forumid' => $forumid,
            'pagerbottom' => $pagerbottom,
        ];
        View::render('forum/viewforum', $data, 'user');
        die;
    }

    /**
     *Reply To Post. not in use yet
     */
    public function reply()
    {
        $this->validForumUser();
        $topicid = Input::get("topicid");
        if (!Validate::Id($topicid)) {
            Redirect::autolink(URLROOT . "/forums", sprintf(Lang::T("FORUMS_NO_ID_FORUM")));
        }
        $data = [
            'title' => 'Reply',
            'topicid' => $topicid,
        ];
        View::render('forum/reply', $data, 'user');
        die;
    }

    /**
     * Edit a Post.
     */
    public function editpost()
    {
        $this->validForumUser();
        $postid = Input::get("postid");

        if (!Validate::Id($postid)) {
            Redirect::autolink(URLROOT . "/forums", Lang::T("FORUMS_DENIED"));
        }
        $res = Forum::getForumPost($postid);
        if ($res->rowCount() != 1) {
            Redirect::autolink(URLROOT . "/forums", "Where is id $postid");
        }
        $arr = $res->fetch(PDO::FETCH_ASSOC);
        if ($_SESSION["id"] != $arr["userid"] && $_SESSION["delete_forum"] != "yes" && $_SESSION["edit_forum"] != "yes") {
            Redirect::autolink(URLROOT . "/forums", Lang::T("FORUMS_DENIED"));
        }
        $data = [
            'title' => 'Edit Post',
            'postid' => $postid,
            'body' => $arr['body'],
        ];
        View::render('forum/edit', $data, 'user');
        die;
    }

    /**
     * Edit Submit.
     */
    public function editsubmit()
    {
        $this->validForumUser();
        $postid = Input::get("postid");
        if (Input::exist()) {
            $body = $_POST['body'];
            if ($body == "") {
                Redirect::autolink(URLROOT . "/forums", "Body cannot be empty!");
            }
            $res = Forum::getForumPost($postid);
            if ($res->rowCount() != 1) {
                Redirect::autolink(URLROOT . "/forums", "Where is this id $postid");
            }
            $arr = $res->fetch(PDO::FETCH_ASSOC);
            if ($_SESSION["id"] != $arr["userid"] && $_SESSION["delete_forum"] != "yes" && $_SESSION["edit_forum"] != "yes") {
                Redirect::autolink(URLROOT . "/forums", Lang::T("FORUMS_DENIED"));
            }
            $body = htmlspecialchars_decode($body);
            $editedat = TimeDate::get_date_time();
            Forum::updateForumPost($body, $editedat, $_SESSION['id'], $postid);
            $returnto = Input::get("returnto");
            if ($returnto != "") {
                Redirect::to($returnto);
            } else {
                Redirect::autolink(URLROOT . "/forums", "Post was edited successfully.");
            }
        } else {
            Redirect::autolink(URLROOT, Lang::T("YOU_DID_NOT_ENTER_ANYTHING"));
        }
    }

    /**
     * Confirm Post/Reply. (function insert_compose_frame)
     */
    public function submittopic()
    {
        $this->validForumUser();
        $forumid = Input::get("forumid");
        $topicid = Input::get("topicid");
        if (!Validate::Id($forumid) && !Validate::Id($topicid)) {
            Redirect::autolink(URLROOT . '/forums', Lang::T("FORUM_ERROR"));
        }
        $newtopic = $forumid > 0;
        $subject = $_POST["subject"];
        if ($newtopic) {
            if (!$subject) {
                Redirect::autolink(URLROOT . '/forums', "You must enter a subject.");
            }
            $subject = trim($subject);
        } else {
            $forumid = get_topic_forum($topicid) or Redirect::autolink(URLROOT . '/forums', "Bad topic ID");
        }
        // Make sure sure user has write access in forum
        $arr = get_forum_access_levels($forumid) or Redirect::autolink(URLROOT . '/forums', "Bad forum ID");
        if ($_SESSION['class'] < $arr["write"]) {
            Redirect::autolink(URLROOT . '/forums', Lang::T("FORUMS_NOT_PERMIT"));
        }
        $body = htmlspecialchars_decode($_POST["body"]);
        if (!$body) {
            Redirect::autolink(URLROOT . '/forums', "No body text.");
        }
        if ($newtopic) { //Create topic
            $subject = $subject;
            DB::run("INSERT INTO forum_topics (userid, forumid, subject) VALUES(?,?,?)", [$_SESSION["id"], $forumid, $subject]);
            $topicid = DB::lastInsertId() or Redirect::autolink(URLROOT . '/forums', "Topics id n/a");
        } else {
            //Make sure topic exists and is unlocked
            $res = DB::run("SELECT * FROM forum_topics WHERE id=?", [$topicid]);
            $arr = $res->fetch(PDO::FETCH_ASSOC) or Redirect::autolink(URLROOT . '/forums', "Topic id n/a");
            if ($arr["locked"] == 'yes') {
                Redirect::autolink(URLROOT . '/forums', "Topic locked");
            }
            //Get forum ID
            $forumid = $arr["forumid"];
        }
        //Insert the new post
        $body = htmlspecialchars_decode($body);
        DB::run("INSERT INTO forum_posts (topicid, userid, added, body) VALUES(?, ?, ?, ?)", [$topicid, $_SESSION["id"], TimeDate::get_date_time(), $body]);
        $postid = DB::lastInsertId();
        // attachments todo
        if (isset($_FILES['upfile'])):
            $array = array($_FILES['upfile']['name']);
            $result = array_filter($array);
            foreach ($result as $k => $ar):
                // check if file has one of the following extensions
                $allowedfileExtensions = array('jpg', 'gif', 'png', 'zip');
                if (in_array($_FILES['upfile']['type'][$k], $allowedfileExtensions)) {
                    Redirect::autolink(URLROOT . '/forums', "Sorry, only zip, JPG, JPEG, PNG, GIF files are allowed.");
                }
                $sourcePath = $_FILES['upfile']['tmp_name'][$k]; // Storing source path of the file in a variable
                $fileSize = $_FILES['upfile']['size'][$k];
                $fileName = $_FILES['upfile']['name'][$k];
                $extension = substr($fileName, -3);
                $hash = sha1($sourcePath);

                $newfile = $hash . "." . $extension;
                $targetPath = TORRENTDIR . "/attachment/" . $hash . ".data"; // Target path where file is to be stored
                $thumbPath = "uploads/thumbnail/" . $hash . ".jpg"; // Target path where attachment as jpg is to be stored

                if ($extension == 'zip') {
                    move_uploaded_file($sourcePath, $targetPath); // Moving Uploaded file
                    DB::run("
										        INSERT INTO attachments (content_id, user_id, upload_date, filename, file_size, file_hash, topicid)
										        VALUES (?,?,?,?,?,?,?)",
                        [$postid, $_SESSION['id'], TimeDate::gmtime(), $fileName,
                            $fileSize, $hash, $topicid]);
                } else {
                    if (move_uploaded_file($sourcePath, $targetPath)) { // Moving Uploaded file
                        SimpleThumbnail::create()->image($targetPath)->thumbnail(100)->to($thumbPath);
                        DB::run("
										            INSERT INTO attachments (content_id, user_id, upload_date, filename, file_size, file_hash, topicid)
										            VALUES (?,?,?,?,?,?,?)",
                            [$postid, $_SESSION['id'], TimeDate::gmtime(), $fileName,
                                $fileSize, $hash, $topicid]);
                    }
                }
            endforeach;
        endif;
        //Update topic last post
        update_topic_last_post($topicid);
        if ($newtopic) {
            $msg_shout = "New Forum Topic: [url=" . URLROOT . "/forums/viewtopic&topicid=" . $topicid . "]" . $subject . "[/url] posted by [url=" . URLROOT . "/profile?id=" . $_SESSION['id'] . "]" . $_SESSION['username'] . "[/url]";
            DB::run("INSERT INTO shoutbox (userid, date, user, message) VALUES(?,?,?,?)", [0, TimeDate::get_date_time(), 'System', $msg_shout]);
            Redirect::to(URLROOT . "/forums/viewtopic&topicid=$topicid&page=last");
        } else {
            Redirect::to(URLROOT . "/forums/viewtopic&topicid=$topicid&page=last#post$postid");
        }
        die;
    }

    /**
     * View Forum Topic.  still todo
     */
    public function viewtopic()
    {
        $this->validForumUser();
        $topicid = $_GET["topicid"];
        $page = $_GET["page"];

        if (!Validate::Id($topicid)) {
            Redirect::autolink(URLROOT . '/forums', "Topic Not Valid");
        }

        // Get topic info
        $res = DB::run("SELECT * FROM forum_topics WHERE id=?", [$topicid]);
        $arr = $res->fetch(PDO::FETCH_ASSOC) or Redirect::autolink(URLROOT . '/forums', "Topic not found");
        $locked = ($arr["locked"] == 'yes');
        $subject = stripslashes($arr["subject"]);
        $sticky = $arr["sticky"] == "yes";
        $forumid = $arr["forumid"];
        // Update Topic Views
        $views = $arr['views'];
        $new_views = $views + 1;
        DB::run("UPDATE forum_topics SET views = $new_views WHERE id=$topicid");

        // Check if user has access to this forum
        $arr2 = Forum::canRead($forumid);
        if (!$arr2 || $_SESSION["class"] < $arr2["minclassread"] && $arr2["guest_read"] == "no") {
            Redirect::autolink(URLROOT . '/forums', "You do not have access to the forum this topic is in.");
        }
        $forum = stripslashes($arr2["name"]);
        $levels = get_forum_access_levels($forumid) or die;
        if ($_SESSION["class"] >= $levels["write"]) {
            $maypost = true;
        } else {
            $maypost = false;
        }
        // Update Last Read
        if ($_SESSION['loggedin'] == true) {
            $r = DB::run("SELECT lastpostread FROM forum_readposts WHERE userid=? AND topicid=?", [$_SESSION['id'], $topicid]);
            $a = $r->fetch(PDO::FETCH_LAZY);
            $lpr = $a[0];
            if (!$lpr) {
                DB::run("INSERT INTO forum_readposts (userid, topicid) VALUES($_SESSION[id], $topicid)");
            }
        }
        // Paginatation

        // Get post count
        $res = DB::run("SELECT COUNT(*) FROM forum_posts WHERE topicid=?", [$topicid]);
        $arr = $res->fetch(PDO::FETCH_LAZY);
        $postcount = $arr[0];

        // Make page menu
        $pagemenu = "<br /><small>\n";
        $perpage = 30;
        $pages = floor($postcount / $perpage);
        if ($pages * $perpage < $postcount) {
            ++$pages;
        }

        if ($page == "last") {
            $page = $pages;
        } else {
            if ($page < 1) {
                $page = 1;
            } elseif ($page > $pages) {
                $page = $pages;
            }

        }
        $offset = max(0, ($page * $perpage) - $perpage);

        if ($page == 1) {
            $pagemenu .= "<b>&lt;&lt; Prev</b>";
        } else {
            $pagemenu .= "<a href='" . URLROOT . "/forums/viewtopic&amp;topicid=$topicid&amp;page=" . ($page - 1) . "'><b>&lt;&lt; Prev</b></a>";
        }

        $pagemenu .= "&nbsp;&nbsp;";
        for ($i = 1; $i <= $pages; ++$i) {
            if ($i == $page) {
                $pagemenu .= "<b>$i</b>\n";
            } else {
                $pagemenu .= "<a href='" . URLROOT . "/forums/viewtopic&amp;topicid=$topicid&amp;page=$i'><b>$i</b></a>\n";
            }

        }
        $pagemenu .= "&nbsp;&nbsp;";
        if ($page == $pages) {
            $pagemenu .= "<b>Next &gt;&gt;</b><br /><br />\n";
        } else {
            $pagemenu .= "<a href='" . URLROOT . "/forums/viewtopic&amp;topicid=$topicid&amp;page=" . ($page + 1) . "'><b>Next &gt;&gt;</b></a><br /><br />\n";
        }

        $pagemenu .= "</small>";

        //Get topic posts
        $res = DB::run("SELECT * FROM forum_posts WHERE topicid=$topicid ORDER BY id LIMIT $offset,$perpage");

        Style::header("View Topic: $subject");
        Style::begin("$forum &gt; $subject");
        forumheader("<a href='" . URLROOT . "/forums/viewforum&amp;forumid=$forumid'>$forum</a> <b style='font-size:16px; vertical-align:middle'>/</b> $subject");
        print("<div style='padding: 6px'>");
        if (!$locked && $maypost) {
            print("<div align='right'>
            <a href='#bottom'><button type='button' class='btn btn-sm ttbtn'><b>Reply</b></button></a>
            </div>");
        } else {
            print("<div align='right'>" . Lang::T("FORUMS_LOCKED") . "</div>");
        }
        print("</div>");

        // Print table of posts
        $pc = $res->rowCount();
        $pn = 0;
        while ($arr = $res->fetch(PDO::FETCH_ASSOC)) {
            ++$pn;
            $postid = $arr["id"];
            $posterid = $arr["userid"];
            $added = TimeDate::utc_to_tz($arr["added"]) . "(" . (TimeDate::get_elapsed_time(TimeDate::sql_timestamp_to_unix_timestamp($arr["added"]))) . " ago)";
            //---- Get poster details
            $res4 = DB::run("SELECT COUNT(*) FROM forum_posts WHERE userid=?", [$posterid]);
            $arr33 = $res4->fetch(PDO::FETCH_LAZY);
            $forumposts = $arr33[0];
            $res2 = DB::run("SELECT * FROM users WHERE id=?", [$posterid]);
            $arr2 = $res2->fetch(PDO::FETCH_ASSOC);
            $postername = Users::coloredname($arr2["username"]);
            $quotename = $arr2["username"];
            if ($postername == "") {
                $by = "Deluser";
                $title = "Deleted Account";
                $privacylevel = "strong";
                $usersignature = "";
                $userdownloaded = "0";
                $useruploaded = "0";
                $avatar = "";
                $nposts = "-";
                $tposts = "-";
            } else {
                $avatar = htmlspecialchars($arr2["avatar"]);
                $userdownloaded = mksize($arr2["downloaded"]);
                $useruploaded = mksize($arr2["uploaded"]);
                $privacylevel = $arr2["privacy"];
                $usersignature = stripslashes(format_comment($arr2["signature"]));
                if ($arr2["downloaded"] > 0) {
                    $userratio = number_format($arr2["uploaded"] / $arr2["downloaded"], 2);
                } else
                if ($arr2["uploaded"] > 0) {
                    $userratio = "Inf.";
                } else {
                    $userratio = "---";
                }
                if (!$arr2["country"]) {
                    $usercountry = "unknown";
                } else {
                    $res4 = DB::run("SELECT name,flagpic FROM countries WHERE id=? LIMIT 1", [$arr2['country']]);
                    $arr4 = $res4->fetch(PDO::FETCH_ASSOC);
                    $usercountry = $arr4["name"];
                }
                $title = format_comment($arr2["title"]);
                $donated = $arr2['donated'];
                $by = "<a href='" . URLROOT . "/profile?id=$posterid'>$postername</a>" . ($donated > 0 ? "<i class='fa fa-star' aria-hidden='true' style='color:orange' title='Donated'></i>" : "") . "";
            }
            if (!$avatar) {
                $avatar = URLROOT . "/assets/images/misc/default_avatar.png";
            }

            # print("<a name=$postid>\n");
            print("<a id='post$postid'></a>");
            if ($pn == $pc) {
                print("<a name='last'></a>\n");
                if ($postid > $lpr && $_SESSION['loggedin'] == true) {
                    DB::run("UPDATE forum_readposts SET lastpostread=$postid WHERE userid=? AND topicid=?", [$_SESSION['id'], $topicid]);
                }
            }

            //Post Top
            ?>
            <div class="row frame-header">
            <div class="col-md-2">
                <?php echo $by; ?>
            </div>
            <div class="col-md-10">
                Posted at <?php echo $added; ?>
            </div>
            </div>
            <?php
//Post Middle

            $body = format_comment($arr["body"]);
            if (Validate::Id($arr['editedby'])) {
                $res2 = DB::run("SELECT username FROM users WHERE id=?", [$arr['editedby']]);
                if ($res2->rowCount() == 1) {
                    $arr2 = $res2->fetch(PDO::FETCH_ASSOC);
                    //edited by comment out if needed
                    $body .= "<br /><br /><small><i>Last edited by <a href='" . URLROOT . "/profile?id=$arr[editedby]'>$arr2[username]</b></a> on " . TimeDate::utc_to_tz($arr["editedat"]) . "</i></small><br />\n";
                    $body .= "\n";
                }
            }

            $quote = htmlspecialchars($arr["body"]);
            $postcount1 = DB::run("SELECT COUNT(forum_posts.userid) FROM forum_posts WHERE id=$posterid");
            while ($row = $postcount1->fetch(PDO::FETCH_LAZY)) {
                if ($privacylevel == "strong" && $_SESSION["control_panel"] != "yes") { //hide stats, but not from staff
                    $useruploaded = "---";
                    $userdownloaded = "---";
                    $userratio = "---";
                    $nposts = "-";
                    $tposts = "-";
                }
                ?>
                <div class="row">
                <div class="col-md-2 d-none d-sm-block border ttborder">
                <center><i><?php echo $title; ?></i></center><br>
                <center><img width='80' height='80' src='<?php echo $avatar ?>' alt='' /></center><br>
                Uploaded: <?php echo $useruploaded; ?><br>
                Downloaded: <?php echo $userdownloaded; ?><br>
                Posts: <?php echo $forumposts; ?><br>
                Ratio: <?php echo $userratio; ?><br>
                Location: <?php echo $usercountry; ?><br>
                </div>
                <div class="col-md-10 border ttborder"><br>
                <?php echo $body; ?>

                <?php
// attachments todo
                $sql = DB::run("SELECT * FROM attachments WHERE content_id =?", [$postid]);
                if ($sql->rowCount() != 0) {
                    foreach ($sql as $row7) {
                        print("<br>&nbsp;<b>$row7[filename]</b><br>");
                        $extension = substr($row7['filename'], -3);
                        if ($extension == 'zip') {
                            $daimage = TORRENTDIR . "/attachment/$row7[file_hash].data";
                            if (file_exists($daimage)) {
                                print(" <a class='btn btn-sm ttbtn' href=\"" . URLROOT . "/download/attachment?id=$row7[id]&amp;hash=" . rawurlencode($row7["file_hash"]) . "\"><i class='fa fa-file-archive-o tticon' ></i>Download</a><br>");
                            } else {
                                print("no zip<br>");
                            }
                        } else {
                            $daimage = "uploads/thumbnail/$row7[file_hash].jpg";
                            if (file_exists($daimage)) {

                                ?>
                <!-- Trigger/Open The Model -->
                <img id="myBtn" src='<?php echo data_uri($daimage, $row7['filename']); ?>' height='110px' width='110px' border='0' alt=''  data-toggle="modal" data-target="#myModal-<?=$daimage;?>">
                <!-- The Modal -->
                <div id="myModal-<?=$daimage;?>" class="modal">
                    <!-- Modal content -->
                    <div class="modal-content">
                        <!-- The Close Button -->
                        <?php
$switchimage = TORRENTDIR . "/attachment/$row7[file_hash].data";
                                ?><button type="button" class="btn btn-sm ttbtn" data-dismiss="modal"><b>CLOSE</b></button><br><?php
echo $row7['filename']; ?><br>
                        <img src='<?php echo data_uri($switchimage, $row7['filename']); ?>' style="width:100%" alt=''>
                    </div>
                </div>
                                <?php
} else {
                                print("<a href=\"" . URLROOT . "/download/attachment?id=$row7[id]&amp;hash=" . rawurlencode($row7["file_hash"]) . "\"><img src='" . URLROOT . "/thumb/$row7[file_hash].jpg' height='110px' width='110px' border='0' alt='' /></a><br>");
                            }
                        }
                    }
                }

                if (!$usersignature) {
                    print("<br />\n");
                } else {
                    print("<br /><hr /><br /><div class='f-sig' align='center'>$usersignature</div>\n");
                }
            }
            ?>
        </div>
        </div>
        <?php
if ($_SESSION['loggedin']) {?>
        <div class="row ttblend">
        <div class="col-md-3 d-none d-sm-block">
        <a href='<?php echo URLROOT; ?>/profile?id=<?php echo $posterid; ?>'><i class='fa fa-user tticon' title='Profile'></i></a>
        <a href='<?php echo URLROOT; ?>/messages/create?id=<?php echo $posterid; ?>'><i class='fa fa-comment tticon' title='Send PM'></i></a>
        <a href='<?php echo URLROOT; ?>/report/forum?forumid=<?php echo $topicid ?>&amp;forumpost=<?php echo $postid ?>'><i class="fa fa-flag tticon" title='<?php echo Lang::T("FORUMS_REPORT_POST") ?>'></i></a>&nbsp;
        <a href='javascript:scroll(0,0);'><i class='fa fa-arrow-up tticon' title='<?php echo Lang::T("FORUMS_GOTO_TOP_PAGE"); ?>'></i></a>
        <!--- add link to exact post -->
        <a href='<?php echo URLROOT; ?>/forums/viewtopic&amp;topicid=<?php echo $topicid; ?>&amp;page=p#post<?php echo $postid; ?>'><i class='fa fa-anchor tticon' title='Direct Post Link'></i></a>
        </div>
        <div class="col-md-9 d-none d-sm-block">
        <?php
// Hide Reply Mod
                if ($_SESSION["id"] !== $posterid) {
                    // say thanks
                    //print("<a href='" . URLROOT . "/likes/thanks?id=$topicid&type=thanksforum'><button class='btn btn-sm btn-success'>Say Thanks</button></a>&nbsp;");
                }

                //define buttons and who can use them
                if ($_SESSION["id"] == $posterid || $_SESSION["edit_forum"] == "yes" || $_SESSION["delete_forum"] == "yes") {
                    print("<a href='" . URLROOT . "/forums/editpost&amp;postid=$postid'><i class='fa fa-pencil tticon' title=" . Lang::T("EDIT") . "></i></a>&nbsp;");
                }
                if ($_SESSION["delete_forum"] == "yes") {
                    print("<a href='" . URLROOT . "/forums/deletepost&amp;postid=$postid&amp;sure=0'><i class='fa fa-trash-o tticon-red' title='Delete'></i></a>&nbsp;");
                }
                if (!$locked && $maypost) {
                    print("<a href=\"javascript:SmileIT('[quote=$quotename] $quote [/quote]', 'Form', 'body');\"><i class='fa fa-quote-right tticon' title='". Lang::T("QUOTE") ."'></i> </a>&nbsp;");
                    print("<a href='#bottom'><i class='fa fa-reply tticon' title='reply'></i></a>");
                }
                ?>
        </div>
        </div>
<?php }?>
        <br>
        <?php
//Post Bottom
        }
        //-------- end posts table ---------//
        print($pagemenu);

        //quick reply
        if (!$locked && $_SESSION['loggedin'] == true) {
            //Style::begin("Reply", $newtopic = false);
            print("<fieldset class='download'><legend><center><b>" . Lang::T("FORUMS_POST_REPLY") . "</b></center></legend>");
            $newtopic = false;
            print("<a name='bottom'></a>");
            print("<form name='Form' method='post' action='" . URLROOT . "/forums/submittopic' enctype='multipart/form-data'>\n");
            if ($newtopic) {
                print("<input type='hidden' name='forumid' value='$id' />\n");
            } else {
                print("<input type='hidden' name='topicid' value='$topicid' />\n");
            }

            print("<table cellspacing='0' cellpadding='0' align='center'>");
            if ($newtopic) {
                print("<tr><td class='alt2'>" . Lang::T("FORUMS_SUBJECT") . "</td><td class='alt1' align='left' style='padding: 0px'><input type='text' size='100' maxlength='100' name='subject' style='border: 0px; height: 19px' /></td></tr>\n");
            }

            print("</table>");

            textbbcode("Form", "body"); // todo

            //echo '<center><input type="file" name="upfile[]" multiple></center><br>';
            print("<center><br /><button class='btn btn-sm ttbtn'>Reply</button></center><br>");

            ?>
    <div class="row justify-content-md-center">
        <div class="col-md-4 border ttborder">
<?php
echo '<center>Add attachment<center><br>';
            echo '<center><input type="file" name="upfile[]" multiple></center><br></div></div><br>';

            print("</form>\n");
            //Style::end();
            print(" </fieldset>");
        } else {
            print("" . Lang::T("FORUMS_LOCKED") . "<br />");
        }
        //end quick reply

        if ($locked) {
            print(Lang::T("FORUMS_TOPIC_LOCKED") . "\n");
        } elseif (!$maypost) {
            print("<i>" . Lang::T("FORUMS_YOU_NOT_PERM_POST_FORUM") . "</i>\n");
        }

        //insert page numbers and quick jump

        // insert_quick_jump_menu($forumid);

        // MODERATOR OPTIONS
        if ($_SESSION["delete_forum"] == "yes" || $_SESSION["edit_forum"] == "yes") {
            print("<div class='f-border f-mod_options' align='center'><table width='100%' cellspacing='0'><tr class='f-title'><th><center>" . Lang::T("FORUMS_MOD_OPTIONS") . "</center></th></tr>\n");
            $res = DB::run("SELECT id,name,minclasswrite FROM forum_forums ORDER BY name");
            print("<tr><td class='ttable_col2'>\n");
            print("<form method='post' action='" . URLROOT . "/forums/renametopic'>\n");
            print("<input type='hidden' name='topicid' value='$topicid' />\n");
            print("<input type='hidden' name='returnto' value='forums/viewtopic&amp;topicid=$topicid' />\n");

            print("<div align='center'  style='padding:3px'>Rename topic:
            <div class='row justify-content-md-center'>
            <div class='col col-lg-4'>
            <input class='form-control' type='text' name='subject' size='30' maxlength='100' value='" . stripslashes(htmlspecialchars($subject)) . "' />
            </div>
            </div>
            \n");

            print("<input type='submit' value='Apply' />");
            print("</div></form>\n");
            print("<form method='post' action='" . URLROOT . "/forums/movetopic&amp;topicid=$topicid'>\n");
            print("<div align='center' style='padding:3px'>");
            print("Move this thread to: <select name='forumid'>");
            while ($arr = $res->fetch(PDO::FETCH_ASSOC)) {
                if ($arr["id"] != $forumid && $_SESSION["class"] >= $arr["minclasswrite"]) {
                    print("<option value='" . $arr["id"] . "'>" . $arr["name"] . "</option>\n");
                }
            }

            print("</select> <input type='submit' value='Apply' /></div></form>\n");
            print("<div align='center'>\n");
            if ($locked) {
                print(Lang::T("FORUMS_LOCKED") . ": <a href='" . URLROOT . "/forums/unlocktopic&amp;forumid=$forumid&amp;topicid=$topicid&amp;page=$page' title='Unlock'><i class='fa fa-unlock tticon-red' title='UnLock Topic'></i></a>\n");
            } else {
                print(Lang::T("FORUMS_LOCKED") . ": <a href='" . URLROOT . "/forums/locktopic&amp;forumid=$forumid&amp;topicid=$topicid&amp;page=$page' title='Lock'><i class='fa fa-lock tticon' title='Lock Topic'></i></a>\n");
            }

            print("Delete Entire Topic: <a href='" . URLROOT . "/forums/deletetopic&amp;topicid=$topicid&amp;sure=0' title='Delete'><i class='fa fa-trash-o tticon-red' title='Delete Topic'></i></a>\n");
            if ($sticky) {
                print(Lang::T("FORUMS_STICKY") . ": <a href='" . URLROOT . "/forums/unsetsticky&amp;forumid=$forumid&amp;topicid=$topicid&amp;page=$page' title='UnStick'><i class='fa fa-exclamation tticon-red' title='Unstick Topic'></i></a>\n");
            } else {
                print(Lang::T("FORUMS_STICKY") . ": <a href='" . URLROOT . "/forums/setsticky&amp;forumid=$forumid&amp;topicid=$topicid&amp;page=$page' title='Stick'><i class='fa fa-exclamation tticon' title='stick Topic'></i></a>\n");
            }

            print("</div></td></tr></table></div>\n");

        }
        Style::end();
        Style::footer();
        die;
    }

    /**
     * Delete a Post.
     */
    public function deletepost()
    {
        $this->validForumUser();
        $postid = Input::get("postid");
        $sure = Input::get("sure");
        if ($_SESSION["delete_forum"] != "yes" || !Validate::Id($postid)) {
            Redirect::autolink(URLROOT . '/forums', Lang::T("FORUMS_DENIED"));
        }
		//SURE?
	    if ($sure == "0") {
		    Redirect::autolink(URLROOT . '/forums', "Sanity check: You are about to delete a post. Click <a href='" . URLROOT . "/forums/deletepost?postid=$postid&sure=1'>here</a> if you are sure.");
        }
        // Get topic id
        $arr = Forum::getForumPostTopicId($postid);
        $topicid = $arr[0];
        // We can not delete the post if it is the only one of the topic
        $arr = Forum::countForumPost($topicid);
        if ($arr < 2) {
            $msg = sprintf(Lang::T("FORUMS_DEL_POST_ONLY_POST"), $topicid);
            Redirect::autolink(URLROOT . '/forums', $msg);
        }
        // Delete post
        Forum::deleteForumPost($postid);
        // Delete attachment todo
        $sql = DB::run("SELECT * FROM attachments WHERE content_id =?", [$postid]);
        if ($sql->rowCount() != 0) {
            foreach ($sql as $row7) {
                $daimage = TORRENTDIR . "/attachment/$row7[file_hash].data";
                if (file_exists($daimage)) {
                    if (unlink($daimage)) {
                        DB::run("DELETE FROM attachments WHERE content_id=?", [$postid]);
                    }
                }
                $extension = substr($row7['filename'], -3);
                if ($extension != 'zip') {
                    $dathumb = "uploads/thumbnail/$row7[file_hash].jpg";
                    if (!unlink($dathumb)) {
                        Redirect::autolink(URLROOT . "/forums/viewtopic&topicid=$topicid", "Could not remove thumbnail = $row7[file_hash].jpg");
                    }
                }
            }
        }
        // Update topic
        update_topic_last_post($topicid);
        Redirect::autolink(URLROOT . "/forums/viewtopic&topicid=$topicid", Lang::T("_SUCCESS_DEL_"));
        die;
    }

    /**
     * Delete a Topic.
     */
    public function deletetopic()
    {
        $this->validForumUser();
        $topicid = Input::get("topicid");
        if (!Validate::Id($topicid) || $_SESSION["delete_forum"] != "yes") {
            Redirect::autolink(URLROOT . "/forums", Lang::T("FORUMS_DENIED"));
        }
        $sure = Input::get("sure");
        if ($sure == "0") {
            Redirect::autolink(URLROOT . "/forums", "Sanity check: You are about to delete a topic. Click <a href='" . URLROOT . "/forums/deletetopic&amp;topicid=$topicid?sure=1'>here</a> if you are sure.");
        }
        Forum::deltopic($topicid);
        Redirect::autolink(URLROOT . "/forums", Lang::T("_SUCCESS_DEL_"));
        die;
    }

    /**
     * Rename a Topic.
     */
    public function renametopic()
    {
        $this->validForumUser();
        if ($_SESSION["delete_forum"] != "yes" && $_SESSION["edit_forum"] != "yes") {
            Redirect::autolink(URLROOT . "/forums", Lang::T("FORUMS_DENIED"));
        }
        $topicid = Input::get('topicid');
        if (!Validate::Id($topicid)) {
            Redirect::autolink(URLROOT . "/forums", Lang::T("FORUMS_DENIED"));
        }
        $subject = Input::get('subject');
        if ($subject == '') {
            Redirect::autolink(URLROOT . "/forums", Lang::T("FORUMS_YOU_MUST_ENTER_NEW_TITLE"));
        }
        $subject = $subject;
        Forum::rename($subject, $topicid);
        $returnto = Input::get('returnto');
        if ($returnto) {
            Redirect::to($returnto);
        }
        die;
    }

    /**
     * Move a Topic.
     */
    public function movetopic()
    {
        $this->validForumUser();
        $forumid = Input::get("forumid");
        $topicid = Input::get("topicid");
        if (!Validate::Id($forumid) || !Validate::Id($topicid) || $_SESSION["delete_forum"] != "yes" || $_SESSION["edit_forum"] != "yes") {
            Redirect::autolink(URLROOT . "/forums", "Invalid ID - $topicid");
        }
        // Make sure topic and forum is valid
        $res = Forum::minClassWrite($forumid);
        if ($res->rowCount() != 1) {
            Redirect::autolink(URLROOT . "/forums", Lang::T("FORUMS_NOT_FOUND"));
        }
        $arr = $res->fetch(PDO::FETCH_LAZY);
        if ($_SESSION['class'] < $arr[0]) {
            Redirect::autolink(URLROOT . "/forums", Lang::T("FORUMS_NOT_ALLOWED"));
        }
        $res = Forum::getSubjectForunId($topicid);
        if ($res->rowCount() != 1) {
            Redirect::autolink(URLROOT . "/forums", Lang::T("FORUMS_NOT_FOUND_TOPIC"));
        }
        $arr = $res->fetch(PDO::FETCH_ASSOC);
        if ($arr["forumid"] != $forumid) {
            Forum::moveTopic($forumid, $topicid);
        }
        Redirect::to(URLROOT . "/forums/viewforum&forumid=$forumid");
        die;
    }

    /**
     * Lock a Topic.
     */
    public function locktopic()
    {
        $this->validForumUser();
        $forumid = Input::get("forumid");
        $topicid = Input::get("topicid");
        $page = Input::get("page");
        if (!Validate::Id($topicid) || $_SESSION["delete_forum"] != "yes" || $_SESSION["edit_forum"] != "yes") {
            Redirect::autolink(URLROOT . "/forums", Lang::T("FORUMS_DENIED"));
        }
        Forum::lock($topicid, 'yes');
        Redirect::to(URLROOT . "/forums/viewforum&forumid=$forumid&page=$page");
        die;
    }

    /**
     * Unlock a Topic.
     */
    public function unlocktopic()
    {

        $this->validForumUser();
        $forumid = Input::get("forumid");
        $topicid = Input::get("topicid");
        $page = Input::get("page");
        if (!Validate::Id($topicid) || $_SESSION["delete_forum"] != "yes" || $_SESSION["edit_forum"] != "yes") {
            Redirect::autolink(URLROOT . "/forums", Lang::T("FORUMS_DENIED"));
        }
        Forum::lock($topicid, 'no');
        Redirect::to(URLROOT . "/forums/viewforum&forumid=$forumid&page=$page");
        die;
    }

    /**
     * Set Topic Sticky.
     */
    public function setsticky()
    {
        $this->validForumUser();
        $forumid = Input::get("forumid");
        $topicid = Input::get("topicid");
        $page = Input::get("page");
        if (!Validate::Id($topicid) || ($_SESSION["delete_forum"] != "yes" && $_SESSION["edit_forum"] != "yes")) {
            Redirect::autolink(URLROOT . "/forums", Lang::T("FORUMS_DENIED"));
        }
        Forum::sticky($topicid, 'yes');
        Redirect::to(URLROOT . "/forums/viewforum&forumid=$forumid&page=$page");
        die;
    }

    /**
     * Unstick a Topic.
     */
    public function unsetsticky()
    {
        $this->validForumUser();
        $forumid = Input::get("forumid");
        $topicid = Input::get("topicid");
        $page = Input::get("page");
        if (!Validate::Id($topicid) || ($_SESSION["delete_forum"] != "yes" && $_SESSION["edit_forum"] != "yes")) {
            Redirect::autolink(URLROOT . "/forums", Lang::T("FORUMS_DENIED"));
        }
        Forum::sticky($topicid, 'no');
        Redirect::to(URLROOT . "/forums/viewforum&forumid=$forumid&page=$page");
        die;
    }

    public function user()
    {
        $id = (int) Input::get("id");
        if (!isset($id) || !$id) {
            Redirect::autolink(URLROOT, Lang::T("ERROR"));
        }
        if ($_SESSION["view_users"] == "no" && $_SESSION["id"] != $id) {
            Redirect::autolink(URLROOT, Lang::T("NO_USER_VIEW"));
        }

        $res = DB::run("SELECT
            forum_posts.id, topicid, userid, forum_posts.added, body,
            avatar, signature, username, title, class, uploaded, downloaded, privacy, donated
            FROM forum_posts
            LEFT JOIN users
            ON forum_posts.userid = users.id
            WHERE userid = $id ORDER BY forum_posts.userid "); //$limit

        $row = $res->fetch(PDO::FETCH_LAZY);

        if (!$row) {
            Redirect::autolink(URLROOT, "User has not posted in forum");
        }
        $title = Lang::T("COMMENTSFOR") . "<a href='profile?id=" . $row['userid'] . "'>&nbsp;$row[username]</a>";

        $data = [
            'title' => $title,
            'id' => $id,
            'res' => $res,
        ];
        View::render('forum/userposts', $data, 'user');

    }

}