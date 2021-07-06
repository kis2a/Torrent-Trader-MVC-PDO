<?php usermenu($data['id']); ?>
<div class="row justify-content-md-center">
    <div class="col-8 border border-warning">
<div class="form-group">
<b><?php echo Lang::T("AVATAR_UPLOAD") ?>:</b> &nbsp;<br><br>

<form action='<?php echo URLROOT ?>/account/avatar?id=<?php echo $data['id'] ?>' method='post' enctype='multipart/form-data'>
<input type='file' name='upfile'><br><br>
<button type='submit' class='btn btn-sm btn-warning' value='<?php echo Lang::T("SUBMIT") ?>'>Submit</button>
</form>
</div>
</div>
</div>