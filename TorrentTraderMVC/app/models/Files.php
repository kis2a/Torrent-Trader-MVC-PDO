<?php
class Files
{

    public static function insertFiles($id, $name, $size)
    {
        DB::run("INSERT INTO `files` (`torrent`, `path`, `filesize`) VALUES (?, ?, ?)", [$id, $name, $size]);
    }
}