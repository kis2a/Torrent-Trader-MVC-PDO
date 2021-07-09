<?php
class Comment
{

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

    
    public static function insert($type, $user, $id, $added, $text)
    {
        $row = DB::run("INSERT INTO comments (user, " . $type . ", added, text) VALUES (?, ?, ?, ?)", [$user, $id, $added, $text]);
        return $row->rowCount();
    }
}
