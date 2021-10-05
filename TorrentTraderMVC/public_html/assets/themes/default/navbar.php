<nav class="navbar navbar-expand-lg">
  <div class="container-fluid">
    <a class="navbar-brand" href="<?php echo URLROOT; ?>"><font color='#FFFFFF'><b><?php echo Config::TT()['SITENAME']; ?></b><br><small><?php echo Config::TT()['_SITEDESC']; ?></small></font></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
    <i class="fa fa-bars fa-1x tticon"></i>
    </button>
    <div class="collapse navbar-collapse" id="navbarText">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">

      <?php if (isset($_SESSION['id'])) { ?>
      <li class="nav-item active">
      <a class="nav-link" href="<?php echo URLROOT ?>">Home <span class="sr-only">(current)</span></a>
      </li>

      <li class="nav-item">
      <?php
      $arr = DB::run("SELECT * FROM messages WHERE receiver=" . $_SESSION["id"] . " and unread='yes' AND location IN ('in','both')")->fetchAll();
      $unreadmail = count($arr);
      if ($unreadmail !== 0) {
        print("<a class='nav-link' href='" . URLROOT . "/messages?type=inbox'><b><font color='#FFFFFF'>$unreadmail</font> " . Lang::N("NEWPM", $unreadmail) . "</b></a>");
      } else {
        print("<a class='nav-link' href='" . URLROOT . "/messages/overview'>" . Lang::T("YOUR_MESSAGES") . "</a>");
      }
      ?>
      </li>

      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        Profile
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
		    <a class="dropdown-item" href="<?php echo URLROOT ?>/profile?id=<?php echo $_SESSION["id"]; ?>"><?php echo Lang::T("PROFILE"); ?></a>
		  	<a class="dropdown-item" href="<?php echo URLROOT ?>/peers/seeding?id=<?php echo $_SESSION['id']; ?>"><?php echo Lang::T("SEEDING"); ?></a>
        <a class="dropdown-item" href="<?php echo URLROOT ?>/friends?id=<?php echo $_SESSION['id']; ?>"><?php echo Lang::T("FRIENDS"); ?></a>
        <a class="dropdown-item" href="<?php echo URLROOT ?>/bonus"><?php echo Lang::T("SEEDING_BONUS"); ?></a> <!-- Check the link! -->
        <a class="dropdown-item" href="<?php echo URLROOT ?>/invite"><?php echo Lang::T("INVITES"); ?></a> <!-- Check the link! -->
      </li>
   <?php   if ($_SESSION["view_torrents"] == "yes") { ?>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        Torrents
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
        <a class="dropdown-item" href="<?php echo URLROOT ?>/search/browse"><?php echo Lang::T("BROWSE_TORRENTS"); ?></a>
			  <a class="dropdown-item" href="<?php echo URLROOT ?>/upload"><?php echo Lang::T("UPLOAD_TORRENT"); ?></a>
			  <a class="dropdown-item" href="<?php echo URLROOT ?>/search"><?php echo Lang::T("SEARCH_TORRENTS"); ?></a>
		  	<?php   if (Config::TT()["REQUESTSON"]) { ?>
        <a class="dropdown-item" href="<?php echo URLROOT ?>/request"><?php echo Lang::T("MAKE_REQUEST"); ?></a>
		  	<?php } ?>
        <a class="dropdown-item" href="<?php echo URLROOT ?>/search/today"><?php echo Lang::T("TODAYS_TORRENTS"); ?></a>
		  	<a class="dropdown-item" href="<?php echo URLROOT ?>/search/needseed"><?php echo Lang::T("TORRENT_NEED_SEED"); ?></a>
        </div>
      </li>
<?php } ?>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Forums
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
        <a class="dropdown-item" href="<?php echo URLROOT ?>/forums"><?php echo Lang::T("FORUMS"); ?></a>
		    <a class="dropdown-item" href="<?php echo URLROOT ?>/forums/viewunread"><?php echo Lang::T("FORUM_NEW_POSTS"); ?></a>
		    <a class="dropdown-item" href="<?php echo URLROOT ?>/forums/search"><?php echo Lang::T("SEARCH"); ?></a>
		    <a class="dropdown-item" href="<?php echo URLROOT ?>/faq"><?php echo Lang::T("FORUM_FAQ"); ?></a>
        </div>
      </li>

      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        Contact
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
		    <a class="dropdown-item" href="<?php echo URLROOT ?>/group/staff">Our Staff</a>
		    <a class="dropdown-item" href="<?php echo URLROOT ?>/contactstaff"><?php echo Lang::T("Contact Staff"); ?></a>
        </div>
      </li>
      <?php } else { ?>
        
      <?php } ?>
      </ul>

      <span class="navbar-text">
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
$privacylevel = Lang::T($_SESSION["privacy"]);

?>

<font color='#FFFFFF'><b>Hello</b>&nbsp;</font><a href="<?php echo URLROOT ?>/profile?id=<?php echo $_SESSION['id'] ?>"><b><?php echo Users::coloredname($_SESSION['username']); ?></b></a>&nbsp;&nbsp;
<a href="#"><i class="fa fa-download tticon"></i></a>&nbsp;<font color='#FFFFFF'><?php echo $userdownloaded ?></font>&nbsp;
<a href="#"><i class="fa fa-upload tticon"></i></a>&nbsp;<font color='#FFFFFF'><?php echo $useruploaded ?></font>&nbsp;
<a href="#"><i class="fa fa-cog tticon"></i></a>&nbsp;<font color='#FFFFFF'><?php echo $userratio ?></font>&nbsp;
<a href="<?php echo URLROOT ?>/bonus"><i class="fa fa fa-smile-o tticon"></i></a>&nbsp;<font color='#FFFFFF'><?php echo $_SESSION['seedbonus'] ?></font>&nbsp;

<a href="<?php echo URLROOT ?>/messages?type=inbox"><i class="fa fa-envelope tticon"></i></a>&nbsp;<font color='#FFFFFF'><?php echo $unreadmail ?></font>&nbsp;
<?php
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
                <a href="javascript:popout(0) "onclick="window.open('<?php echo  URLROOT ?>/peers/popoutseed?id=<?php echo  $_SESSION['id'] ?>','Seeding','width=350,height=350,scrollbars=yes')"><i class="fa fa-arrow-circle-up tticon"></i></a>&nbsp;<font color='#FFFFFF'><?php echo $activeseed ?></font>&nbsp;
                <a href="javascript:popout(0) "onclick="window.open('<?php echo  URLROOT ?>/peers/popoutleech?id=<?php echo  $_SESSION['id'] ?>','Leeching','width=350,height=350,scrollbars=yes')"><i class="fa fa-arrow-circle-down tticon"></i></a> &nbsp;<font color='#FFFFFF'><?php echo $activeleech ?></font>&nbsp;
                <a href="#"><i class="fa fa-refresh fa-spin fa-1x fa-fw tticon"></i></a>&nbsp;<?php echo $connectable ?>&nbsp;
              <?php
              } ?>

              <?php
              if ($_SESSION["control_panel"] == "yes") { ?>
          <a href="<?php echo URLROOT; ?>/admincp"><i class="fa fa-address-book tticon"></i></a>&nbsp;
          <?php } ?>
        <a href="<?php echo URLROOT; ?>/logout"><i class="fa fa-sign-out tticon"></i></a>
      <?php } else { ?>



        <a href="<?php echo URLROOT; ?>/login"><font color='#FFFFFF'><b>Login</b></font></a>
      <?php } ?>
      </span>
    </div>
  </div>
</nav>