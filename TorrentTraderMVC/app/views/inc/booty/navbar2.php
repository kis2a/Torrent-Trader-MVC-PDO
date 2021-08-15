<div class="d-flex flex-row-reverse">
<?php if (isset($_SESSION['id'])) { 

if ($_SESSION["uploaded"] > 0 && $_SESSION["downloaded"] == 0) {
  $userratio = 'Inf.';
} elseif ($_SESSION["downloaded"] > 0) {
  $userratio = number_format($_SESSION["uploaded"] / $_SESSION["downloaded"], 2);
} else {
  $userratio = '---';
}
$userdownloaded = mksize($_SESSION["downloaded"]);
$useruploaded = mksize($_SESSION["uploaded"]);
$privacylevel = Lang::T($_SESSION["privacy"]); ?>

<a href="<?php echo URLROOT; ?>/logout"><i class="fa fa-sign-out" style="color:#FFFFFF;"></i></a>&nbsp;&nbsp; <?php

if ($_SESSION["control_panel"] == "yes") { ?>
    <a href="<?php echo URLROOT; ?>/admincp"><i class="fa fa-address-book" style="color:#FFFFFF;"></i></a>&nbsp;&nbsp; <?php
} 

if ($_SESSION["view_torrents"] == "yes") {
    $activeseed = get_row_count("peers", "WHERE userid = '$_SESSION[id]' AND seeder = 'yes'");
    $activeleech = get_row_count("peers", "WHERE userid = '$_SESSION[id]' AND seeder = 'no'");
    $stmt = DB::run("SELECT connectable FROM peers WHERE userid=? LIMIT 1", [$_SESSION['id']]);
    $connect = $stmt->fetchColumn();
    if ($connect == 'yes') {
       $connectable = "<b><font color='#FFFFFF'>Y</font></b>";
    } elseif ($connect == 'no') {
       $connectable = "<b><font color='#FFFFFF'>X</font></b>";
    } else {
       $connectable = "<b><font color='#FFFFFF'>?</font></b>";
    } ?>
    <a href="#"><i class="fa fa-refresh fa-spin fa-3x fa-fw" style="color:#FFFFFF;font-size:13px"></i></a>&nbsp;<?php echo $connectable ?>&nbsp;&nbsp;
    <font color='#FFFFFF'><?php echo $activeleech ?></font>&nbsp;&nbsp;<a href="javascript:popout(0) "onclick="window.open('<?php echo  URLROOT ?>/peers/popoutleech?id=<?php echo  $_SESSION['id'] ?>','Leeching','width=350,height=350,scrollbars=yes')"><i class="fa fa-arrow-circle-down" style="color:#FFFFFF;"></i></a>&nbsp;&nbsp;
    <font color='#FFFFFF'><?php echo $activeseed ?></font>&nbsp;&nbsp;<a href="javascript:popout(0) "onclick="window.open('<?php echo  URLROOT ?>/peers/popoutseed?id=<?php echo  $_SESSION['id'] ?>','Seeding','width=350,height=350,scrollbars=yes')"><i class="fa fa-arrow-circle-up" style="color:#FFFFFF;"></i></a>&nbsp;&nbsp;
  <?php
} ?>

<font color='#FFFFFF'><?php echo $unreadmail ?></font>&nbsp;&nbsp;<a href="<?php echo URLROOT ?>/messages?type=inbox"><i class="fa fa-envelope" style="color:#FFFFFF;"></i></a>&nbsp;&nbsp;
<font color='#FFFFFF'><?php echo $_SESSION['seedbonus'] ?></font>&nbsp;&nbsp;<a href="<?php echo URLROOT ?>/bonus"><i class="fa fa fa-smile-o" style="color:#FFFFFF;"></i></a>&nbsp;&nbsp;
<font color='#FFFFFF'><?php echo $userratio ?></font>&nbsp;&nbsp;<a href="#"><i class="fa fa-cog" style="color:#FFFFFF;"></i></a>&nbsp;&nbsp;
<font color='#FFFFFF'><?php echo $useruploaded ?></font>&nbsp;&nbsp;<a href="#"><i class="fa fa-upload" style="color:#FFFFFF;"></i></a>&nbsp;&nbsp;
<font color='#FFFFFF'><?php echo $userdownloaded ?></font>&nbsp;&nbsp;<a href="#"><i class="fa fa-download" style="color:#FFFFFF;"></i></a>&nbsp;&nbsp;
<a href="<?php echo URLROOT ?>/profile?id=<?php echo $_SESSION['id'] ?>"><b><?php echo Users::coloredname($_SESSION['username']); ?></b></a>&nbsp;&nbsp;
<font color='#FFFFFF'><b>Hello</b>&nbsp;&nbsp;</font>

<?php } else { ?>
<a href="<?php echo URLROOT; ?>/login"><font color='#FFFFFF'><b>Login</b></font></a>
 <?php } ?>

</div><br>