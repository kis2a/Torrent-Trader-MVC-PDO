<?php
class Peer
{
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function seedingTorrent($id, $seeder)
    {
        $sql = $this->db->run("SELECT `torrent`, `uploaded`, `downloaded` 
                               FROM `peers` 
                               LEFT JOIN torrents 
                               ON torrent = torrents.id 
                               WHERE userid = ? AND seeder = ?", [$id, $seeder]);
        return $sql;
    }
}
