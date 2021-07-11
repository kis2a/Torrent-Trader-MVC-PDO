<?php
class Logs
{

    public static function write($text)
    {
        $text = $text;
        $added = TimeDate::get_date_time();
        DB::run("INSERT INTO log (added, txt) VALUES (?,?)", [$added, $text]);
    }
}