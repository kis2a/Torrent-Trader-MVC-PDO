<?php usermenu($_SESSION['id']);
include APPROOT.'/views/message/messagenavbar.php';?>

<div class="row justify-content-center">
<div class="col-6">
<div class="jumbotron">
<center><br>
<b><?php echo  Lang::T("Overview"); ?></b><br>
    <?php echo  Lang::T("INBOX"); ?> :
    &nbsp;<a href="<?php echo URLROOT; ?>/messages?type=inbox"><font color=white><?php echo $data['inbox']; ?></font></a>
    <br>
    <?php echo  Lang::T("Unread"); ?> :
    &nbsp;<a href="<?php echo URLROOT; ?>/messages?type=inbox"><font color=orange><?php echo $data['unread']; ?></font></a>
    <br>
    <?php echo  Lang::T("OUTBOX"); ?> :
    &nbsp;<a href="<?php echo URLROOT; ?>/messages?type=outbox"><?php echo $data['outbox'], Lang::N("", $data['outbox']); ?></a>
    <br>
    <?php echo  Lang::T("DRAFT"); ?> :
    &nbsp;<a href="<?php echo URLROOT; ?>/messages?type=draft"><?php echo $data['draft'], Lang::N("", $data['draft']); ?></a>
    <br>
    <?php echo  Lang::T("TEMPLATES"); ?> :
    &nbsp;<a href="<?php echo URLROOT; ?>/messages?type=templates"><?php echo $data['template'], Lang::N("", $data['template']); ?></a>
<br></center>
</div>
</div>
</div>