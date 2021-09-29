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

    public static function getname($type, $votedfor)
    {
        switch ($type) {
            case "user":
                $q = DB::run("SELECT username FROM users WHERE id = ?", [$votedfor])->fetch();
                $test = array('name' => $q['username']);
                break;
            case "torrent":
                $q = DB::run("SELECT name FROM torrents WHERE id = ?", [$votedfor])->fetch();
                $test = array('name' => $q['name']);
                break;
            case "comment":
                $q = DB::run("SELECT text, news, torrent FROM comments WHERE id = ?", [$votedfor])->fetch();
                $test = array('name' => $q['text'], 'news' => $q['news'], 'torrent' => $q['torrent']);
                break;
            case "forum":
                $q = DB::run("SELECT subject FROM forum_topics WHERE id = ?", [$votedfor])->fetch();
                $test = array('name' => $q['subject']);
                break;
            case "req":
                $q = DB::run("SELECT request FROM requests WHERE id = ?", [$votedfor])->fetch();
                $test = array('name' => $q['request']);
                break;
        }
        return $test;
    }

}
