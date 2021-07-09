<?php
class Reports
{

    public static function selectReport($addedby, $votedfor, $type)
    {
        $stmt = DB::run("SELECT id FROM reports WHERE addedby =? AND votedfor=? AND type =?", [$addedby, $votedfor, $type]);
        return $stmt;
    }

    public static function selectForumReport($addedby, $votedfor, $xtra, $type)
    {
        $stmt = DB::run("SELECT id FROM reports WHERE addedby =? AND votedfor=? AND votedfor_xtra=? AND type =?", [$addedby, $votedfor, $xtra, $type]);
        return $stmt;
    }

    public static function insertReport($addedby, $votedfor, $type, $reason, $xtra = 0)
    {
        DB::run("INSERT into reports (addedby,votedfor,votedfor_xtra,type,reason) VALUES (?, ?, ?, ?, ?)", [$addedby, $votedfor, $xtra, $type, $reason]);
    }

}
