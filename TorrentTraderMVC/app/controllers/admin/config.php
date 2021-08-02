<form method="post" action="<?php echo URLROOT ?>/adminconfig/submit">
<div class="container">
  <div class="row">
    <div class="col-md-4 border ttborder">
    <label for="SITENAME">Site Name:</label><br>
    <input type="text"  class="form-control" id="SITENAME" name="SITENAME" value="<?php echo Config::TT()['SITENAME'] ?>"><br>
    <label for="_SITEDESC">Site Description:</label><br>
    <input type="text"  class="form-control" id="_SITEDESC" name="_SITEDESC" value="<?php echo Config::TT()['_SITEDESC'] ?>"><br>
    <label for="SITEEMAIL">Site Email:</label><br>
    <input type="text"  class="form-control" id="SITEEMAIL" name="SITEEMAIL" value="<?php echo Config::TT()['SITEEMAIL'] ?>"><br>
    <label for="SITENOTICEON">Site Notice on/off:</label><br>
    <?php print("<input name='SITENOTICEON' value='true' type='radio' " . (Config::TT()['SITENOTICEON'] ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='SITENOTICEON' value='false' type='radio' " . (!Config::TT()['SITENOTICEON'] ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="SITENOTICE">Site Notice:</label><br>
    <input type="text"  class="form-control" id="SITENOTICE" name="SITENOTICE" value="<?php echo Config::TT()['SITENOTICE'] ?>"><br>
    <label for="SITE_ONLINE">Site Online:</label><br>
    <?php print("<input name='SITE_ONLINE' value='true' type='radio' " . (Config::TT()['SITE_ONLINE'] ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='SITE_ONLINE' value='false' type='radio' " . (!Config::TT()['SITE_ONLINE'] ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="OFFLINEMSG">Offline Messages:</label><br>
    <input type="text"  class="form-control" id="OFFLINEMSG" name="OFFLINEMSG" value="<?php echo Config::TT()['OFFLINEMSG'] ?>"><br>
    <label for="WELCOMEPM_ON">Welcome PM On:</label><br>
    <?php print("<input name='WELCOMEPM_ON' value='true' type='radio' " . (Config::TT()['WELCOMEPM_ON'] ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='WELCOMEPM_ON' value='false' type='radio' " . (!Config::TT()['WELCOMEPM_ON'] ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="WELCOMEPM_MSG">Welcome PM Message:</label><br>
    <input type="text"  class="form-control" id="WELCOMEPM_MSG" name="WELCOMEPM_MSG" value="<?php echo Config::TT()['WELCOMEPM_MSG'] ?>"><br>
    <label for="UPLOADRULES">Upload Rules:</label><br>
    <input type="text"  class="form-control" id="UPLOADRULES" name="UPLOADRULES" value="<?php echo Config::TT()['UPLOADRULES'] ?>"><br>
    <label for="TORRENTTABLE_COLUMNS">Torrnt Table Columns:</label><br>
    <input type="text"  class="form-control" id="TORRENTTABLE_COLUMNS" name="TORRENTTABLE_COLUMNS" value="<?php echo Config::TT()['TORRENTTABLE_COLUMNS'] ?>"><br>
    <label for="TORRENTTABLE_EXPAND">Torrent Table Expand:</label><br>
    <input type="text"  class="form-control" id="TORRENTTABLE_EXPAND" name="TORRENTTABLE_EXPAND" value="<?php echo Config::TT()['TORRENTTABLE_EXPAND'] ?>"><br>
    <label for="CAPTCHA_ON">Google Captcha on/off :</label><br>
    <?php print("<input name='CAPTCHA_ON' value='true' type='radio' " . (Config::TT()['CAPTCHA_ON'] ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='CAPTCHA_ON' value='false' type='radio' " . (!Config::TT()['CAPTCHA_ON'] ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="CAPTCHA_KEY">Captcha Key:</label><br>
    <input type="text"  class="form-control" id="CAPTCHA_KEY" name="CAPTCHA_KEY" value="<?php echo Config::TT()['CAPTCHA_KEY'] ?>"><br>
    <label for="CAPTCHA_SECRET">Captcha Secret:</label><br>
    <input type="text"  class="form-control" id="CAPTCHA_SECRET" name="CAPTCHA_SECRET" value="<?php echo Config::TT()['CAPTCHA_SECRET'] ?>"><br>
    
    </div>
    <div class="col-md-2 border ttborder">

    <label for="MEMBERSONLY">Members Only:</label><br>
    <?php print("<input name='MEMBERSONLY' value='true' type='radio' " . (Config::TT()['MEMBERSONLY'] ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='MEMBERSONLY' value='false' type='radio' " . (!Config::TT()['MEMBERSONLY'] ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="MEMBERSONLY_WAIT">Members Wait:</label><br>
    <?php print("<input name='MEMBERSONLY_WAIT' value='true' type='radio' " . (Config::TT()['MEMBERSONLY_WAIT'] ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='MEMBERSONLY_WAIT' value='false' type='radio' " . (!Config::TT()['MEMBERSONLY_WAIT'] ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="ALLOWEXTERNAL">Allow External:</label><br>
    <?php print("<input name='ALLOWEXTERNAL' value='true' type='radio' " . (Config::TT()['ALLOWEXTERNAL'] ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='ALLOWEXTERNAL' value='false' type='radio' " . (!Config::TT()['ALLOWEXTERNAL'] ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="UPLOADERSONLY">Uploader Only:</label><br>
    <?php print("<input name='UPLOADERSONLY' value='true' type='radio' " . (Config::TT()['UPLOADERSONLY'] ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='UPLOADERSONLY' value='false' type='radio' " . (!Config::TT()['UPLOADERSONLY'] ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="INVITEONLY">Invite Only:</label><br>
    <?php print("<input name='INVITEONLY' value='true' type='radio' " . (Config::TT()['INVITEONLY'] ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='INVITEONLY' value='false' type='radio' " . (!Config::TT()['INVITEONLY'] ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="ENABLEINVITES">Enable Invites:</label><br>
    <?php print("<input name='ENABLEINVITES' value='true' type='radio' " . (Config::TT()['ENABLEINVITES'] ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='ENABLEINVITES' value='false' type='radio' " . (!Config::TT()['ENABLEINVITES'] ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="CONFIRMEMAIL">Confirm Email:</label><br>
    <?php print("<input name='CONFIRMEMAIL' value='true' type='radio' " . (Config::TT()['CONFIRMEMAIL'] ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='CONFIRMEMAIL' value='false' type='radio' " . (!Config::TT()['CONFIRMEMAIL'] ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="ACONFIRM">Admin Confirm:</label><br>
    <?php print("<input name='ACONFIRM' value='true' type='radio' " . (Config::TT()['ACONFIRM'] ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='ACONFIRM' value='false' type='radio' " . (!Config::TT()['ACONFIRM'] ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="ANONYMOUSUPLOAD">Anon Uploads:</label><br>
    <?php print("<input name='ANONYMOUSUPLOAD' value='true' type='radio' " . (Config::TT()['ANONYMOUSUPLOAD'] ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='ANONYMOUSUPLOAD' value='false' type='radio' " . (!Config::TT()['ANONYMOUSUPLOAD'] ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="UPLOADSCRAPE">Upload Scrape:</label><br>
    <?php print("<input name='UPLOADSCRAPE' value='true' type='radio' " . (Config::TT()['UPLOADSCRAPE'] ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='UPLOADSCRAPE' value='false' type='radio' " . (!Config::TT()['UPLOADSCRAPE'] ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="FORUMS">Forums:</label><br>
    <?php print("<input name='FORUMS' value='true' type='radio' " . (Config::TT()['FORUMS'] ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='FORUMS' value='false' type='radio' " . (!Config::TT()['FORUMS'] ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="FORUMS_GUESTREAD">Forum Guest Read:</label><br>
    <?php print("<input name='FORUMS_GUESTREAD' value='true' type='radio' " . (Config::TT()['FORUMS_GUESTREAD'] ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='FORUMS_GUESTREAD' value='false' type='radio' " . (!Config::TT()['FORUMS_GUESTREAD'] ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="OLD_CENSOR">Old Censor:</label><br>
    <?php print("<input name='OLD_CENSOR' value='true' type='radio' " . (Config::TT()['OLD_CENSOR'] ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='OLD_CENSOR' value='false' type='radio' " . (!Config::TT()['OLD_CENSOR'] ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="FORCETHANKS">Force Thanks:</label><br>
    <?php print("<input name='FORCETHANKS' value='true' type='radio' " . (Config::TT()['FORCETHANKS'] ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='FORCETHANKS' value='false' type='radio' " . (!Config::TT()['FORCETHANKS'] ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="ALLOWLIKES">Allow Likes:</label><br>
    <?php print("<input name='ALLOWLIKES' value='true' type='radio' " . (Config::TT()['ALLOWLIKES'] ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='ALLOWLIKES' value='false' type='radio' " . (!Config::TT()['ALLOWLIKES'] ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="REQUESTSON">request On:</label><br>
    <?php print("<input name='REQUESTSON' value='true' type='radio' " . (Config::TT()['REQUESTSON'] ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='REQUESTSON' value='false' type='radio' " . (!Config::TT()['REQUESTSON'] ? " checked='checked'" : "") . " />False<br><br>");?>
    
    </div>
    <div class="col-md-2 border ttborder">

    <label for="LEFTNAV">Left Blocks on/off:</label><br>
    <?php print("<input name='LEFTNAV' value='true' type='radio' " . (Config::TT()['LEFTNAV'] ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='LEFTNAV' value='false' type='radio' " . (!Config::TT()['LEFTNAV'] ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="RIGHTNAV">Right Blocks on/off:</label><br>
    <?php print("<input name='RIGHTNAV' value='true' type='radio' " . (Config::TT()['RIGHTNAV'] ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='RIGHTNAV' value='false' type='radio' " . (!Config::TT()['RIGHTNAV'] ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="MIDDLENAV">Middle Blocks on/off:</label><br>
    <?php print("<input name='MIDDLENAV' value='true' type='radio' " . (Config::TT()['MIDDLENAV'] ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='MIDDLENAV' value='false' type='radio' " . (!Config::TT()['MIDDLENAV'] ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="SHOUTBOX">Shoutbox on/off:</label><br>
    <?php print("<input name='SHOUTBOX' value='true' type='radio' " . (Config::TT()['SHOUTBOX'] ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='SHOUTBOX' value='false' type='radio' " . (!Config::TT()['SHOUTBOX'] ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="NEWSON">News on/off:</label><br>
    <?php print("<input name='NEWSON' value='true' type='radio' " . (Config::TT()['NEWSON'] ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='NEWSON' value='false' type='radio' " . (!Config::TT()['NEWSON'] ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="DONATEON">Donate on/off:</label><br>
    <?php print("<input name='DONATEON' value='true' type='radio' " . (Config::TT()['DONATEON'] ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='DONATEON' value='false' type='radio' " . (!Config::TT()['DONATEON'] ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="DISCLAIMERON">Discliamer on/off:</label><br>
    <?php print("<input name='DISCLAIMERON' value='true' type='radio' " . (Config::TT()['DISCLAIMERON'] ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='DISCLAIMERON' value='false' type='radio' " . (!Config::TT()['DISCLAIMERON'] ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="FORUMONINDEX">Forum On Index:</label><br>
    <?php print("<input name='FORUMONINDEX' value='true' type='radio' " . (Config::TT()['FORUMONINDEX'] ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='FORUMONINDEX' value='false' type='radio' " . (!Config::TT()['FORUMONINDEX'] ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="IPCHECK">IP Check:</label><br>
    <?php print("<input name='IPCHECK' value='true' type='radio' " . (Config::TT()['IPCHECK'] ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='IPCHECK' value='false' type='radio' " . (!Config::TT()['IPCHECK'] ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="ACCOUNTMAX">Account Max:</label><br>
    <input type="text"  class="form-control" id="ACCOUNTMAX" name="ACCOUNTMAX" value="<?php echo Config::TT()['ACCOUNTMAX'] ?>"><br>
    <label for="IMDB1">IMDB:</label><br>
    <?php print("<input name='IMDB' value='true' type='radio' " . (Config::TT()['IMDB'] ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='IMDB' value='false' type='radio' " . (!Config::TT()['IMDB'] ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="YOU_TUBE">Youtube:</label><br>
    <?php print("<input name='YOU_TUBE' value='true' type='radio' " . (Config::TT()['YOU_TUBE'] ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='YOU_TUBE' value='false' type='radio' " . (!Config::TT()['YOU_TUBE'] ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="FREELEECHGBON">Free Leech On:</label><br>
    <?php print("<input name='FREELEECHGBON' value='true' type='radio' " . (Config::TT()['FREELEECHGBON'] ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='FREELEECHGBON' value='false' type='radio' " . (!Config::TT()['FREELEECHGBON'] ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="FREELEECHGB">Free Leech GB:</label><br>
    <input type="text"  class="form-control" id="FREELEECHGB" name="FREELEECHGB" value="<?php echo Config::TT()['FREELEECHGB'] ?>"><br>
    <label for="HIDEBBCODE">Hide BBcode:</label><br>
    <?php print("<input name='HIDEBBCODE' value='true' type='radio' " . (Config::TT()['HIDEBBCODE'] ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='HIDEBBCODE' value='false' type='radio' " . (!Config::TT()['HIDEBBCODE'] ? " checked='checked'" : "") . " />False<br><br>");?>

    </div>
    <div class="col-md-2 border ttborder">

    <label for="DEFAULTLANG">Default Language:</label><br>
    <input type="text"  class="form-control" id="DEFAULTLANG" name="DEFAULTLANG" value="<?php echo Config::TT()['DEFAULTLANG'] ?>"><br>
    <label for="DEFAULTTHEME">Default Theme:</label><br>
    <input type="text"  class="form-control" id="DEFAULTTHEME" name="DEFAULTTHEME" value="<?php echo Config::TT()['DEFAULTTHEME'] ?>"><br>
    <label for="MAXUSERS">Max Users:</label><br>
    <input type="text"  class="form-control" id="MAXUSERS" name="MAXUSERS" value="<?php echo Config::TT()['MAXUSERS'] ?>"><br>
    <label for="MAXUSERSINVITE">Max Users Invited:</label><br>
    <input type="text"  class="form-control" id="MAXUSERSINVITE" name="MAXUSERSINVITE" value="<?php echo Config::TT()['MAXUSERSINVITE'] ?>"><br>
    <label for="CURRENCYSYMBOL">Currency Symbol:</label><br>
    <input type="text"  class="form-control" id="CURRENCYSYMBOL" name="CURRENCYSYMBOL" value="<?php echo Config::TT()['CURRENCYSYMBOL'] ?>"><br>
    <label for="BONUSPERTIME">Bonus Time:</label><br>
    <input type="text"  class="form-control" id="BONUSPERTIME" name="BONUSPERTIME" value="<?php echo Config::TT()['BONUSPERTIME'] ?>"><br>
    <label for="ADDBONUS">Add Bonus:</label><br>
    <input type="text"  class="form-control" id="ADDBONUS" name="ADDBONUS" value="<?php echo Config::TT()['ADDBONUS'] ?>"><br>
    <label for="CACHE_TYPE">Cache Type:</label><br>
    <input type="text"  class="form-control" id="CACHE_TYPE" name="CACHE_TYPE" value="<?php echo Config::TT()['CACHE_TYPE'] ?>"><br>
    <label for="MEMCACHE_HOST">Memcache Host:</label><br>
    <input type="text"  class="form-control" id="MEMCACHE_HOST" name="MEMCACHE_HOST" value="<?php echo Config::TT()['MEMCACHE_HOST'] ?>"><br>
    <label for="MEMCACHE_PORT">Memcache Port:</label><br>
    <input type="text"  class="form-control" id="MEMCACHE_PORT" name="MEMCACHE_PORT" value="<?php echo Config::TT()['MEMCACHE_PORT'] ?>"><br>
    <label for="RATIOWARNENABLE">Ratio Warn Enable:</label><br>
    <?php print("<input name='RATIOWARNENABLE' value='true' type='radio' " . (Config::TT()['RATIOWARNENABLE'] ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='RATIOWARNENABLE' value='false' type='radio' " . (!Config::TT()['RATIOWARNENABLE'] ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="RATIOWARNMINRATIO">Warn Min Ration:</label><br>
    <input type="text"  class="form-control" id="RATIOWARNMINRATIO" name="RATIOWARNMINRATIO" value="<?php echo Config::TT()['RATIOWARNMINRATIO'] ?>"><br>
    <label for="RATIOWARN_MINGIGS">Warn Min Gigs:</label><br>
    <input type="text"  class="form-control" id="RATIOWARN_MINGIGS" name="RATIOWARN_MINGIGS" value="<?php echo Config::TT()['RATIOWARN_MINGIGS'] ?>"><br>
    <label for="RATIOWARN_DAYSTOWARN">Days To Warn:</label><br>
    <input type="text"  class="form-control" id="RATIOWARN_DAYSTOWARN" name="RATIOWARN_DAYSTOWARN" value="<?php echo Config::TT()['RATIOWARN_DAYSTOWARN'] ?>"><br>

    </div>
    <div class="col-md-2 border ttborder">

    <label for="PEERLIMIT">Peer Limit:</label><br>
    <input type="text"  class="form-control" id="PEERLIMIT" name="PEERLIMIT" value="<?php echo Config::TT()['PEERLIMIT'] ?>"><br>
    <label for="AUTOCLEANINTERVAL">Autoclean Interval:</label><br>
    <input type="text"  class="form-control" id="AUTOCLEANINTERVAL" name="AUTOCLEANINTERVAL" value="<?php echo Config::TT()['AUTOCLEANINTERVAL'] ?>"><br>
    <label for="ANNOUNCEINTERVAL">Announce Interval:</label><br>
    <input type="text"  class="form-control" id="ANNOUNCEINTERVAL" name="ANNOUNCEINTERVAL" value="<?php echo Config::TT()['ANNOUNCEINTERVAL'] ?>"><br>
    <label for="SIGNUPTIMEOUT">Signup Timeout:</label><br>
    <input type="text"  class="form-control" id="SIGNUPTIMEOUT" name="SIGNUPTIMEOUT" value="<?php echo Config::TT()['SIGNUPTIMEOUT'] ?>"><br>
    <label for="MAXDEADTORRENTTIMEOUT">Dead Torrent Timeout:</label><br>
    <input type="text"  class="form-control" id="MAXDEADTORRENTTIMEOUT" name="MAXDEADTORRENTTIMEOUT" value="<?php echo Config::TT()['MAXDEADTORRENTTIMEOUT'] ?>"><br>
    <label for="mail_type">Mail Type:</label><br>
    <input type="text"  class="form-control" id="mail_type" name="mail_type" value="<?php echo Config::TT()['mail_type'] ?>"><br>
    <label for="mail_smtp_host">SMTP Host:</label><br>
    <input type="text"  class="form-control" id="mail_smtp_host" name="mail_smtp_host" value="<?php echo Config::TT()['mail_smtp_host'] ?>"><br>
    <label for="mail_smtp_port">SMTP Port:</label><br>
    <input type="text"  class="form-control" id="mail_smtp_port" name="mail_smtp_port" value="<?php echo Config::TT()['mail_smtp_port'] ?>"><br>
    <label for="mail_smtp_ssl">SMTP SSL:</label><br>
    <?php print("<input name='mail_smtp_ssl' value='true' type='radio' " . (Config::TT()['mail_smtp_ssl'] ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='mail_smtp_ssl' value='false' type='radio' " . (!Config::TT()['mail_smtp_ssl'] ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="mail_smtp_auth">SMTP Auth:</label><br>
    <?php print("<input name='mail_smtp_auth' value='true' type='radio' " . (Config::TT()['mail_smtp_auth'] ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='mail_smtp_auth' value='false' type='radio' " . (!Config::TT()['mail_smtp_auth'] ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="mail_smtp_user">SMTP User:</label><br>
    <input type="text"  class="form-control" id="mail_smtp_user" name="mail_smtp_user" value="<?php echo Config::TT()['mail_smtp_user'] ?>"><br>
    <label for="mail_smtp_pass">SMTP Pass:</label><br>
    <input type="text"  class="form-control" id="mail_smtp_pass" name="mail_smtp_pass" value="<?php echo Config::TT()['mail_smtp_pass'] ?>"><br>

    </div>
  </div>
</div>

<center><input type="submit"  class="btn btn-sm ttbtn" value="Submit"></center>

</form>