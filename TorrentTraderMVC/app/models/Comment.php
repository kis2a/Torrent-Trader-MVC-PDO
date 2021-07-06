<?php
class Comment
{
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function selectByRequest($id)
    {
        $row = $this->db->run("SELECT * FROM comments WHERE req =?", [$id])->fetch(PDO::FETCH_LAZY);
        return $row;
    }

    public function selectAll($id)
    {
        $row = $this->db->run("SELECT * FROM comments WHERE id=?", [$id])->fetch();
        return $row;
    }

    public function delete($id)
    {
        $row = $this->db->run("DELETE FROM comments WHERE id =?", [$id]);
    }

    
    public function insert($type, $user, $id, $added, $text)
    {
        $row = $this->db->run("INSERT INTO comments (user, " . $type . ", added, text) VALUES (?, ?, ?, ?)", [$user, $id, $added, $text]);
        return $row->rowCount();
    }
}
