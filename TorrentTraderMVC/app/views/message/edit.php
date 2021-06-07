<?php include APPROOT.'/views/message/messagenavbar.php'; ?>
        <form name="form" action="update?id=<?php echo $data['id']; ?>" method="post">
        <label for="receiver">To</label>
        <input type="text" name="receiver" value="<?php echo $data['username']; ?>" id="receiver"><br>

        <label for="template">Template:</label>&nbsp;
            <select name="template">
            <?php  Helper::echotemplates(); ?>
            </select><br>

        <label for="name">Subject</label>
        <input type="text" name="subject" placeholder="Subject" value="<?php echo $data['subject']; ?>" id="subject">
        <?php print textbbcode("form", "msg", $data['msg']); ?>
        <center><input type="submit" value="Update">
        <button type="submit" class="btn-sm btn-warning" name="Update" value="Update">Update</button>&nbsp;
       </form>
        </center>