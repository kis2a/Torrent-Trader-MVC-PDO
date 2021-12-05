<?php
foreach ($data['selecttor'] as $torr):
    torrentmenu($data['id'], $torr['external']);
    ?>
	<div class="jumbotron">
	<div class="row">
	<div class="col">
	    <b><?php echo Lang::T("DESCRIPTION"); ?>:</b>&nbsp;<?php echo format_comment($torr['descr']); ?><br>
	    <b><?php echo Lang::T("CATEGORY"); ?>:</b>&nbsp;<?php echo $torr["cat_parent"]; ?> -> <?php echo $torr["cat_name"]; ?><br>
	    <?php
    if (empty($torr["lang_name"])) {
        $torr["lang_name"] = "Unknown/NA";
    }?>
	    <b><?php echo Lang::T("LANG"); ?>:</b>&nbsp;<?php echo $torr["lang_name"]; ?><br>
	    <?php
    if (isset($torr["lang_image"]) && $torr["lang_image"] != "") {
        print("&nbsp;<img border=\"0\" src=\"" . URLROOT . "/assets/images/languages/" . $torr["lang_image"] . "\" alt=\"" . $torr["lang_name"] . "\" />");
    }?>
	    <b><?php echo Lang::T("TOTAL_SIZE"); ?>:</b>&nbsp;<?php echo mksize($torr["size"]); ?><br>
	    <b><?php echo Lang::T("INFO_HASH"); ?>:</b>&nbsp;<?php echo $torr["info_hash"]; ?><br> <?php
    if ($torr["anon"] == "yes" && !$torr['owned']) {?>
	        <b><?php echo Lang::T("ADDED_BY"); ?>:</b>&nbsp; Anonymous<br>
	        <?php
    } elseif ($torr["username"]) {?>
        <b><?php echo Lang::T("ADDED_BY"); ?>:</b>&nbsp;<a href='profile?id=<?php echo $torr["owner"]; ?>'><?php echo Users::coloredname($torr["username"]); ?></a><br><?php
} else {?>
        <b><?php echo Lang::T("ADDED_BY"); ?>:</b>&nbsp; Unknown<br><?php
}?>
        <b><?php echo Lang::T("FREELEECH"); ?>:</b>&nbsp;<?php echo $torr['freeleech']; ?><br>
        <b><?php echo Lang::T("DATE_ADDED"); ?>:</b>&nbsp;<?php echo date("d-m-Y H:i:s", TimeDate::utc_to_tz_time($torr["added"])); ?><br>
        <b><?php echo Lang::T("VIEWS"); ?>:</b>&nbsp;<?php echo number_format($torr["views"]); ?><br>
        <b><?php echo Lang::T("HITS"); ?>:</b>&nbsp;<?php echo number_format($torr["hits"]); ?><br>
        <?php // LIKE MOD
if (Config::TT()['ALLOWLIKES']) {
    $data1 = DB::run("SELECT user FROM likes WHERE liked=? AND type=? AND user=? AND reaction=?", [$torr['id'], 'torrent', $_SESSION['id'], 'like']);
    $likes = $data1->fetch(PDO::FETCH_ASSOC);
    if ($likes) {?>
                <b>Reaction:</b>&nbsp;<a href='<?php echo URLROOT; ?>/likes?id=<?php echo $torr['id']; ?>&type=unliketorrent'><i class='fa fa-thumbs-up tticon' title='Like'></i></a><br><?php
} else {?>
                <b>Reaction:</b>&nbsp;<a href='<?php echo URLROOT; ?>/likes?id=<?php echo $torr['id']; ?>&type=liketorrent'><i class='fa fa-thumbs-up tticon' title='Like'></i></a><br><?php
}
}
if (Config::TT()['ALLOWLIKES']) {
    $data3 = DB::run("SELECT * FROM `users` AS u LEFT JOIN `likes` AS l ON(u.id = l.user) WHERE liked=? AND type=?", [$torr['id'], 'torrent']);
    print('<b>Liked by</b>&nbsp;');
    foreach ($data3 as $stmt):
        print("<a href='" . URLROOT . "/profile?id=$stmt[id]'>" . Users::coloredname($stmt['username']) . "</a>&nbsp;");
    endforeach;
}
echo "<br />";
if ($torr["external"] != 'yes' && $torr["freeleech"] == '1') {
    print("<b>" . Lang::T("FREE_LEECH") . ": </b><font color='#ff0000'>" . Lang::T("FREE_LEECH_MSG") . "</font><br />");
}
if ($torr["external"] != 'yes' && $torr["vip"] == 'yes') {
    print("<b>Torrent VIP: </b><font color='orange'>Torrent reserved for VIP</font><br>");
}
print("<b>" . Lang::T("LAST_CHECKED") . ": </b>" . date("d-m-Y H:i:s", TimeDate::utc_to_tz_time($torr["last_action"])) . "<br><br>");
if ($_SESSION['loggedin']) {
    echo Ratings::ratingtor($data['id']);
}
// Scrape External Torrents
if ($torr["external"] == 'yes') {
    echo $data['scraper'];
}
?>
</div>

<div class="col">
    <?php
if (!Config::TT()['FORCETHANKS']) {
    // Magnet
    if ($torr["external"] == 'yes') {
        print("<a href=\"" . URLROOT . "/download?id=$torr[id]&amp;name=" . rawurlencode($torr["filename"]) . "\"><button type='button' class='btn btn-sm ttbtn'>" . Lang::T("DOWNLOAD_TORRENT") . "</button></a>");
        print("&nbsp;<a href=\"magnet:?xt=urn:btih:" . $torr["info_hash"] . "&dn=" . $torr["filename"] . "&tr=udp://tracker.openbittorrent.com&tr=udp://tracker.publicbt.com\"><button type='button' class='btn btn-sm ttbtn'>Magnet Download</button></a><br>");
    } else {
        print("<a href=\"" . URLROOT . "/download?id=$torr[id]&amp;name=" . rawurlencode($torr["filename"]) . "\"><button type='button' class='btn btn-sm ttbtn'>" . Lang::T("DOWNLOAD_TORRENT") . "</button></a>");
        print("&nbsp;<a href=\"magnet:?xt=urn:btih:" . $torr["info_hash"] . "&dn=" . $torr["filename"] . "&tr=" . URLROOT . "/announce.php?passkey=" . $_SESSION["passkey"] . "\"><button type='button' class='btn btn-sm ttbtn'>Magnet Download</button></a><br>");
    }
} else {
    print("<a href=\"" . URLROOT . "/download?id=$torr[id]&amp;name=" . rawurlencode($torr["filename"]) . "\"><button type='button' class='btn btn-sm ttbtn'>" . Lang::T("DOWNLOAD_TORRENT") . "</button></a>");
    $data = DB::run("SELECT user FROM thanks WHERE thanked = ? AND type = ? AND user = ?", [$torr['id'], 'torrent', $_SESSION['id']]);
    $like = $data->fetch(PDO::FETCH_ASSOC);
    if ($like) {
        // magnet
        if ($torr["external"] == 'yes') {
            if ($_SESSION["can_download"] == "yes") {
                // magnet
                print("&nbsp;<a href=\"magnet:?xt=urn:btih:" . $torr["info_hash"] . "&dn=" . $torr["filename"] . "&tr=" . URLROOT . "/announce.php?passkey=" . $_SESSION["passkey"] . "\"><button type='button' class='btn btn-sm ttbtn'>Magnet Download</button></a><br>");
            } else {
                echo '<br>';
            }
        } else {
            if ($_SESSION["can_download"] == "yes") {
                // magnet button
                print("&nbsp;<a href=\"magnet:?xt=urn:btih:" . $torr["info_hash"] . "&dn=" . $torr["filename"] . "&tr=udp://tracker.openbittorrent.com&tr=udp://tracker.publicbt.com\"><button type='button' class='btn btn-sm ttbtn'>Magnet Download</button></a><br>");
            } else {
                echo '<br>';
            }
        }
    } else {
        if ($_SESSION["id"] != $torr["owner"]) {
            print("<a href='" . URLROOT . "/likes/thanks?id=$torr[id]&type=torrent'><button  class='btn btn-sm ttbtn'>Thanks</button></a><br>");
        } else {
            if ($torr["external"] == 'yes') {
                // magnet
                print("&nbsp;<a href=\"magnet:?xt=urn:btih:" . $torr["info_hash"] . "&dn=" . $torr["filename"] . "&tr=" . URLROOT . "/announce.php?passkey=" . $_SESSION["passkey"] . "\"><button type='button' class='btn btn-sm ttbtn'>Magnet Download</button></a><br>");
            } else {
                print("&nbsp;<a href=\"magnet:?xt=urn:btih:" . $torr["info_hash"] . "&dn=" . $torr["filename"] . "&tr=udp://tracker.openbittorrent.com&tr=udp://tracker.publicbt.com\"><button type='button' class='btn btn-sm ttbtn'>Magnet Download</button></a><br>");
            }
        }
    }
}

?>
    <b><?php echo Lang::T("HEALTH"); ?>: </b>
    <img src='<?php echo URLROOT; ?>/assets/images/health/health_<?php echo health($torr['leechers'], $torr['seeders']); ?>.gif' alt='' /><br>
    <b><?php echo Lang::T("SEEDS"); ?>: </b><font color='green'><?php echo number_format($torr["seeders"]); ?></font><br />
    <b><?php echo Lang::T("LEECHERS"); ?>: </b><font color='#ff0000'><?php echo number_format($torr["leechers"]); ?></font><br /><?php
if ($torr["external"] != 'yes') {?>
        <b><?php echo Lang::T("SPEED"); ?>: </b><br /><?php
}?>
    <b><?php echo Lang::T("COMPLETED"); ?>:</b><?php echo number_format($torr["times_completed"]); ?>&nbsp;<?php
if ($torr["external"] != "yes" && $torr["times_completed"] > 0) {?>
        <a href='<?php echo URLROOT; ?>/completed?id=<?php echo $data['id']; ?>'><?php echo Lang::T("WHOS_COMPLETED"); ?></a>]<br><?php
}
if ($torr["seeders"] <= 1) {?>
        [<a href='<?php echo URLROOT ?>/torrent/reseed?id=<?php echo $torr['id']; ?>'><?php echo Lang::T("REQUEST_A_RE_SEED"); ?></a>]<br><?php
}
if ($torr["external"] != 'yes' && $torr["freeleech"] == '1') {?>
        <b><?php echo Lang::T("FREE_LEECH"); ?>: </b><font color='#ff0000'><?php echo Lang::T("FREE_LEECH_MSG"); ?></font><br><?php
}?>
    <b><?php echo Lang::T("LAST_CHECKED"); ?>: </b><?php echo date("d-m-Y H:i:s", TimeDate::utc_to_tz_time($torr["last_action"])); ?><br>
<?php
    if ($_SESSION["edit_users"] == "yes") {?>
    <a href="<?php echo URLROOT; ?>/torrent?id=<?php echo $torr['id'] ?>&bump=1"><button type='button' class='btn btn-sm ttbtn'><?php echo Lang::T("Bump") ?></button></a><?php
}
Bookmarks::select($torr['id']);
?>
    <a href="<?php echo URLROOT; ?>/report/torrent?torrent=<?php echo $torr['id']; ?>"><button type='button' class='btn btn-sm ttbtn'><?php echo Lang::T("REPORT_TORRENT") ?></button></a>&nbsp;
    <?php if ($_SESSION["edit_torrents"] == "yes") {
    echo "<a href='" . URLROOT . "/torrent/edit?id=$torr[id]&amp;returnto=" . urlencode($_SERVER["REQUEST_URI"]) . "'><button type='button' class='btn btn-sm ttbtn'><b>" . Lang::T("EDIT_TORRENT") . "</b></button></a>&nbsp;";
}
if ($_SESSION["edit_users"] == "yes") {?>
        <a href="<?php echo URLROOT; ?>/snatched?tid=<?php echo $torr['id']; ?>"><button type='button' class='btn btn-sm ttbtn'><?php echo Lang::T("SNATCHLIST") ?></button></a><?php
}
if ($_SESSION["delete_torrents"] == "yes") {?>
        <a href="<?php echo URLROOT; ?>/torrent/delete?id=<?php echo $torr['id']; ?>"><button type='button' class='btn btn-sm ttbtn'><?php echo Lang::T("Delete") ?></button></a><?php
}?>

</div>

</div>
</div>

<?php

if(!empty($torr["tmdb"]) && in_array($torr["cat_parent"], SerieCats)) {
    $id_tmdb = TMDBS::getId($torr["tmdb"]);
    $total = DB::run("SELECT count(*) FROM tmdb WHERE id_tmdb = ? AND type = ?", [$id_tmdb, 'show'])->fetchColumn();
    if($total > 0) {
        TMDBS::getSerie($id_tmdb);
    }
} elseif(!empty($torr["tmdb"]) && in_array($torr["cat_parent"], MovieCats)) {
    $id_tmdb = TMDBS::getId($torr["tmdb"]);
    $total = DB::run("SELECT count(*) FROM tmdb WHERE id_tmdb = ? AND type = ?", [$id_tmdb, 'movie'])->fetchColumn();
    if($total > 0) {
        TMDBS::getFilm($id_tmdb);
    }
} else {
?>
    <div class="container">
    <div class="row">
        <div class="col">
        <?php
        if (!empty($torr["tube"])) {
            print("<embed src='" . str_replace("watch?v=", "v/", htmlspecialchars($torr["tube"])) . "' type=\"application/x-shockwave-flash\" width=\"400\" height=\"310\"></embed>");
        } ?>
        </div>
        <div class="col">
        <?php
        if ($torr["image1"] != "") {
            $img1 = "&nbsp;&nbsp;<img src='" . data_uri(UPLOADDIR . "/images/" . $torr["image1"], $torr['image1']) . "' height='300' width='200' border='0' alt='' />";
        }
        print("" . $img1 . ""); ?>
        </div>
        <div class="col">
        <?php
        if ($torr["image2"] != "") {
            $img2 = "<img src='" . data_uri(UPLOADDIR . "/images/" . $torr["image2"], $torr['image2']) . "' height='300' width='200' border='0' alt='' />";
        }
        print("" . $img2 . ""); ?>
        </div>
    </div>
    </div> <?php
}

//DISPLAY NFO BLOCK
if ($torr["nfo"] == "yes") {
    $nfofilelocation = UPLOADDIR . "/nfos/$torr[id].nfo";
    $filegetcontents = file_get_contents($nfofilelocation);
    $nfo = $filegetcontents;
    if ($nfo) {
        $nfo = Helper::my_nfo_translate($nfo);
        echo "<br /><br /><b>NFO:</b><br />";
        print("<div><textarea class='nfo' style='width:98%;height:100%;' rows='20' cols='20' readonly='readonly'>" . stripslashes($nfo) . "</textarea></div>");
    } else {
        print(Lang::T("ERROR") . " reading .nfo file!");
    }
}
endforeach;

  // Similar Torrents mod
  $shortname = CutName(htmlspecialchars($torr["name"]), 50);
  $searchname = substr($torr['name'], 0, 8);
  $query1 = str_replace(" ", ".", sqlesc("%" . $searchname . "%"));
  $catid = str_replace(".", " ", sqlesc("%" . $data['category'] . "%"));
  $r = DB::run("SELECT torrents.id,  torrents.name,  torrents.size,  torrents.added,  torrents.seeders,  torrents.leechers,  torrents.category, categories.image 
           FROM torrents 
         LEFT JOIN categories ON torrents.category = categories.id 
  WHERE (torrents.name LIKE {$query1}) 
  OR (torrents.category LIKE {$catid}) 
  LIMIT 10");

if ($r->rowCount() > 0) {
    ?>
    <br><center><b>Similar Torrents</b></center>
    <div class="table-responsive"><table class="table table-striped"><thead><tr>
    <th>Type</th>
    <th>Name</th>
    <th>Size</th>
    <th>Added</th>
    <th>S</th>
    <th>L</th>
    </tr></thead><tbody> <?php
while ($a = $r->fetch(PDO::FETCH_ASSOC)) {
$cat = "<img class=glossy src=\"".URLROOT."/assets/images/categories/$a[image]\" alt=\"$a[name]\" title=\"$row[cat_parent] : $row[cat_name]\"\>";
$name = $a["name"];
echo" <tr>
    <td>$cat</td>
    <td><a title=".$a["name"]." href=torrents-details.php?id=" . $a["id"] . "&hit=1><b>" . CutName(htmlspecialchars($a["name"]), $char1) . "</b><br/></a></td>
    <td>" . mksize($a['size']) . "</td>
    <td>$a[added]</td>
    <td><span style='color:Chartreuse'>$a[seeders]</span></td>
    <td><span style='color:red'>$a[leechers]</span></td>";
}
echo "<tr>";
echo "</tbody></table></div>";

}
      else {
          print(Lang::T("NO_SIMILAR_TORRENT_FOUND"));
      }

