<form method='post' action='<?php echo  URLROOT ?>/admincategories/delete?id=<?php echo $data['id'] ?>&amp;sure=1'>
<div class="row justify-content-md-center">
<div class="col-md-6">
<b>Category ID to move all Torrents To: </b>
<input type='text' name='newcat' /> (Cat ID)<br>
    <div class="text-center">
        <input type='submit' class="btn btn-sm ttbtn" value='<?php echo Lang::T("SUBMIT") ?>' />
    </div>
</div>
</div>
</form>