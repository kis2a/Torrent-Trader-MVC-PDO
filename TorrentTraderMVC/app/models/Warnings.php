<?php
class Warnings
{
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function getWarningById($id)
    {
        $user = $this->db->run("SELECT * FROM warnings WHERE userid=? ORDER BY id DESC", [$id]);
        return $user;
    }

    public function insertWarning($userid, $reason, $timenow, $expiretime, $warnedby ,$type)
    {
        $this->db->run("INSERT INTO warnings (userid, reason, added, expiry, warnedby, type) 
        VALUES (?,?,?,?,?,?)", [$userid, $reason, $timenow, $expiretime, $warnedby, $type]);
        //return $user;
    }

}
