<?php
class Cleanup
{
    // Automatic System Update Function
    public static function autoclean()
    {
        $now = TimeDate::gmtime();
        $docleanup = 0;

        $res = DB::run("SELECT last_time FROM tasks WHERE task='cleanup'");
        $row = $res->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            DB::run("INSERT INTO tasks (task, last_time) VALUES ('cleanup',$now)");
            return;
        }

        $ts = $row['last_time']; // $row['0'] returned null now int string
        if ($ts + Config::TT()['AUTOCLEANINTERVAL'] > $now) {
            return;
        }

        $planned_clean = DB::run("UPDATE tasks SET last_time=? WHERE task=? AND last_time =?", [$now, 'cleanup', $ts]);
        if (!$planned_clean) {
            return;
        }

        self::run();
    }

    // Invite update function
    public static function autoinvites($interval, $minlimit, $maxlimit, $minratio, $invites, $maxinvites)
    {
        $time = TimeDate::gmtime() - ($interval * 86400);
        $minlimit = $minlimit * 1024 * 1024 * 1024;
        $maxlimit = $maxlimit * 1024 * 1024 * 1024;
        $res = DB::run("SELECT id, username, class, invites FROM users WHERE enabled = 'yes' AND status = 'confirmed' AND downloaded >= $minlimit AND downloaded < $maxlimit AND uploaded / downloaded >= $minratio AND warned = 'no' AND UNIX_TIMESTAMP(invitedate) <= $time");
        if ($res->rowCount() > 0) {
            while ($arr = $res->fetch(PDO::FETCH_ASSOC)) {
                $maxninvites = $maxinvites[$arr['class']];
                if ($arr['invites'] >= $maxninvites) {
                    continue;
                }

                if (($maxninvites - $arr['invites']) < $invites) {
                    $invites = $maxninvites - $arr['invites'];
                }

                DB::run("UPDATE users SET invites = invites+$invites, invitedate = NOW() WHERE id=$arr[id]");
                Logs::write("Gave $invites invites to '$arr[username]' - Class: " . Groups::get_user_class_name($arr['class']) . "");
            }
        }
    }

    public static function run() {
        self::deletepeers();
        self::makevisible();
        self::bonus();
        self::vipuntil();
        self::pendinguser();
        self::deletelogs();
        self::freeleech();
        if (Config::TT()['RATIOWARNENABLE']) {
            self::ratiowarn();
        }
        self::expiredwarn();
        self::iswarned();
        self::autoinvite();
        if (HNR_ON) {
            self::hitnrun();
        }
    }

    public static function deletepeers()
    {
        // LOCAL TORRENTS - DELETE OLD NON-ACTIVE PEERS
        $deadtime = TimeDate::get_date_time(TimeDate::gmtime() - Config::TT()['ANNOUNCEINTERVAL']);
        DB::run("DELETE FROM peers WHERE last_action < ?", [$deadtime]);
    }

    public static function makevisible()
    {
        // LOCAL TORRENTS - MAKE NON-ACTIVE/OLD TORRENTS INVISIBLE
        $deadtime = TimeDate::gmtime() - Config::TT()['MAXDEADTORRENTTIMEOUT'];
        DB::run("UPDATE torrents SET visible=?
             WHERE visible=? AND last_action < FROM_UNIXTIME(?) AND seeders = ? AND leechers = ? AND external !=?",
            ['no', 'yes', $deadtime, 0, 0, 'yes']);
    }

    public static function bonus()
    {
        // every hour
        $res = DB::run("SELECT last_time FROM tasks WHERE task='bonus'");
        $row = $res->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            DB::run("INSERT INTO tasks (task, last_time) VALUES (?,?)", ['bonus', TimeDate::gmtime()]);
        }
        if ($row['last_time'] + Config::TT()['ADDBONUS'] < TimeDate::gmtime()) {
            $res1 = DB::run("SELECT DISTINCT userid as peer, (
                         SELECT DISTINCT COUNT( torrent )
                         FROM peers
                         WHERE seeder = ?  AND userid = peer) AS count
                         FROM peers WHERE seeder = ?", ['yes', 'yes'])->fetchAll();
            foreach ($res1 as $row) {
                DB::run("UPDATE users SET seedbonus = seedbonus + '" . (Config::TT()['BONUSPERTIME'] * $row['count']) . "' WHERE id = ?", [$row['peer']]);
                DB::run("UPDATE tasks SET last_time=? WHERE task=?", [TimeDate::gmtime(), 'bonus']);
            }
        }
    }

    public static function vipuntil()
    {
        $subject = 'Your VIP class stay has just expired';
        $msg = 'Your VIP class stay has just expired';
        $rowv = DB::run("SELECT id, oldclass FROM users WHERE vipuntil = ? AND oldclass != ?", [null, 0])->fetchAll();
        if ($rowv) {
            DB::run("UPDATE users SET class =?, oldclass=?, vipuntil =? WHERE vipuntil < ?", [$rowv['oldclass'], 0, null, TimeDate::get_date_time()]);
            DB::run("INSERT INTO messages (sender, receiver, added, subject, msg, poster) VALUES(?, ?, ?, ?, ?, ?)", [0, $rowv['id'], TimeDate::get_date_time(), $subject, $msg, 0]);
        }
    }

    public static function pendinguser()
    {
        // DELETE PENDING USER ACCOUNTS OVER TIMOUT AGE
        $deadtime = TimeDate::gmtime() - Config::TT()['SIGNUPTIMEOUT'];
        DB::run("DELETE FROM users WHERE status = ? AND added < FROM_UNIXTIME(?)", ['pending', $deadtime]);
    }

    public static function deletelogs()
    {
        $ts = TimeDate::gmtime() - LOGCLEAN;
        DB::run("DELETE FROM log WHERE added < FROM_UNIXTIME(?)", [$ts]);
    }

    public static function freeleech()
    {
        if (Config::TT()['FREELEECHGBON']);{
            $query = DB::run("SELECT `id`, `name` FROM `torrents` WHERE `banned` = ? AND `freeleech` = ? AND `size` >= ?", ['no', 0, Config::TT()['FREELEECHGB']])->fetchAll();
            if ($query) {
                foreach ($query as $row) {
                    DB::run("UPDATE `torrents` SET `freeleech` = ? WHERE `id` = ?", [1, $row['id']]);
                    Logs::write("Freeleech added on  <a href='torrent?id=$row[id]'>$row[name]</a> because it is bigger than " . Config::TT()['FREELEECHGB'] . "");
                }
            }
        }
    }

    public static function ratiowarn()
    {
        // LEECH WARN USERS WITH LOW RATIO
        $downloaded = Config::TT()['RATIOWARN_MINGIGS'] * 1024 * 1024 * 1024;
        // ADD RATIO WARNING
        $res = DB::run("SELECT id,username FROM users WHERE class <= ? AND warned = ? AND enabled= ? AND uploaded / downloaded < ? AND downloaded >= ?", [_UPLOADER, 'no', 'yes', Config::TT()['RATIOWARNMINRATIO'], $downloaded])->fetchAll();
        if ($res) {
            $reason = "You have been warned because of having low ratio. You need to get a " . Config::TT()['RATIOWARNMINRATIO'] . " before next " . Config::TT()['RATIOWARN_DAYSTOWARN'] . " days or your account may be banned.";
            $expiretime = gmdate("Y-m-d H:i:s", TimeDate::gmtime() + (86400 * Config::TT()['RATIOWARN_DAYSTOWARN']));
            foreach ($res as $arr) {
                DB::run("INSERT INTO warnings (userid, reason, added, expiry, warnedby, type) VALUES (?,?,?,?,?,?)", [$arr["id"], $reason, TimeDate::get_date_time(), $expiretime, 0, 'Poor Ratio']);
                DB::run("UPDATE users SET warned=? WHERE id=?", ['yes', $arr["id"]]);
                DB::run("INSERT INTO messages (sender, receiver, added, msg, poster) VALUES (?,?,?,?,?)", [0, $arr["id"], TimeDate::get_date_time(), $reason, 0]);
                Logs::write("Auto Leech warning has been <b>added</b> for: <a href='" . URLROOT . "/profile?id=" . $arr["id"] . "'>" . Users::coloredname($arr["username"]) . "</a>");
            }
        }
        // REMOVE RATIO WARNING
        $res1 = DB::run("SELECT users.id, users.username FROM users INNER JOIN warnings ON users.id=warnings.userid
                         WHERE type=? AND active = ? AND warned = ?  AND enabled=? AND uploaded / downloaded >= ? AND downloaded >= ?", ['Poor Ratio', 'yes', 'yes', 'yes', Config::TT()['RATIOWARNMINRATIO'], $downloaded])->fetchAll();
        if ($res1) {
            $reason = "Your warning of low ratio has been removed. We highly recommend you to keep a your ratio up to not be warned again.\n";
            foreach ($res1 as $arr1) {
                Logs::write("Auto Leech warning has been removed for: <a href='" . URLROOT . "/profile?id=" . $arr1["id"] . "'>" . Users::coloredname($arr1["username"]) . "</a>");
                DB::run("UPDATE users SET warned = ? WHERE id = ?", ['no', $arr1["id"]]);
                DB::run("UPDATE warnings SET expiry = ?, active = ? WHERE userid = ?", [TimeDate::get_date_time(), 'no', $arr1["id"]]);
                DB::run("INSERT INTO messages (sender, receiver, added, msg, poster) VALUES (?,?,?,?,?)", [0, $arr1["id"], TimeDate::get_date_time(), $reason, 0]);
            }
        }
        // BAN RATIO WARNED USERS
        $res = DB::run("SELECT users.id, users.username, UNIX_TIMESTAMP(warnings.expiry) AS expiry FROM users INNER JOIN warnings ON users.id=warnings.userid
                        WHERE type=? AND active = ? AND class = ? AND enabled=? AND warned = ? AND uploaded / downloaded < ? AND downloaded >= ?", ['Poor Ratio', 'yes', 1, 'yes', 'yes', Config::TT()['RATIOWARNMINRATIO'], $downloaded])->fetchAll();
        if ($res) {
            $expires = (86400 * Config::TT()['RATIOWARN_DAYSTOWARN']);
            foreach ($res as $arr) {
                if (TimeDate::gmtime() - $arr["expiry"] >= 0) {
                    DB::run("UPDATE users SET enabled=?, warned=? WHERE id=?", ['no', 'no', $arr["id"]]);
                    Logs::write("User <a href='" . URLROOT . "/profile?id=" . $arr["id"] . "'>" . Users::coloredname($arr["username"]) . "</a> has been banned (Auto Leech warning).");
                }
            }
        }
    }

    public static function expiredwarn()
    {
        // REMOVE EXPIRED WARNINGS
        $res = DB::run("SELECT users.id, users.username, warnings.expiry FROM users INNER JOIN warnings ON users.id=warnings.userid
                    WHERE type != ? AND warned = ?  AND enabled=? AND warnings.active = ? AND warnings.expiry < ?", ['Poor Ratio', 'yes', 'yes', 'yes', TimeDate::get_date_time()])->fetchAll();
        if ($res) {
            foreach ($res as $arr1) {
                DB::run("UPDATE users SET warned = ? WHERE id = ?", ['no', $arr1['id']]);
                DB::run("UPDATE warnings SET active = ? WHERE userid = ? AND expiry < ?", ['no', $arr1['id'], TimeDate::get_date_time()]);
                Logs::write("Removed warning for $arr1[username]. Expiry: $arr1[expiry]");
            }
        }
    }

    public static function iswarned()
    {
        // UPDATE USERS THAT STILL HAVE ACTIVE WARNINGS
        DB::run("UPDATE users SET warned = 'yes' WHERE warned = 'no' AND id IN (SELECT userid FROM warnings WHERE active = 'yes')");
    }

    public static function autoinvite()
    {
        // GIVE INVITES ACCORDING TO RATIO/GIGS (max 20)
        self::autoinvites(14, 1, 4, 0.90, 2, 20);
    }

    public static function hitnrun()
    {
        $timenow = TimeDate::gmtime();
        // my edits
        $length = HNR_DEADLINE * 86400; // 7 days
        $seedtime = HNR_SEEDTIME * 3600; // 48 hours
        // deadline here 7 days - seedtime/target 48 hours
        DB::run("UPDATE snatched SET hnr = ? WHERE completed = ? AND hnr = ? AND uload < dload AND $timenow - $length > stime AND $seedtime > ltime AND done=?", ['yes', 1, 'no', 'no']);
        DB::run("UPDATE `snatched` SET `hnr` = ? WHERE `hnr` = ? AND uload >= dload", ['no', 'yes']);
        // seedtime/target
        DB::run("UPDATE `snatched` SET `hnr` = ? WHERE `hnr` = ? AND ltime >= ?", ['no', 'yes', $seedtime]);
        $a = DB::run("SELECT DISTINCT uid FROM snatched WHERE hnr = ? AND done= ?", ['yes', 'no']);
        if ($a->rowCount() > 0):
            while ($b = $a->fetch(PDO::FETCH_ASSOC)):
                $user = $b['uid'];
                $count = DB::run("SELECT COUNT( hnr ) FROM snatched WHERE uid = ? AND hnr = ?", [$user, 'yes'])->fetchColumn();
                
                //$length = HNR_DEADLINE;
                $expiretime = gmdate("Y-m-d H:i:s", $timenow + $length);

                $f = DB::run("SELECT type, active FROM warnings WHERE userid = ?", [$user])->fetch(PDO::FETCH_ASSOC);
                $type = $f['type'];
                $active = $f['active'];
                // warn 5 hit & runs then warned
                if ($count >= HNR_WARN && $type != "HnR"):
                    $reason = "" . Lang::T("CLEANUP_WARNING_FOR_ACCUMULATING") . " " . HNR_WARN . " H&R.";
                    $subject = "" . Lang::T("CLEANUP_WARNING_FOR_H&R") . "";
                    $msg = "" . Lang::T("CLEANUP_YOU_HAVE_BEEN_WARNEWD_ACCUMULATED") . " " . HNR_WARN . " " . Lang::T("CLEANUP_H&R_INVITE_CHECK_RULE") . "\n[color=red]" . Lang::T("CLEANUP_MSG_WARNING_7_DAYS_BANNED") . "[/color]";

                    $rov = DB::run("SELECT enabled FROM users WHERE id = ?", [$user])->fetch(PDO::FETCH_ASSOC);
                    if ($rov["enabled"] == "yes"):
                        DB::run("UPDATE users SET warned = 'yes' WHERE id = $user");
                        DB::run("INSERT INTO warnings (userid, reason, added, expiry, warnedby, type) VALUES (?,?,?,?,?,?)", [$user, $reason, TimeDate::get_date_time(), $expiretime, 0, 'HnR']);
                        DB::run("INSERT INTO messages (sender, receiver, added, subject, msg, poster) VALUES (?,?,?,?,?,?)", [0, $user, TimeDate::get_date_time(), $subject, $msg, 1]);
                    endif;
                endif;
                // Unwarned below 5 hit & runs then unwarn
                if ($count < HNR_WARN && $type == "HnR"):
                    $subject = "" . Lang::T("CLEANUP_REMOVAL_OF_H&R_WARNING") . "";
                    $msg = "" . Lang::T("CLEANUP_YOU_NOW_HAVE_LESS_THAN") . " " . HNR_WARN . " H&R.\n" . Lang::T("CLEANUP_YOUR_WARNING_FOR_H&R_HAS_REMOVED") . "";
                    DB::run("UPDATE users SET warned = 'no' WHERE id = $user");
                    DB::run("DELETE FROM warnings WHERE userid = $user AND type = 'HnR'");
                    DB::run("INSERT INTO messages (sender, receiver, added, subject, msg, poster) VALUES (?,?,?,?,?,?)", [0, $user, TimeDate::get_date_time(), $subject, $msg, 1]);
                endif;
                // Ban after 50
                if ($count >= HNR_BAN):
                    $g = DB::run("SELECT username, email, modcomment FROM users WHERE id = $user");
                    $h = $g->fetch(PDO::FETCH_ASSOC);
                    $modcomment = $h[2];
                    $modcomment = gmdate("d/m/Y") . " - " . Lang::T("CLEANUP_BANNED_FOR") . " " . $count . " H&R.\n " . $modcomment;
                    DB::run("UPDATE users SET enabled = 'no', warned = 'no', modcomment = '$modcomment' WHERE id = $user");
                    DB::run("DELETE FROM warnings WHERE userid = $user AND type = 'HnR'");
                    Logs::write(Lang::T("CLEANUP_THE_MEMBER") . " <a href='account-details.php?id=" . $user . "'>" . $h[0] . "</a> " . Lang::T("CLEANUP_HAS_BEEN_BANNED_REASON") . " " . $count . " H&R.");
                    $subject = "" . Lang::T("CLEANUP_YOUR_ACCOUNT") . " " . Config::TT()['SITENAME'] . " " . Lang::T("CLEANUP_HAS_BEEN_DISABLED") . "";
                    $body = "" . Lang::T("CLEANUP_YOU_WERE_BANNED_FOLLOWING") . "\n
																					------------------------------
																					\n/" . Lang::T("CLEANUP_YOU_HAVE_ACCUMULATED") . " $count H&R.\n
																					------------------------------
																					\n" . Lang::T("CLEANUP_YOU_CAN_CONTACT_BY_LINK") . " :
																					" . URLROOT . "/contact.php
																					\n\n\n" . Config::TT()['SITENAME'] . " " . Lang::T("ADMIN");
                    $TTMail = new TTMail();
                    $TTMail->Send($h[1], "$subject", "$body", "" . Lang::T("OF") . ": " . Config::TT()['SITEEMAIL'] . "", "-f" . Config::TT()['SITEEMAIL'] . "");
                endif;
                // Ban download after 15
                if ($count >= HNR_STOP_DL){
                    DB::run("UPDATE users SET downloadbanned = ? WHERE id = ?", ['yes', $user]);
                }
            endwhile;
        endif;
    }

    public static function optimize()
    {
        // OPTIMIZE TABLES
        $res = DB::run("SHOW TABLES");
        while ($table = $res->fetch(PDO::FETCH_LAZY)) {
            // Get rid of overhead.
            DB::run("REPAIR TABLE `$table[0]`;");
            // Analyze table for faster indexing.
            DB::run("ANALYZE TABLE `$table[0]`;");
            // Optimize table to minimize thrashing.
            DB::run("OPTIMIZE TABLE `$table[0]`;");
        }
    }

}