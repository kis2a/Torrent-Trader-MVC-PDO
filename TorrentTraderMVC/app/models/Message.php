<?php
class Message
{

    public static function countmsg()
    {
        $res = DB::run("SELECT COUNT(*), COUNT(`unread` = 'yes') FROM messages WHERE `receiver` = $_SESSION[id] AND `location` IN ('in','both')");
        $res = DB::run("SELECT COUNT(*) FROM messages WHERE receiver=" . $_SESSION["id"] . " AND `location` IN ('in','both')");
        $inbox = $res->fetchColumn();
        $res = DB::run("SELECT COUNT(*) FROM messages WHERE `receiver` = " . $_SESSION["id"] . " AND `location` IN ('in','both') AND `unread` = 'yes'");
        $unread = $res->fetchColumn();
        $res = DB::run("SELECT COUNT(*) FROM messages WHERE `sender` = " . $_SESSION["id"] . " AND `location` IN ('out','both')");
        $outbox = $res->fetchColumn();
        $res = DB::run("SELECT COUNT(*) FROM messages WHERE `sender` = " . $_SESSION["id"] . " AND `location` = 'draft'");
        $draft = $res->fetchColumn();
        $res = DB::run("SELECT COUNT(*) AS count FROM messages WHERE `sender` = " . $_SESSION["id"] . " AND `location` = 'template'");
        $template = $res->fetchColumn();

        $arr = [
            'inbox' => $inbox,
            'unread' => $unread,
            'outbox' => $outbox,
            'draft' => $draft,
            'template' => $template,
        ];
        return $arr;
    }

    public static function insertmessage($sender, $receiver, $added, $subject, $msg, $unread, $location, $poster = 0)
    {
        DB::run("INSERT INTO `messages`
        (`sender`, `receiver`, `added`, `subject`, `msg`, `poster`, `unread`, `location`)
                 VALUES (?,?,?,?,?,?,?,?)",
            [$sender, $receiver, $added, $subject, $msg, $poster, $unread, $location]
        );
        // email notif
        $user = DB::run("SELECT id, username, acceptpms, notifs, email FROM users WHERE id=?", [$receiver])->fetch(PDO::FETCH_ASSOC);
        if (strpos($user['notifs'], '[pm]') !== false) {
            $body = "You have received a PM\n\nYou can use the URL below to view the message (you may have to login).\n\n$url/messages\n\n".Config::TT()['SITENAME']."";
            $url = URLROOT;
            $email = Config::TT()['SITEEMAIL'];
            $TTMail = new TTMail();
            $TTMail->Send($user["email"], "You have received a PM", $body, "From: " . Config::TT()['SITEEMAIL'] . "", "-f" . Config::TT()['SITEEMAIL'] . "");
        }
    }

    public static function updateRead($id, $receiver)
    {
        DB::run("UPDATE messages SET `unread` = 'no' 
               WHERE `id` = $id AND `receiver` = $receiver");
    }

    public static function getallmsg($id)
    {
        $res = DB::run('SELECT * FROM messages WHERE id = ?', [$id])->fetch(PDO::FETCH_ASSOC);
        return $res;
    }
    
    public static function updateMessage($msg, $id)
    {
        DB::run('UPDATE messages SET msg = ? WHERE id = ?', [$msg, $id]);
    }

    public static function msgPagination($type)
    {
        switch ($type) {
            case 'inbox':
                $where = "`receiver` = $_SESSION[id] AND `location` IN ('in','both') ORDER BY added DESC";
                $count = DB::run("SELECT COUNT(*) FROM messages WHERE $where")->fetchColumn();
                list($pagertop, $pagerbottom, $limit) = pager(25, $count, URLROOT."/messages?type=inbox&");
                break;
            case 'outbox':
                $where = "`sender` = $_SESSION[id] AND `location` IN ('out','both') ORDER BY added DESC";
                $count= DB::run("SELECT COUNT(*) FROM messages WHERE $where")->fetchColumn();
                list($pagertop, $pagerbottom, $limit) = pager(25, $count, URLROOT."/messages?type=outbox&");
                break;
            case 'templates':
                $where = "`sender` = $_SESSION[id] AND `location` = 'template' ORDER BY added DESC";
                $count= DB::run("SELECT COUNT(*) FROM messages WHERE $where")->fetchColumn();
                list($pagertop, $pagerbottom, $limit) = pager(25, $count, URLROOT."/messages?type=templates&");
                break;
            case 'draft':
                $where = "`sender` = $_SESSION[id] AND `location` = 'draft' ORDER BY added DESC";
                $count = DB::run("SELECT COUNT(*) FROM messages WHERE $where")->fetchColumn();
                list($pagertop, $pagerbottom, $limit) = pager(25, $count, URLROOT."/messages?type=draft&");
                break;
            }
        $arr = ['pagertop' => $pagertop, 'pagerbottom' => $pagerbottom, 'limit' => $limit, 'where' => $where];
        return $arr;
    }
}