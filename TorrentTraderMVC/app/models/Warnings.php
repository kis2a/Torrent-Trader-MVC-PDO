<?php
class Warnings
{

    public static function getWarningById($id)
    {
        $user = DB::run("SELECT * FROM warnings WHERE userid=? ORDER BY id DESC", [$id]);
        return $user;
    }

    public static function insertWarning($userid, $reason, $timenow, $expiretime, $warnedby ,$type)
    {
        DB::run("INSERT INTO warnings (userid, reason, added, expiry, warnedby, type) 
        VALUES (?,?,?,?,?,?)", [$userid, $reason, $timenow, $expiretime, $warnedby, $type]);
        //return $user;
    }

}
