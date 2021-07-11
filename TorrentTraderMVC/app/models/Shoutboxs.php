<?php
class Shoutboxs
{

    public static function insertShout($userid, $date, $user, $message)
    {
        DB::run("INSERT INTO shoutbox (userid, date, user, message) VALUES(?,?,?,?)", [$userid, $date, $user, $message]);
    }

    public static function getAllShouts($limit = 20)
    {
        $stmt = DB::run("SELECT * FROM shoutbox WHERE staff = 0 ORDER BY msgid DESC LIMIT $limit");
        return $stmt;
    }

    public static function checkFlood($message, $username)
    {
        $stmt = DB::run("SELECT COUNT(*) FROM shoutbox 
                        WHERE message=? AND user=? AND UNIX_TIMESTAMP(?)-UNIX_TIMESTAMP(date) < ?", 
                        [$message, $username, TimeDate::get_date_time(), 30])->fetch(PDO::FETCH_LAZY);
        return $stmt;
    }

    public static function getByShoutId($id)
    {
        $stmt = DB::run("SELECT * FROM shoutbox WHERE msgid=?", [$id])->fetch(PDO::FETCH_LAZY);
        return $stmt;
    }

    public static function deleteByShoutId($id)
    {
        DB::run("DELETE FROM shoutbox WHERE msgid=?", [$id]);
    }

    public static function updateShout($message, $id)
    {
        DB::run("UPDATE shoutbox SET message=? WHERE msgid=?", [$message, $id]);
    }

}