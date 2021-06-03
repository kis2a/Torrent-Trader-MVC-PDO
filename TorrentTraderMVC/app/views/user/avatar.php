<?php usermenu($data['id']); ?>
<b><?php echo Lang::T("AVATAR_UPLOAD") ?>:</b> &nbsp;
<form action='<?php echo URLROOT ?>/account/avatar?id=<?php echo $data['id'] ?>' method='post' enctype='multipart/form-data'>
<input type='file' name='upfile'>
<button type='submit' class='btn btn-sm btn-primary' value='<?php echo Lang::T("SUBMIT") ?>'>Submit</button>
</form><br />