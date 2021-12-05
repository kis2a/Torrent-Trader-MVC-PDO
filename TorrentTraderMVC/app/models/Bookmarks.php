<?php
class Bookmarks
{
    public static function select($target, $type = 'torrent')
    {
        $bookt = DB::run("SELECT COUNT(*) FROM bookmarks WHERE targetid = ? AND `type` = ? AND userid = ?", [$target, $type, $_SESSION['id']])->fetchColumn();
        if ($bookt > 0) {
            print("<a href=".URLROOT."/bookmark/delete?target=$target><button type='button' class='btn btn-sm ttbtn'>Delete Bookmark</button></a>");
        } else {
            print("<a href=".URLROOT."/bookmark/add?target=$target><button type='button' class='btn btn-sm ttbtn'>Add Bookmark</button></a>");
        }
    }
}