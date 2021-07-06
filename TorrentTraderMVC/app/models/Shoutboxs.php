<?php
class Shoutboxs
{
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function insertShout($userid, $date, $user, $message)
    {
        $this->db->run("INSERT INTO shoutbox (userid, date, user, message) VALUES(?,?,?,?)", [$userid, $date, $user, $message]);
    }

    public function getAllShouts($limit = 20)
    {
        $stmt = $this->db->run("SELECT * FROM shoutbox WHERE staff = 0 ORDER BY msgid DESC LIMIT $limit");
        return $stmt;
    }

    public function checkFlood($message, $username)
    {
        $stmt = $this->db->run("SELECT COUNT(*) FROM shoutbox 
                                WHERE message=? AND user=? AND UNIX_TIMESTAMP(?)-UNIX_TIMESTAMP(date) < ?", 
                                [$message, $username, TimeDate::get_date_time(), 30])->fetch(PDO::FETCH_LAZY);
        return $stmt;
    }

    public function getByShoutId($id)
    {
        $stmt = $this->db->run("SELECT * FROM shoutbox WHERE msgid=?", [$id])->fetch(PDO::FETCH_LAZY);
        return $stmt;
    }

    public function deleteByShoutId($id)
    {
        $this->db->run("DELETE FROM shoutbox WHERE msgid=?", [$id]);
    }

    public function updateShout($message, $id)
    {
        DB::run("UPDATE shoutbox SET message=? WHERE msgid=?", [$message, $id]);
    }

}
