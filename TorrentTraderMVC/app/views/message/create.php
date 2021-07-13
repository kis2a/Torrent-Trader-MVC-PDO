<?php include APPROOT.'/views/message/messagenavbar.php'; ?><br>
<form name="form" action="<?php echo URLROOT; ?>/messages/submit" method="post">
<center>
    <label for="reciever">Reciever:</label>&nbsp;
    <input type="text" id="search-box" name="reciever" placeholder="User Name" />
    <div id="suggesstion-box"></div><br>
    
    <label for="template">Template:</label>&nbsp;
    <select name="template">
    <?php  Helper::echotemplates(); ?>
    </select><br>
    
    <label for="subject">Subject:</label>&nbsp;
    <input type="text" name="subject" size="50" placeholder="Subject" id="subject"><br>
    </center>
    <?php print textbbcode("form", "body", "$body");?><br>
<center>
    <button type="submit" class="btn-sm ttbtn" name="Update" value="create">Create</button>&nbsp;
    <label>Save Copy In Outbox</label>
    <input type="checkbox" name="save" checked='Checked'>&nbsp;
    <button type="submit" class="btn btn-sm ttbtn" name="Update" value="draft">Draft</button>
    <button type="submit" class="btn btn-sm ttbtn" name="Update" value="template">Template</button>
    </center>
    </form>
