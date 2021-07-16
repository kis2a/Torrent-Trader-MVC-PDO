<nav class="navbar navbar-expand-lg">
  <a class="navbar-brand" href="<?php echo URLROOT; ?>"><font color='#fff'><b><?php echo SITENAME; ?></b><br><small><?php echo _SITEDESC; ?></small></font></a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
    <i class="fa fa-bars" style="color:#fff; font-size:28px;"></i>
  </button>
  
  <div class="collapse navbar-collapse" id="navbarsExampleDefault">
    <?php if (isset($_SESSION['id'])): ?>
    <ul class="navbar-nav mr-auto">

      <li class="nav-item active">
        <a class="nav-link" href="<?php echo URLROOT ?>">Home <span class="sr-only">(current)</span></a>
      </li>

      <li class="nav-item">
      <?php
      $arr = DB::run("SELECT * FROM messages WHERE receiver=" . $_SESSION["id"] . " and unread='yes' AND location IN ('in','both')")->fetchAll();
      $unreadmail = count($arr);
    if ($unreadmail !== 0) {
      print("<a class='nav-link' href='" . URLROOT . "/messages?type=inbox'><b><font color='#fff'>$unreadmail</font> " . Lang::N("NEWPM", $unreadmail) . "</b></a>");
    } else {
      print("<a class='nav-link' href='" . URLROOT . "/messages/overview'>" . Lang::T("YOUR_MESSAGES") . "</a>");
    }
    ?>
      </li>

      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        Profile
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
		    <a class="dropdown-item" href="<?php echo URLROOT ?>/profile?id=<?php echo $_SESSION["id"]; ?>"><?php echo Lang::T("PROFILE"); ?></a>
		  	<a class="dropdown-item" href="<?php echo URLROOT ?>/messages?type=inbox"><?php echo Lang::T("YOUR_MESSAGES"); ?></a>
        <a class="dropdown-item" href="<?php echo URLROOT ?>/peers/seeding?id=<?php echo $_SESSION['id']; ?>"><?php echo Lang::T("YOUR_TORRENTS"); ?></a>
        <a class="dropdown-item" href="<?php echo URLROOT ?>/friends?id=<?php echo $_SESSION['id']; ?>"><?php echo Lang::T("FRIENDS"); ?></a>
        <a class="dropdown-item" href="<?php echo URLROOT ?>/bonus"><?php echo Lang::T("SEEDING_BONUS"); ?></a> <!-- Check the link! -->
        <a class="dropdown-item" href="<?php echo URLROOT ?>/invite"><?php echo Lang::T("INVITES"); ?></a> <!-- Check the link! -->
        <div class="dropdown-divider"></div>
        <a class="dropdown-item" href="#">Something else here</a>
        </div>
      </li>

      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        Torrents
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
        <a class="dropdown-item" href="<?php echo URLROOT ?>/search/browse"><?php echo Lang::T("BROWSE_TORRENTS"); ?></a>
			  <a class="dropdown-item" href="<?php echo URLROOT ?>/upload"><?php echo Lang::T("UPLOAD_TORRENT"); ?></a>
			  <a class="dropdown-item" href="<?php echo URLROOT ?>/search"><?php echo Lang::T("SEARCH_TORRENTS"); ?></a>
		  	<a class="dropdown-item" href="<?php echo URLROOT ?>/request"><?php echo Lang::T("MAKE_REQUEST"); ?></a>
		  	<a class="dropdown-item" href="<?php echo URLROOT ?>/search/today"><?php echo Lang::T("TODAYS_TORRENTS"); ?></a>
		  	<a class="dropdown-item" href="<?php echo URLROOT ?>/search/needseed"><?php echo Lang::T("TORRENT_NEED_SEED"); ?></a>
        </div>
      </li>

      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        Contact
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
		    <a class="dropdown-item" href="<?php echo URLROOT ?>/group/staff">Our Staff</a>
		    <a class="dropdown-item" href="<?php echo URLROOT ?>/contactstaff"><?php echo Lang::T("Contact Staff"); ?></a>
        </div>
      </li>

    </ul>
    <?php endif;?>

    <?php if (!$_SESSION['id']): ?>
    <ul class="navbar-nav mr-auto">
    <a href="<?php echo URLROOT ?>/forums"><b><?php echo Lang::T("FORUMS"); ?></b></a>&nbsp;&nbsp;
	  </ul>
    <?php endif;?>

    <ul class="navbar-nav navbar-right d-none d-sm-block">
    <?php if (isset($_SESSION['id'])): ?>
          <a href="<?php echo URLROOT; ?>/profile?id=<?php echo $_SESSION['id']; ?>">Hi <b><?php echo Users::coloredname($_SESSION['username']); ?></b></a>&nbsp;
    <?php
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
      print("&nbsp;&nbsp;
        <img src='" . URLROOT . "/assets/images/up.gif' border='none' height='20' width='20' alt='Downloaded' title='Downloaded'><font color='#FFCC66'><b>$userdownloaded</b></font>&nbsp;&nbsp;
        <img src='" . URLROOT . "/assets/images/seed.gif' border='none' height='20' width='20' alt='Uploaded' title='Uploaded'> <font color='#33CCCC'><b>$useruploaded</b></font>&nbsp;&nbsp;
        <img src='" . URLROOT . "/assets/images/button_online.png' border='none' height='20' width='20' alt='Ratio' title='Ratio'> (<b><font color='#FFF'>$userratio</font></b>)&nbsp;&nbsp;
        B &nbsp;<a href='" . URLROOT . "/bonus' title='Bonus'><font color=#00cc00>$_SESSION[seedbonus]</font></a>&nbsp;&nbsp;");
      // connectable yes or no
      if ($_SESSION["view_torrents"] == "yes") {
        $activeseed = get_row_count("peers", "WHERE userid = '$_SESSION[id]' AND seeder = 'yes'");
        $activeleech = get_row_count("peers", "WHERE userid = '$_SESSION[id]' AND seeder = 'no'");
        $stmt = DB::run("SELECT connectable FROM peers WHERE userid=? LIMIT 1", [$_SESSION['id']]);
        $connect = $stmt->fetchColumn();
        if ($connect == 'yes') {
           $connectable = "<b><font face=\"Verdana\" style=\"font-size: 10px\" color=\"#00FF00\">YES</font></b>";
        } elseif ($connect == 'no') {
           $connectable = "<b><font face=\"Verdana\" style=\"font-size: 10px\" color=\"FF0000\">NO</font></b>";
        } else {
           $connectable = "<b><font face=\"Verdana\" style=\"font-size: 10px\" color=\"99CCFF\">Check Settings</font></b>";
        }
        }
        print("&nbsp;<font color=#fff>(<i>S:</i></font>&nbsp; <a href=\"javascript:popout(0)\"onclick=\"window.open('" . URLROOT . "/peers/popoutseed?id=" . $_SESSION["id"] . "','Seeding','width=350,height=350,scrollbars=yes')\"><font color='#ffff00'><b>" . $activeseed . "</b></font></a>&nbsp;");
        print("<font color=#fff><i>L:</i> </font>&nbsp;<a href=\"javascript:popout(0)\"onclick=\"window.open('" . URLROOT . "/peers/popoutleech?id=" . $_SESSION["id"] . "','Seeding','width=350,height=350,scrollbars=yes')\"><font color='#ffff00'><b>" . $activeleech . "</b></font></a>&nbsp;");
        print("<font color=#fff><i>C:</i></font>&nbsp; " . $connectable . ")");
        if ($_SESSION["control_panel"] == "yes"): ?>
          &nbsp;&nbsp;<a href="<?php echo URLROOT; ?>/admincp"><font color='#fff'><?php echo Lang::T("STAFFCP") ?></font></a>&nbsp;&nbsp;
          <?php endif;?>
          <a href="<?php echo URLROOT; ?>/logout">Logout</a>&nbsp;
    <?php endif;?>
    </ul>
  </div>
</nav>