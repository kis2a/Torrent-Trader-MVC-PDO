<?php
class News
{
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function selectAll($id)
    {
        $row = $this->db->run("SELECT * FROM news WHERE id =?", [$id])->fetch(PDO::FETCH_LAZY);
        return $row;
    }
/*
    public function selectUserEmail($id)
    {
        $row = $this->db->run("SELECT email FROM users WHERE id=?", [$id])->fetch(PDO::FETCH_ASSOC);
        return $row;
    }
*/
}