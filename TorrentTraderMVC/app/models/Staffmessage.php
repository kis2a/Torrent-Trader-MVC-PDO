<?php

class Staffmessage
{

    public static function insertStaffMessage($userid, $added, $msg, $sub)
    {
        $stmt = DB::run("INSERT INTO staffmessages (sender, added, msg, subject) VALUES(?,?,?,?)", [$userid, $added, $msg, $sub]);
        $count = $stmt->rowCount();
        return $count;
    }

    public static function getAll()
    {
        $row = DB::run("SELECT * FROM staffmessages ORDER BY id desc");
        return $row;
    }

}