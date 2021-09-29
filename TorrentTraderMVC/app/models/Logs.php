<?php
class Logs
{

    public static function write($text)
    {
        $text = $text;
        $added = TimeDate::get_date_time();
        DB::run("INSERT INTO log (added, txt) VALUES (?,?)", [$added, $text]);
    }

    public static function countWhere($where)
    {
        $count = DB::run("SELECT COUNT(*) FROM log $where")->fetchColumn();
        return $count;
    }

    public static function getAll($where, $limit)
    {
        $stmt = DB::run("SELECT id, added, txt FROM log $where ORDER BY id DESC $limit");
        return $stmt;
    }

}