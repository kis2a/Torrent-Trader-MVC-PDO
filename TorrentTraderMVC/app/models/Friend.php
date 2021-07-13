<?php
class Friend
{

    public static function countFriendAndEnemy($userid, $id)
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

    public static function getall($userid, $type)
    {
        $arr = DB::run("SELECT f.friendid as id, u.username AS name, u.class, u.avatar, u.title, u.enabled, u.last_access 
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

    public static function getalltype($userid, $type)
    {
        $stmt = DB::run("SELECT f.friendid as id, u.username AS name, u.class, u.avatar, u.title, u.enabled, u.last_access FROM friends AS f LEFT JOIN users as u ON f.friendid = u.id WHERE userid=? AND friend=? ORDER BY name", [$userid, $type])->fetch();
        return $stmt;
    }

    public static function insert($userid, $targetid, $type)
    {
        DB::run("INSERT INTO friends (userid, friendid, friend) VALUES (?,?,?)", [$userid, $targetid, $type]);
    }

    public static function isMyFriend($userid, $targetid)
    {
        $stmt = DB::run("SELECT id FROM friends WHERE userid=? AND userid=?", [$userid, $targetid]);
        return $stmt;
    }

}