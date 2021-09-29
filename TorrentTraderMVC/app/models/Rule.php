<?php
class Rule
{

    public static function getRules()
    {
        $stmt = DB::run("SELECT * FROM `rules` ORDER BY `id`");
        return $stmt;
    }

}