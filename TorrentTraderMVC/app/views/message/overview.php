<?php usermenu($_SESSION['id']);
include APPROOT.'/views/message/messagenavbar.php';?>

<div class="row justify-content-center">
<div class="col-6">
<div class="jumbotron">
<center>
<b><?php echo  Lang::T("Overview"); ?></b><br>

    <a href="<?php echo URLROOT; ?>/messages/inbox"><?php echo  Lang::T("INBOX"); ?> :</a>
    &nbsp;[<a href="<?php echo URLROOT; ?>/messages/inbox"><font color=white><?php echo $data['inbox']; ?></font></a>]
    <br>
    <a href="<?php echo URLROOT; ?>/messages/inbox"><?php echo  Lang::T("Unread"); ?> :</a>
    &nbsp;[<a href="<?php echo URLROOT; ?>/messages/inbox"><font color=orange><?php echo $data['unread']; ?></font></a>]
    <br>
    <a href='<?php echo URLROOT; ?>/messages/outbox'><?php echo  Lang::T("OUTBOX"); ?> :</a>
    &nbsp;<?php echo $data['outbox'], Lang::N("", $data['outbox']); ?>
    <br>
    <a href="<?php echo URLROOT; ?>/messages/draft"><?php echo  Lang::T("DRAFT"); ?> :</a>
    &nbsp;<?php echo $data['draft'], Lang::N("", $data['draft']); ?>
    <br>
    <a href="<?php echo URLROOT; ?>/messages/templates">-<?php echo  Lang::T("TEMPLATES"); ?> :</a>
    &nbsp;<?php echo $data['template'], Lang::N("", $data['template']); ?>
    <br>
    </center>
</div></div></div>