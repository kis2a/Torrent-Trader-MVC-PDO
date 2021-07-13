<?php include APPROOT.'/views/message/messagenavbar.php'; ?><br>
<div class="row justify-content-md-center">
    <div class="col-8 border ttborder">
        <form name="form" action="update?id=<?php echo $data['id']; ?>" method="post"><br>
        <center>
        <label for="receiver">To</label>
        <input type="text" name="receiver" value="<?php echo $data['username']; ?>" id="receiver"><br>
        <br>
        <label for="template">Template:</label>&nbsp;
            <select name="template">
            <?php  Helper::echotemplates(); ?>
            </select>
            <br><br>
        <label for="name">Subject</label>
        <input type="text" name="subject" placeholder="Subject" value="<?php echo $data['subject']; ?>" id="subject"><br><br>
        <center>
        </div>
        </div><br>
        <?php print textbbcode("form", "msg", $data['msg']); ?>
        <center><br>
        <button type="submit" class="btn-sm ttbtn" name="Update" value="Update">Update</button><br><br>
       </form>
        </center>