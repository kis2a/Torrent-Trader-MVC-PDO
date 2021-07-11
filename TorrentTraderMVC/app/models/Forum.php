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
              ON forumcats.id = forum_forums.category
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

}