<form method='post' action='<?php echo URLROOT ?>/admintorrentlang/edit?id=<?php echo $data['id'] ?>&amp;save=1'>
<?php
while ($arr = $data['res']->fetch(PDO::FETCH_LAZY)) {
    ?>
    <center>
    <table border='0' cellspacing='0' cellpadding='5'>
    <tr><td align='left'><b>Name: </b><input type='text' name='name' value="<?php echo $arr['name'] ?>"></td></tr>
    <tr><td align='left'><b>Sort: </b><input type='text' name='sort_index' value="<?php echo $arr['sort_index'] ?>"></td></tr>
    <tr><td align='left'><b>Image: </b><input type='text' name='image' value="<?php echo $arr['image'] ?>"> single filename</td></tr>
    </table>
    </center>
    <div class="text-center">
    <input type='submit' class='btn btn-sm ttbtn' value='<?php echo Lang::T("SUBMIT"); ?>' />
    </div>
    <?php
}
?>
</form>