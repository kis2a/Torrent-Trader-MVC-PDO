<?php
class Bonuses
{

    // Get Bonus by Post Id
    public static function getBonusByPost($id)
    {
        $res = DB::run("SELECT `type`, `value`, `cost` FROM `bonus` WHERE `id` = '$_POST[id]'");
        $row = $res->fetch(PDO::FETCH_LAZY);
        return $row;
    }

    // Get Bonus Order By Type
    public static function getAll()
    {
        $res = DB::run("SELECT * FROM `bonus` ORDER BY `type`");
        $row1 = $res->fetchAll();
        return $row1;
    }

    // Set User Bonus
    public static function setBonus($cost, $id)
    {
        $row = DB::run("UPDATE `users` SET `seedbonus` = `seedbonus` - '$cost' WHERE `id` = '$id'");
    }

}
