<?php
class Blocks
{
    public static function left()
    {
        $TTCache = new Cache();
        $db = Database::instance();
        if (($blocks = $TTCache->get("blocks_left", 900)) === false) {
            $res = $db->run("SELECT * FROM blocks WHERE position='left' AND enabled=1 ORDER BY sort");
            $blocks = array();
            while ($result = $res->fetch(PDO::FETCH_LAZY)) {
                $blocks[] = $result["name"];
            }
            $TTCache->Set("blocks_left", $blocks, 900);
        }
        foreach ($blocks as $blockfilename) {
            if (!in_array($_GET['url'], ISURL)) {
                include "../app/views/blocks/" . $blockfilename . "_block.php";
            }
        }
    }

    public static function right()
    {
        $TTCache = new Cache();
        $db = Database::instance();
        if (($blocks = $TTCache->get("blocks_right", 900)) === false) {
            $res = $db->run("SELECT * FROM blocks WHERE position='right' AND enabled=1 ORDER BY sort");
            $blocks = array();
            while ($result = $res->fetch(PDO::FETCH_LAZY)) {
                $blocks[] = $result["name"];
            }
            $TTCache->Set("blocks_right", $blocks, 900);
        }
        foreach ($blocks as $blockfilename) {
            if (!in_array($_GET['url'], ISURL)) {
                include "../app/views/blocks/" . $blockfilename . "_block.php";
            }
        }
    }

    public static function middle()
    {
        $TTCache = new Cache();
        $db = Database::instance();
        if (($blocks = $TTCache->get("blocks_middle", 900)) === false) {
            $res = $db->run("SELECT * FROM blocks WHERE position='middle' AND enabled=1 ORDER BY sort");
            $blocks = array();
            while ($result = $res->fetch(PDO::FETCH_LAZY)) {
                $blocks[] = $result["name"];
            }
            $TTCache->Set("blocks_middle", $blocks, 900);
        }
        foreach ($blocks as $blockfilename) {
            if (!in_array($_GET['url'], ISURL)) {
                include "../app/views/blocks/" . $blockfilename . "_block.php";
            }
        }
    }

    public static function resortleft()
    {
        $db = Database::instance();
        $sortleft = $db->run("SELECT sort, id FROM blocks WHERE position='left' AND enabled=1 ORDER BY sort ASC");
        $i = 1;
        while ($sort = $sortleft->fetch(PDO::FETCH_ASSOC)) {
            $db->run("UPDATE blocks SET sort = $i WHERE id=" . $sort["id"]);
            $i++;
        }
    }

    public static function resortmiddle()
    {
        $db = Database::instance();
        $sortmiddle = $db->run("SELECT sort, id FROM blocks WHERE position='middle' AND enabled=1 ORDER BY sort ASC");
        $i = 1;
        while ($sort = $sortmiddle->fetch(PDO::FETCH_ASSOC)) {
            $db->run("UPDATE blocks SET sort = $i WHERE id=" . $sort["id"]);
            $i++;
        }
    }

    public static function resortright()
    {
        $db = Database::instance();
        $sortright = $db->run("SELECT sort, id FROM blocks WHERE position='right' AND enabled=1 ORDER BY sort ASC");
        $i = 1;
        while ($sort = $sortright->fetch(PDO::FETCH_ASSOC)) {
            $db->run("UPDATE blocks SET sort = $i WHERE id=" . $sort["id"]);
            $i++;
        }
    }

    public static function getblock($enabled)
    {
        $isenabled = DB::run("SELECT named, name, description, position, sort 
                 FROM blocks WHERE enabled=$enabled ORDER BY position, sort");
        return $isenabled;
    }

    public static function getposition($position)
    {
        $getposition = DB::run("SELECT position FROM blocks 
                                WHERE position='left' AND enabled=1")->rowCount() + 1;
        return $getposition;
    }

    public static function delete($delthis)
    {
        DB::run("DELETE FROM blocks WHERE id=?" [$delthis]);
    }

    public static function update($position, $sort, $id)
    {
        DB::run("UPDATE blocks SET position = ?, sort =? WHERE id =? ", [$position, $sort, $id]);
    }

    public static function move($id)
    {
        $move = DB::run("SELECT position, sort, id FROM blocks WHERE id = ?" [$id]);
        return $move;
    }

    public static function getall()
    {
        $arr = DB::run("SELECT * FROM blocks ORDER BY id");
        return $arr;
    }

    public static function getorder()
    {
        $arr = DB::run("SELECT * FROM blocks ORDER BY enabled DESC, position, sort");
        return $arr;
    }

    public static function getname()
    {
        $arr = DB::run("SELECT name FROM blocks");
        return $arr;
    }

    public static function insert($wantedname, $name, $description)
    {
        DB::run("INSERT INTO blocks (named, name, description, position, enabled, sort) VALUES (?,?,?,?,?,?)", [$wantedname, $name, $description, 'left', 0, 0]);
    }

}