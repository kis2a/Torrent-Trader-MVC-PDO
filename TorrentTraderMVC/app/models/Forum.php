<?php
class Forum
{

    public static function getIndex()
    {
        $stmt = DB::run("
              SELECT forumcats.id
              AS fcid, forumcats.name AS fcname, forum_forums.*
              FROM forum_forums
              LEFT JOIN forumcats
              ON forumcats.id = forum_forums.category WHERE sub = 0
              ORDER BY forumcats.sort, forum_forums.sort, forum_forums.name");
        return $stmt;
    }

    public static function getsub()
    {
        $stmt = DB::run("
              SELECT forumcats.id
              AS fcid, forumcats.name AS fcname, forum_forums.*
              FROM forum_forums
              LEFT JOIN forumcats
              ON forumcats.id = forum_forums.category WHERE sub != 0
              ORDER BY forumcats.sort, forum_forums.sort, forum_forums.name");
        return $stmt;
    }

    public static function viewunread()
    {
        $stmt = DB::run("
              SELECT id, forumid, subject, lastpost
              FROM forum_topics
              ORDER BY lastpost");
        return $stmt;
    }

    public static function sticky($topicid, $opt = 'yes')
    {
        $stmt = DB::run("
              UPDATE forum_topics
              SET sticky=?
              WHERE id=?", [$opt, $topicid]);
    }

    public static function lock($topicid, $opt = 'yes')
    {
        $stmt = DB::run("
              UPDATE forum_topics
              SET locked=?
              WHERE id=?", [$opt, $topicid]);
    }

    public static function rename($subject, $topicid)
    {
        $stmt = DB::run("
              UPDATE forum_topics
              SET subject=?
              WHERE id=?", [$subject, $topicid]);
    }

    public static function deltopic($topicid)
    {
        DB::run("DELETE FROM forum_topics WHERE id=?", [$topicid]);
        DB::run("DELETE FROM forum_posts WHERE topicid=?", [$topicid]);
        DB::run("DELETE FROM forum_readposts WHERE topicid=?", [$topicid]);
        // delete attachment
        $sql = DB::run("SELECT * FROM attachments WHERE topicid =?", [$topicid]);
        if ($sql->rowCount() != 0) {
            foreach ($sql as $row7) {
                //print("<br>&nbsp;<b>$row7[filename]</b><br>");
                $daimage = TORRENTDIR . "/attachment/$row7[file_hash].data";
                if (file_exists($daimage)) {
                    if (unlink($daimage)) {
                        DB::run("DELETE FROM attachments WHERE content_id=?", [$row7['id']]);
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
    }

    public static function canRead($forumid) {
        $res2 = DB::run("SELECT * FROM forum_forums WHERE id=?", [$forumid]);
        $arr2 = $res2->fetch(PDO::FETCH_ASSOC);
        return $arr2;
    }

    public static function searchForum($keywords) {
        $stmt = DB::run("SELECT forum_posts.topicid, forum_posts.userid, forum_posts.id, forum_posts.added,
                 MATCH ( forum_posts.body ) AGAINST ( ? ) AS relevancy
                 FROM forum_posts
                 WHERE MATCH ( forum_posts.body ) AGAINST ( ? IN BOOLEAN MODE )
                 ORDER BY added DESC", ['%' . $keywords . '%', '%' . $keywords . '%']);
        return $stmt;
    }

    public static function getForumTopic($forumid, $limit) {
        $stmt = DB::run("SELECT * FROM forum_topics WHERE forumid=$forumid ORDER BY sticky, lastpost  DESC $limit")->fetchAll();
        return $stmt;
    }

    public static function getMinRead($forumid) {
        $stmt = DB::run("SELECT name, minclassread, guest_read FROM forum_forums WHERE id=?", [$forumid]);
        return $stmt;
    }

    public static function getForumPost($postid) {
        $stmt = DB::run("SELECT * FROM forum_posts WHERE id=?", [$postid]);
        return $stmt;
    }

    public static function updateForumPost($body, $editedat, $id, $postid) {
        $stmt = DB::run("UPDATE forum_posts SET body=?, editedat=?, editedby=? WHERE id=?", [$body, $editedat, $id, $postid]);
    }

    public static function getForumPostTopicId($postid) {
        $stmt = DB::run("SELECT topicid FROM forum_posts WHERE id=?", [$postid])->fetch(PDO::FETCH_LAZY) 
                or Redirect::autolink(URLROOT . '/forums', Lang::T("FORUMS_NOT_FOUND_POST"));
        return $stmt;
    }

    public static function countForumPost($topicid) {
        $stmt = DB::run("SELECT COUNT(*) FROM forum_posts WHERE topicid=?", [$topicid])->fetchcolumn();
        //$count = $stmt->rowCount();
        return $stmt;
    }
    
    public static function deleteForumPost($postid) {
        DB::run("DELETE FROM forum_posts WHERE id=?", [$postid]);
    }
        
    public static function minClassWrite($forumid) {
        $stmt = DB::run("SELECT minclasswrite FROM forum_forums WHERE id=?", [$forumid]);
        return $stmt;
    }
        
    public static function getSubjectForunId($topicid) {
        $stmt = DB::run("SELECT subject,forumid FROM forum_topics WHERE id=?", [$topicid]);
        return $stmt;
    }
        
    public static function moveTopic($forumid, $topicid) {
        DB::run("UPDATE forum_topics SET forumid=$forumid, moved='yes' WHERE id=$topicid");
    }

}