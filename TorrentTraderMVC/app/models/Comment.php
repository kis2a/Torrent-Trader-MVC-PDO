<?php
class Comment
{

    public static function commentPager($id, $type)
    {
        $commcount = DB::run("SELECT COUNT(*) FROM comments WHERE $type =?", [$id])->fetchColumn();
        if ($commcount) {
            list($pagertop, $pagerbottom, $limit) = pager(10, $commcount, "comments?id=$id&amp;type=$type");
            $commres = DB::run("SELECT comments.id, text, user, comments.added, avatar, signature, username, title, class, uploaded, downloaded, privacy, donated FROM comments LEFT JOIN users ON comments.user = users.id WHERE $type = $id ORDER BY comments.id $limit");
        } else {
            unset($commres);
        }
        return $pager = [
            'pagertop' => $pagertop,
            'commres' => $commres,
            'pagerbottom' => $pagerbottom,
            'limit' => $limit,
            'commcount' => $commcount,
        ];
    }

    public static function selectCommentUser($id)
    {
        $row = DB::run("SELECT comments.id, text, user, comments.added, avatar,
                               signature, username, title, class, uploaded, downloaded, privacy, donated
                        FROM comments
                        LEFT JOIN users
                        ON comments.user = users.id
                        WHERE user = $id ORDER BY comments.id ")->fetch(PDO::FETCH_LAZY);
        return $row;
    }

    public static function selectByRequest($id)
    {
        $row = DB::run("SELECT * FROM comments WHERE req =?", [$id])->fetch(PDO::FETCH_LAZY);
        return $row;
    }

    public static function selectAll($id)
    {
        $row = DB::run("SELECT * FROM comments WHERE id=?", [$id])->fetch();
        return $row;
    }

    public static function delete($id)
    {
        $row = DB::run("DELETE FROM comments WHERE id =?", [$id]);
    }

    public static function updateText($text, $id)
    {
        $row = DB::run("UPDATE comments SET text=? WHERE id=?", [$text, $id]);
    }

    
    public static function insert($type, $user, $id, $added, $text)
    {
        $row = DB::run("INSERT INTO comments (user, " . $type . ", added, text) VALUES (?, ?, ?, ?)", [$user, $id, $added, $text]);
        return $row->rowCount();
    }

    public static function selectTorrent($id)
    {
        $row = DB::run("SELECT torrent FROM comments WHERE id=?", [$id])->fetch(PDO::FETCH_ASSOC);
        return $row;
    }

}