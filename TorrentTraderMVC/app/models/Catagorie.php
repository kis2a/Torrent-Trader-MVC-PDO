<?php
class Catagorie
{

    public static function genrelist()
    {
        $ret = array();
        $res = DB::run("SELECT id, name, parent_cat FROM categories ORDER BY parent_cat ASC, sort_index ASC");
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $ret[] = $row;
        }
        return $ret;
    }

    public static function dropdown()
    {
        $cats = self::genrelist();
        $catdropdown = "";
        foreach ($cats as $cat) {
            $catdropdown .= "<option value=\"" . $cat["id"] . "\"";
            if ($cat["id"] == @$_GET["cat"]) {
                $catdropdown .= " selected=\"selected\"";
            }
            $catdropdown .= ">" . htmlspecialchars($cat["parent_cat"]) . ": " . htmlspecialchars($cat["name"]) . "</option>\n";
        }
        return $catdropdown;
    }

}