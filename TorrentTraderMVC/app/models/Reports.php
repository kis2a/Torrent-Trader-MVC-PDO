<?php
class Reports
{
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function selectReport($addedby, $votedfor, $type)
    {
        $stmt = $this->db->run("SELECT id FROM reports WHERE addedby =? AND votedfor=? AND type =?", [$addedby, $votedfor, $type]);
        return $stmt;
    }

    public function selectForumReport($addedby, $votedfor, $xtra, $type)
    {
        $stmt = $this->db->run("SELECT id FROM reports WHERE addedby =? AND votedfor=? AND votedfor_xtra=? AND type =?", [$addedby, $votedfor, $xtra, $type]);
        return $stmt;
    }

    public function insertReport($addedby, $votedfor, $type, $reason, $xtra = 0)
    {
        $this->db->run("INSERT into reports (addedby,votedfor,votedfor_xtra,type,reason) VALUES (?, ?, ?, ?, ?)", [$addedby, $votedfor, $xtra, $type, $reason]);
    }

}
