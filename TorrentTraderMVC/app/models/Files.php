<?php
class Files
{
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function insertFiles($id, $name, $size)
    {
        $this->db->run("INSERT INTO `files` (`torrent`, `path`, `filesize`) VALUES (?, ?, ?)", [$id, $name, $size]);
    }
}