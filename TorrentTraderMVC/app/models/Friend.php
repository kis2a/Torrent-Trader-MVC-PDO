<?php
class Friend
{
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function countFriendAndEnemy($userid, $id)
    {
        $r = DB::run("SELECT id FROM friends WHERE userid=? AND friend=? AND friendid=?", [$userid, 'friend', $id]);
        $friend = $r->rowCount();
        $r = DB::run("SELECT id FROM friends WHERE userid=? AND friend=? AND friendid=?", [$userid, 'enemy', $id]);
        $block = $r->rowCount();

        $arr = [
            'friend' => $friend,
            'enemy' => $block
        ];
        return $arr;
    }

    public function getall($userid, $type)
    {
        $arr = $this->db->run("SELECT f.friendid as id, u.username AS name, u.class, u.avatar, u.title, u.enabled, u.last_access 
                               FROM friends AS f 
                               LEFT JOIN users as u ON f.friendid = u.id 
                               WHERE userid=? AND friend=? ORDER BY name", [$userid, $type]);
        $count = $arr->rowCount();
        $data = [
            'count' => $count,
            '$arr' => $arr,
        ];

        return $data;
    }
}