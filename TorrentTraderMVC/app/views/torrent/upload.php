<div class="row justify-content-md-center">
    <div class="col-md-6 border ttborder">
	<center><b><?php echo stripslashes("Upload Rules"); ?></b><br>
	<b><?php echo stripslashes(Config::TT()['UPLOADRULES']); ?></b></center>
	</div>
</div>
<br>
<div class="row justify-content-md-center">
<div class="col-md-6">
<form name="upload" enctype="multipart/form-data" action="<?php echo URLROOT; ?>/upload/submit" method="post">
<input type="hidden" name="takeupload" value="yes" />
<?php echo Lang::T("ANNOUNCE_URL"); ?>: 
<?php while (list($key, $value) = thisEach($data['announce_urls'])) { ?>
    <b><?php echo $value; ?></b><br />
<?php } ?>
<?php if (Config::TT()['ALLOWEXTERNAL']) { ?>
    <br /><p class="text-center"><b><?php echo Lang::T("THIS_SITE_ACCEPTS_EXTERNAL"); ?></b></p>
<?php }  ?>

<div class="mb-6 row">
  <label for="torrent" class="col-sm-2 col-form-label"><?php echo Lang::T("TORRENT_FILE"); ?>:</label>
  <div class="col-sm-6">
  <input class="form-control" type="file" name="torrent" value='<?php echo $_FILES['torrent']['name']; ?>'>
  </div>
</div><br>

<div class="mb-6 row">
    <label for="nfo" class="col-sm-2 col-form-label"><?php echo Lang::T("NFO"); ?>:</label>
    <div class="col-sm-6">
    <input class="form-control" type="file" name="nfo" value='<?php echo $_FILES['nfo']['name']; ?>'>
    </div>
</div><br>

<div class="mb-6 row">
    <label for="name" class="col-sm-2 col-form-label"><?php echo Lang::T("TORRENT_NAME"); ?>:</label>
    <div class="col-sm-6">
	<input class="form-control" type='text' name='name'><?php echo Lang::T("THIS_WILL_BE_TAKEN_TORRENT"); ?>
    </div>
</div><br>

<?php 
if (Config::TT()['IMDB1']) { ?>
<div class="mb-6 row">
    <label for="imdb" class="col-sm-2 col-form-label"><a href="https://www.imdb.com/?ref_=nv_home" target='_blank'><img border='0' src='assets/images/imdb.png' width='30' height='30' title='Click here to go to IMDB'></a></label>
    <div class="col-sm-8">
	<input class="form-control" type='text' name='imdb'>Example https://www.imdb.com/title/tt1799527/
    </div>
</div><br><?php
}

if (Config::TT()['YOU_TUBE']) { ?>
<div class="mb-6 row">
	<label for="imdb" class="col-sm-2 col-form-label"><a href=\"http://www.youtube.com\" target='_blank'><img border='0' src='assets/images/youtube.png' width='30' height='30' title='Click here to go to Youtube'></a></label>
	<div class="col-sm-8">
	<input class="form-control" type='text' name='tube'>Example https://www.youtube.com/watch?v=aYzVrjB-CWs
	</div>
</div><br><?php
} ?>

<div class="mb-6 row">
    <label for="image0" class="col-sm-2 col-form-label"><?php echo Lang::T("IMAGE"); ?> 1:</label>
    <div class="col-sm-6">
    <input class="form-control" type="file" name="image0" value='<?php echo $_FILES['nfo']['name']; ?>'>
    </div>
</div><br>

<div class="mb-6 row">
    <label for="image1" class="col-sm-2 col-form-label"><?php echo Lang::T("IMAGE"); ?> 2:</label>
    <div class="col-sm-6">
    <input class="form-control" type="file" name="image1">
    <?php
    echo Lang::T("MAX_FILE_SIZE"); ?>: <?php echo mksize(IMAGEMAXFILESIZE);?><br><?php
    echo Lang::T("ACCEPTED_FORMATS"); ?>: <?php echo implode(", ", array_unique(ALLOWEDIMAGETYPES));
    ?>
    </div>
</div><br><?php

$category = "<select name=\"type\">\n<option value=\"0\">" . Lang::T("CHOOSE_ONE") . "</option>\n";
$cats = genrelist();
foreach ($cats as $row) {
    $category .= "<option value=\"" . $row["id"] . "\">" . htmlspecialchars($row["parent_cat"]) . ": " . htmlspecialchars($row["name"]) . "</option>\n";
}
$category .= "</select>\n"; ?>
<div class="mb-6 row">
    <label for="type" class="col-sm-2"><?php echo Lang::T("CATEGORY") ?></label>
    <div class="col-sm-6">
    <?php echo $category ?>
    </div>
</div><br><?php

$language = Lang::select(); ?>
<div class="mb-6 row">
    <label for="type" class="col-sm-2"><?php echo Lang::T("LANGUAGE") ?></label>
    <div class="col-sm-6">
    <?php echo  $language ?>
    </div>
</div><br><?php

if ($_SESSION["class"] > _VIP) { ?>
<div class="mb-6 row">
	<label for="free" class="col-sm-2 form-check-label">Freeleech:</label>
	<div class="col-sm-7">
	<input class="form-check-input" type='checkbox' name='free' value=1>&nbsp;Check this box if you want the torrent freeleech.
	</div>
</div><br><?php
}

if ($_SESSION["class"] > _VIP) { ?>
<div class="mb-6 row">
	<label for="vip" class="col-sm-2 form-check-label">VIP:</label>
	<div class="col-sm-7">
	<input class="form-check-input" type='checkbox' name='vip'  value='yes'>&nbsp;Check this box if you want the torrent VIP only.
	</div>
</div> <br><?php
}

if (Config::TT()['ANONYMOUSUPLOAD']) { ?>
<div class="mb-6 row">
      <label for="anonycheck" class="col-sm-2 form-check-label">Anon :</label>
      <div class="col-sm-7">
	  <input class="form-check-input" type="checkbox" name="anonycheck" value='yes'>&nbsp;<?php echo Lang::T("UPLOAD_ANONY"); ?>
      </div>
</div><br><?php
} ?>

</div>
</div>
<div class="row justify-content-md-center">
<div class="col-md-8">
<p class="text-center"><?php echo Lang::T("DESCRIPTION"); ?></p><?php
print textbbcode("upload", "descr", "$descr"); ?>
<br><center><input type="submit" class="btn btn-sm ttbtn" value="<?php echo Lang::T("UPLOAD_TORRENT"); ?>" /><br />
<i><?php echo Lang::T("CLICK_ONCE_IMAGE"); ?></i>
</center>
</form>

</div>
</div>