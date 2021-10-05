<?php
// get image embeded image
function data_uri($file, $mime)
{
    $contents = file_get_contents($file);
    $base64 = base64_encode($contents);
    return ('data:' . $mime . ';base64,' . $base64);
}

// Function To Count Database Table
function get_row_count($table, $suffix = "")
{
    global $pdo;
    $suffix = !empty($suffix) ? ' ' . $suffix : '';
    $row = DB::run("SELECT COUNT(*) FROM $table $suffix")->fetchColumn();
    return $row;
}

// Returns The Size
function mksize($s, $precision = 2)
{
    $suf = array("B", "kB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB");

    for ($i = 1, $x = 0; $i <= count($suf); $i++, $x++) {
        if ($s < pow(1024, $i) || $i == count($suf)) // Change 1024 to 1000 if you want 0.98GB instead of 1,0000MB
        {
            return number_format($s / pow(1024, $x), $precision) . " " . $suf[$x];
        }
    }
}

// Shorten Name
function CutName($vTxt, $Car)
{
    if (strlen($vTxt) > $Car) {
        return substr($vTxt, 0, $Car) . "...";
    }
    return $vTxt;
}

// Returns a numeric conversion according to a string
function strtobytes($str)
{
    $str = trim($str);
    if (!preg_match('!^([\d\.]+)\s*(\w\w)?$!', $str, $matches)) {
        return 0;
    }

    $num = $matches[1];
    $suffix = strtolower($matches[2]);
    switch ($suffix) {
        case "tb": // TeraByte
            return $num * 1099511627776;
        case "gb": // GigaByte
            return $num * 1073741824;
        case "mb": // MegaByte
            return $num * 1048576;
        case "kb": // KiloByte
            return $num * 1024;
        case "b": // Byte
            default:
            return $num;
    }
}

// Profile Navbar
function usermenu($id)
{
    ?>
    <a href='<?php echo URLROOT; ?>/profile?id=<?php echo $id; ?>'><button type="button" class="btn btn-sm ttbtn">Profile</button></a>
    <?php if ($_SESSION["id"] == $id or $_SESSION["class"] > _UPLOADER) {?>
    <a href='<?php echo URLROOT; ?>/profile/edit?id=<?php echo $id; ?>'><button type="button" class="btn btn-sm ttbtn">Edit</button></a>&nbsp;
    <?php }?>
    <?php if ($_SESSION["id"] == $id) {?>
    <a href='<?php echo URLROOT; ?>/account/changepw?id=<?php echo $id; ?>'><button type="button" class="btn btn-sm ttbtn">Password</button></a>
    <a href='<?php echo URLROOT; ?>/account/email?id=<?php echo $id; ?>'><button type="button" class="btn btn-sm ttbtn">Email</button></a>
    <a href='<?php echo URLROOT; ?>/messages/overview'><button type="button" class="btn btn-sm ttbtn">Messages</button></a>
    <a href='<?php echo URLROOT; ?>/bonus'><button type="button" class="btn btn-sm ttbtn">Seed Bonus</button></a>
    <?php }?>
    <?php if ($_SESSION["view_users"]) {?>
    <a href='<?php echo URLROOT; ?>/friends?id=<?php echo $id; ?>'><button type="button" class="btn btn-sm ttbtn">Friends</button></a>
    <?php }?>
    <?php if ($_SESSION["view_torrents"]) {?>
    <a href='<?php echo URLROOT; ?>/peers/seeding?id=<?php echo $id; ?>'><button type="button" class="btn btn-sm ttbtn"><?php echo Lang::T("SEEDING"); ?></button></a>
    <a href='<?php echo URLROOT; ?>/peers/uploaded?id=<?php echo $id; ?>'><button type="button" class="btn btn-sm ttbtn"><?php echo Lang::T("UPLOADED"); ?></button></a>
    <?php }?>
    <?php if ($_SESSION["class"] > _UPLOADER) {?>
    <a href='<?php echo URLROOT; ?>/warning?id=<?php echo $id; ?>'><button type="button" class="btn btn-sm ttbtn">Warn</button></a>
    <a href='<?php echo URLROOT; ?>/profile/admin?id=<?php echo $id; ?>'><button type="button" class="btn btn-sm ttbtn">Admin</button></a>
	<?php }?>
    <br><br><?php
}

// Torrent Navbar
function torrentmenu($id, $external = 'no')
{
    ?>
    <a href='<?php echo URLROOT; ?>/torrent?id=<?php echo $id; ?>'><button type="button" class="btn btn-sm ttbtn">Back</button></a>
    <?php if ($_SESSION["id"] == $id or $_SESSION["edit_torrents"] == 'yes') {?>
    <a href='<?php echo URLROOT; ?>/torrent/edit?id=<?php echo $id; ?>'><button type="button" class="btn btn-sm ttbtn">Edit</button></a>
    <?php }?>
    <a href='<?php echo URLROOT; ?>/comments?type=torrent&amp;id=<?php echo $id; ?>'><button type="button" class="btn btn-sm ttbtn">Comments</button></a>
    <a href='<?php echo URLROOT; ?>/torrent/torrentfilelist?id=<?php echo $id; ?>'><button type="button" class="btn btn-sm ttbtn">Files</button></a>
    <?php if ($external != 'yes') {?>
    <a href='<?php echo URLROOT; ?>/peers/peerlist?id=<?php echo $id; ?>'><button type="button" class="btn btn-sm ttbtn">Peers</button></a>
    <?php }
    if ($external == 'yes') {?>
     <a href='<?php echo URLROOT; ?>/torrent/torrenttrackerlist?id=<?php echo $id; ?>'><button type="button" class="btn btn-sm ttbtn">Trackers</button></a>
    <?php }?>
    <br><br>
    <?php
}

// Upload Image - torrent/edit
function uploadimage($x, $imgname, $tid)
{
    $imagesdir = UPLOADDIR."/images";
    $allowed_types = ALLOWEDIMAGETYPES;
    if (!($_FILES["image$x"]["name"] == "")) {
        if ($imgname != "") {
            $img = "$imagesdir/$imgname";
            $del = unlink($img);
        }
        $y = $x + 1;
        $im = getimagesize($_FILES["image$x"]["tmp_name"]);
        if (!$im[2]) {
            Redirect::autolink(URLROOT . $_SERVER["HTTP_REFERER"], "Invalid Image $y.");
        }
        if (!array_key_exists($im['mime'], $allowed_types)) {
            Redirect::autolink(URLROOT . $_SERVER["HTTP_REFERER"], Lang::T("INVALID_FILETYPE_IMAGE"));
        }
        if ($_FILES["image$x"]["size"] > IMAGEMAXFILESIZE) {
            Redirect::autolink(URLROOT . $_SERVER["HTTP_REFERER"], sprintf(Lang::T("INVAILD_FILE_SIZE_IMAGE"), $y));
        }
        $uploaddir = "$imagesdir/";
        $ifilename = $tid . $x . $allowed_types[$im['mime']];
        $copy = copy($_FILES["image$x"]["tmp_name"], $uploaddir . $ifilename);
        if (!$copy) {
            Redirect::autolink(URLROOT . $_SERVER["HTTP_REFERER"], sprintf(Lang::T("ERROR_UPLOADING_IMAGE"), $y));
        }
        return $ifilename;
    }
}

// Escape (Not Needed in Prepared Statements)
function sqlesc($x)
{
    if (!is_numeric($x)) {
        $x = "'" . $x . "'";
    }
    return $x;
}