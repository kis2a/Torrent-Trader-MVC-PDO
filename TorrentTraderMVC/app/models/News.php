<?php
class News
{

    public static function selectAll($id)
    {
        $row = DB::run("SELECT * FROM news WHERE id =?", [$id])->fetch(PDO::FETCH_LAZY);
        return $row;
    }

}