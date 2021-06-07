<?php
if ($_SESSION["edit_torrents"] == "yes") {
    ?>
    <center><a href='<?php echo URLROOT ?>/nfo/edit?id=<?php echo $data['id'] ?>'><?php echo Lang::T("NFO_EDIT") ?></a><center>
    <?php
} ?>
<textarea class='nfo' style='width:98%;height:100%;' rows='50' cols='20' readonly='readonly'>
<?php echo stripslashes($data['nfo']); ?></textarea>