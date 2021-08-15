    <div class="logo-details">
      <i class='bx bx-menu'></i>
      <span class="logo_name">Torrent Trader</span>
    </div>

    <ul class="nav-links">
      <li>
        <a href="<?php echo URLROOT ?>">
          <i class='bx bx-home' ></i>
          <span class="link_name">Home</span>
        </a>
      </li>
      <li>
        <div class="iocn-link">
          <a href="<?php echo URLROOT ?>/messages?type=inbox">
            <i class='bx bx-message-rounded-dots' ></i>
            <span class="link_name">Messages</span>
          </a>
          <i class='bx bxs-chevron-down arrow' ></i>
        </div>
        <ul class="sub-menu">
          <li><a class="link_name" href="<?php echo URLROOT ?>/messages?type=inbox">Messages</a></li>
          <li><a href="<?php echo URLROOT ?>/messages/create">Create</a></li>
          <li><a href="<?php echo URLROOT ?>/messages/overview">Overview</a></li>
        </ul>
      </li>
      <li>
        <div class="iocn-link">
          <a href="<?php echo URLROOT ?>/forums">
            <i class='bx bx-chat' ></i>
            <span class="link_name">Forums</span>
          </a>
          <i class='bx bxs-chevron-down arrow' ></i>
        </div>
        <ul class="sub-menu">
          <li><a class="link_name" href="<?php echo URLROOT ?>/forums">Forums</a></li>
          <li><a href="<?php echo URLROOT ?>/forums/viewunread">New Posts</a></li>
          <li><a href="<?php echo URLROOT ?>/forums/search">Search Forums</a></li>
        </ul>
      </li>
      <li>
        <div class="iocn-link">
         <a href="<?php echo URLROOT ?>/contactstaff">
          <i class='bx bx-book-content' ></i>
          <span class="link_name">Contact</span>
        </a>
        <i class='bx bxs-chevron-down arrow' ></i>
        </div>
        <ul class="sub-menu">
          <li><a class="link_name" href="<?php echo URLROOT ?>/contactstaff">Contact</a></li>
          <li><a href="<?php echo URLROOT ?>/group/staff">Staff</a></li>
        </ul>
      </li>
      <li>
        <div class="iocn-link">
         <a href="<?php echo URLROOT ?>/search/browse">
          <i class='bx bx-download' ></i>
          <span class="link_name">Torrents</span>
        </a>
        <i class='bx bxs-chevron-down arrow' ></i>
        </div>
        <ul class="sub-menu">
          <li><a class="link_name" href="<?php echo URLROOT ?>/search/browse">Torrents</a></li>
          <li><a href="<?php echo URLROOT ?>/upload">Upload</a></li>
          <li><a href="<?php echo URLROOT ?>/search">Search</a></li>
          <?php   if (Config::TT()["REQUESTSON"]) { ?>
          <li><a href="<?php echo URLROOT ?>/Request">Request</a></li>
          <?php } ?>
          <li><a href="<?php echo URLROOT ?>/search/today">Todays</a></li>
          <li><a href="<?php echo URLROOT ?>/search/needseed">Need Seed</a></li>
        </ul>
      </li>

      <li>
        <div class="iocn-link">
         <a href="<?php echo URLROOT ?>/search/browse">
          <i class='bx bx-paint-roll' ></i>
          <span class="link_name">Theme</span>
        </a>
        <i class='bx bxs-chevron-down arrow' ></i>
        </div>
        <ul class="sub-menu">
<?php    $stylesheets = '';
    $ss_r = DB::run("SELECT * from stylesheets");
    $ss_sa = array();
    while ($ss_a = $ss_r->fetch(PDO::FETCH_ASSOC)) {
        $ss_id = $ss_a["uri"];
        $ss_name = $ss_a["name"];
        $ss_sa[$ss_name] = $ss_id;
    }
    ksort($ss_sa);
    reset($ss_sa);
    while (list($ss_name, $ss_id) = thisEach($ss_sa)) {
        if ($ss_id == $_SESSION["stylesheet"]) {
            $ss = " selected='selected'";
        } else {
            $ss = "";
        }
        $stylesheets .= "<option value='$ss_id'$ss>$ss_name</option>\n";
    }   ?>

<form method="post" action="<?php echo URLROOT; ?>/stylesheet/forbooty" class="form-horizontal">
  
<div class="form-group">
		<center><label><?php echo Lang::T("THEME"); ?></label></center>
		<select name="stylesheet" style="width: 95%" ><?php echo $stylesheets; ?></select>
</div>
    <center><button type="submit" class="btn ttbtn" value="" /><?php echo Lang::T("APPLY"); ?></button><center>
</form>
        </ul>
      </li>

  </ul>