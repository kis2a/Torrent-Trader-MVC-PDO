<?php $arr = $data['res']->fetch(PDO::FETCH_ASSOC); ?>

<a href='<?php echo URLROOT ?>/request'><button  class='btn btn-sm btn-warning'>All Request</button></a>&nbsp;
<a href='<?php echo URLROOT ?>/request?requestorid=<?php echo $_SESSION['id'] ?>'><button  class='btn btn-sm btn-warning'>View my requests</button></a>

<div class="row justify-content-md-center">
    <div class="col-6 border border-warning">
<form name="form" action="edit&id=<?php echo $arr['id']; ?>" method="post">
<input type="hidden" name="filledby" value="<?php echo $arr['filledby']; ?>" />
<label for="cat">Change Cat id:</label><br>
<input type="text" class="form-control" name="cat" value="<?php echo $arr['id']; ?>" id="cat"><br>
<label for="request">Request Tilte:</label><br>
<input type="text" class="form-control" name="request" value="<?php echo $arr['request']; ?>" id="request"><br>
<label for="descr">Description:</label><br>
<input type="text" class="form-control" name="descr" value="<?php echo $arr['descr']; ?>" id="descr"><br>
<label for="filled">Url To Torrent:</label><br>
<input type="text"  class="form-control" name="filled" value="<?php echo $arr['filled']; ?>" id="filled"><br>
<center>
<input type="submit"  class='btn btn-sm btn-warning' value="Update">
<center><br>
</form>
 </div>
</div>