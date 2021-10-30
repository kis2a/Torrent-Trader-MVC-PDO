<?php
class Bonus
{

    public function __construct()
    {
        $this->session = Auth::user(0, 2);
    }

    public function index()
    {
        $id = (int) Input::get("id");
        if (Validate::Id($id)) {
            $row = Bonuses::getBonusByPost($id);
            if (!$row || $_SESSION['seedbonus'] < $row['cost']) {
                Redirect::autolink(URLROOT."/bonus", "Demand not valid.");
            }
            $cost = $row['cost'];
            Bonuses::setBonus($cost, $_SESSION['id']);
            $this->bonusswitch($row);
            Redirect::autolink(URLROOT."/bonus", "Your account has been credited.");
        }

        $row1 = Bonuses::getAll();
        $data = [
            'title' => 'Seed Bonus',
            'bonus' => $row1,
            'usersbonus' => $_SESSION['seedbonus'],
            'configbonuspertime' => Config::TT()['BONUSPERTIME'],
            'configautoclean_interval' => floor(Config::TT()['ADDBONUS'] / 60),
			'usersid' => $_SESSION['id'],
        ];
        View::render('bonus/index', $data, 'user');
    }

    private function bonusswitch($row)
    {
        switch ($row['type']) {
            case 'invite':
                DB::run("UPDATE `users` SET `invites` = `invites` + '$row[value]' WHERE `id` = '$_SESSION[id]'");
                break;
            case 'traffic':
                DB::run("UPDATE `users` SET `uploaded` = `uploaded` + '$row[value]' WHERE `id` = '$_SESSION[id]'");
                break;
            case 'HnR':
                $uid = $_SESSION["id"];
                if (empty($uid)) {
                    Redirect::autolink(URLROOT, "You are not a member ?.");
                }
                if (isset($uid)) {
                    $res = DB::run("SELECT * FROM `snatched` WHERE `uid` = ? AND `hnr` = ?", [$uid, 'yes']);
                    if ($res->rowCount() == 0) {
                        Redirect::autolink("bonus", "No HnR found for this user.");
                    }
                    Logs::write("A HnR for <a href='profile?id=" . $uid . "'>" . Users::coloredname($_SESSION['username']) . "</a> has been removed");
                    $new_modcomment = gmdate("d-m-Y \à H:i") . " - ";
                    $new_modcomment .= "" . Users::coloredname($_SESSION['username']) . " has cleared H&R for " . $row['cost'] . " points \n";
                    $modcom = $new_modcomment;
                    DB::run("UPDATE `users` SET `modcomment` = CONCAT($modcom,modcomment) WHERE id = ?", [$uid]);
                    DB::run("UPDATE `snatched` SET `ltime` = ?, `hnr` = ? WHERE `uid` = ?", [129600, 'no', $uid]);
                }
                break;
            case 'other':
                break;
            case 'VIP':
                $days = $row['value'];
                $vipuntil = ($_SESSION["vipuntil"] > "0000-00-00 00:00:00") ? $vipuntil = TimeDate::get_date_time(strtotime($_SESSION["vipuntil"]) + (60 * 86400)) : $vipuntil = TimeDate::get_date_time(TimeDate::gmtime() + (60 * 86400));
                $oldclass = ($_SESSION["vipuntil"] > "0000-00-00 00:00:00") ? $oldclass = $_SESSION["oldclass"] : $oldclass = $_SESSION["class"];
                DB::run("UPDATE `users` SET `class` = '3', `oldclass`='$oldclass', `vipuntil` = '$vipuntil' WHERE `id` = '$_SESSION[id]'");
                break;
        }
    }

    public function trade()
    {
        $uid = (int) $_SESSION['id'];

        $qry = "SELECT 
                    snatched.tid as tid, 
                    torrents.name, 
                    torrents.size, 
	                snatched.uload, 
                    snatched.dload, 
		            snatched.ltime, 
		            snatched.hnr, 
		            users.uploaded, 
		            users.seedbonus, 
		            users.modcomment 
		            FROM snatched 
		            INNER JOIN users ON snatched.uid = users.id 
		            INNER JOIN torrents ON snatched.tid = torrents.id
		            WHERE users.status = 'confirmed'
		            AND snatched.uid = '$uid'
		            AND snatched.hnr = 'yes'
		            AND snatched.done = 'no'
		            ORDER BY stime DESC";
        $res = DB::run($qry);
        
        if ($_POST["requestpoints"]) {
            while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
                $torid = $_POST['torid'];
                $modcom = $row['modcomment'];
                }
                $modcomment = gmdate("d-M-Y") . " - " . Lang::T("DELETED_RECORDING") . ": " . $torid . " " . Lang::T("POINTS_OF_SEED_BONUS") . "\n" . $modcom;
                DB::run("UPDATE users SET seedbonus = seedbonus - '100', modcomment = ? WHERE id = ?", [$modcomment, $uid]);
                DB::run("UPDATE snatched SET ltime = '86400', hnr = 'no', done = 'yes' WHERE tid = ? AND uid = ?", [$torid, $uid]);
                Logs::write("<a href=" . URLROOT . "/profile?id=$_SESSION[id]><b>$_SESSION[username]</b></a> " . Lang::T("DELETED_RECORDING") . ": <a href=" . URLROOT . "/torrent?id=$torid><b>$torid</b></a> " . Lang::T("POINTS_OF_SEED_BONUS") . "");
                Redirect::autolink(URLROOT . "/bonus/trade", Lang::T("ONE_RECORDING_HIT_AND_RUN_DELETED"));
        }

        if ($_POST["requestupload"]) {
                while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
                    $torid = $_POST['torid'];
                    $torsize = $row['size'];
                    $viewsize = mksize($row['size']);
                    $modcom = $row['modcomment'];
                }
                $modcomment = gmdate("d-M-Y") . " - " . Lang::T("DELETED_RECORDING") . ": " . $torid . " with " . $viewsize . " " . Lang::T("OF_UPLOAD") . "\n" . $modcom;
                DB::run("UPDATE users SET uploaded = uploaded - '$torsize', modcomment = ? WHERE id = ?", [$modcomment, $uid]);
                DB::run("UPDATE snatched SET ltime = '86400', hnr = 'no', done = 'yes' WHERE tid = '$torid' AND uid = '$uid'");
                Logs::write("<a href=" . URLROOT . "/profile?id=$_SESSION[id]><b>$_SESSION[username]</b></a> " . Lang::T("DELETED_RECORDING") . ": <a href=" . URLROOT . "/torrent?id=$torid><b>$torid</b></a> " . Lang::T("HIT_AND_RUN_WITH") . " <b>$viewsize</b> " . Lang::T("OF_UPLOAD") . "");
                Redirect::autolink(URLROOT . "/bonus/trade", Lang::T("ONE_RECORDING_HIT_AND_RUN_DELETED"));
        }
            
        if ($res->rowCount() > 0) {
                Style::header(Lang::T("YOUR_RECORDINGS_OF_HIT_AND_RUN"));
                Style::begin(Lang::T("YOUR_RECORDINGS_OF_HIT_AND_RUN"));
                echo "<p class='text-center'>To solve this problem you must to keep seeding these torrents for ".HNR_SEEDTIME." hours or until ratio becomes 1:1<br>
                  But if you want a fast way, you can trade to delete these recordings With of Upload<p>"; ?>
            
               <form method="post" action="<?php echo URLROOT; ?>/bonus/trade">
               <div class='table-responsive'> <table class='table table-striped'><thead><tr>
               <th><?php echo Lang::T("TORRENT_NAME"); ?></th>
               <th><i class='fa fa-upload tticon' title='Uploaded'></i></th>
               <th><i class='fa fa-download tticon' title='Downloaded'></i></th>
               <th><?php echo Lang::T("SEED_TIME"); ?></th>
               <th><?php echo Lang::T("DELETE"); ?></th>
               </tr></thead><tbody>
              <?php
              while ($row = $res->fetch(PDO::FETCH_ASSOC)):
                $torid = $row['tid'];
                $tosize = $row['size'];
                $upload = $row['uploaded'];
                $points = $row['seedbonus'];
                $smallname = htmlspecialchars(CutName($row['name'], 40));
                $dispname = "<b>" . $smallname . "</b>"; ?>
				<tr>
		        <td align='left' class='table_col1'><a href=<?php echo URLROOT ?>/torrent?id=<?php $row['tid'] ?>&hit=1><?php echo $dispname ?></a></td>
			    <td class="table_col2"><font color="#27B500"><?php echo mksize($row['uload']); ?></font></td>
			    <td class="table_col1"><font color="#FF2200"><?php echo mksize($row['dload']); ?></font></td>
			    <td class="table_col2"><?php echo ($row['uploaded']) ? TimeDate::mkprettytime($row['ltime']) : '---'; ?></td>
			    <td class="table_col1" align="left">
		          <?php
		          if ($points >= 100) { ?>
			         <input type='hidden' name='torid' value="<?php echo $torid; ?>">
			         <input type="submit" class="button" name="requestpoints" value="Delete">&nbsp; <?php echo Lang::T("SNATCHLIST_COST"); ?> <font color="#FF2200"><b>100</b></font> <?php echo Lang::T("SNATCHLIST_POINTS_OF_SEED_BONUS"); ?>
		          <?php } else { ?>
		             <font color="#FF1200">&nbsp;<?php echo Lang::T("SNATCHLIST_YOU_DONT_HAVE_ENOUGH"); ?> <b><?php echo Lang::T("SNATCHLIST_SEEDBONUS"); ?></b> <?php echo Lang::T("SNATCHLIST_FOR_TRADING"); ?></font><?php
			       }
			       if ($upload > $tosize) { ?>
			         <input type='hidden' name='torid' value="<?php echo $torid; ?>">
			         <div style="margin-top:2px"><input type="submit" class="button" name="requestupload" value="Delete">&nbsp; <?php echo Lang::T("SNATCHLIST_COST"); ?> <font color="#FF2200"><b><?php echo mksize($tosize); ?></b></font> <?php echo Lang::T("SNATCHLIST_UPLOAD"); ?></div>
		           <?php } else {?>
		             <div style="margin-top:2px"><font color="#FF1200">&nbsp;<?php echo Lang::T("SNATCHLIST_YOU_DONT_HAVE_ENOUGH"); ?> <b><?php echo Lang::T("SNATCHLIST_UPLOAD"); ?></b> <?php echo Lang::T("SNATCHLIST_FOR_TRADING"); ?></font></div> <?php
		            } ?>
		        </td>
			    </tr><?php
			   endwhile; ?>
             </tbody></table></div>
             </form>
             <?php
           Style::end();
           Style::footer();
 
        } else {
            Redirect::autolink(URLROOT, Lang::T("THERE_ARE_NO_RECORDINGS"));
        }
    }
    
}