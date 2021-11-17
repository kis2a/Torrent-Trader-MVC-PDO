<form method="post" action="<?php echo URLROOT ?>/adminconfig/submit">

<p class='text-center'>To Allow User, add username to _OWNERS setting manually at app/config/settings.php</p>
<p class='text-center'>Like your Settings ? Make a backup before changing <a href='<?php echo URLROOT; ?>/adminconfig/backup ?>'> Here</a></p>

<div class="jumbotron border ttborder"><br>
  <div class="row">
    <div class="col-md-4">

    <label for="SITENAME">Site Name:</label><br>
    <input type="text"  class="form-control" id="SITENAME" name="SITENAME" value="<?php echo Config::TT()['SITENAME'] ?>"><br>
    <label for="_SITEDESC">Site Description:</label><br>
    <input type="text"  class="form-control" id="_SITEDESC" name="_SITEDESC" value="<?php echo Config::TT()['_SITEDESC'] ?>"><br>
    <label for="SITEEMAIL">Site Email:</label><br>
    <input type="text"  class="form-control" id="SITEEMAIL" name="SITEEMAIL" value="<?php echo Config::TT()['SITEEMAIL'] ?>"><br>
    <label for="SITENOTICEON">Site Notice on/off:</label><br>
    <?php $checked = Config::TT()['SITENOTICE'] == 1;
    print("<input name='SITENOTICEON' value='1' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='SITENOTICEON' value='0' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="SITENOTICE">Site Notice:</label><br>
    <input type="text"  class="form-control" id="SITENOTICE" name="SITENOTICE" value="<?php echo Config::TT()['SITENOTICE'] ?>"><br>
    <label for="SITE_ONLINE">Site Online:</label><br>
    <?php $checked = Config::TT()['SITE_ONLINE'] == 1;
    print("<input name='SITE_ONLINE' value='1' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='SITE_ONLINE' value='0' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="OFFLINEMSG">Offline Messages:</label><br>
    <input type="text"  class="form-control" id="OFFLINEMSG" name="OFFLINEMSG" value="<?php echo Config::TT()['OFFLINEMSG'] ?>"><br>
    <label for="WELCOMEPM_ON">Welcome PM On:</label><br>
    <?php $checked = Config::TT()['WELCOMEPM_ON'] == 1;
    print("<input name='WELCOMEPM_ON' value='1' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='WELCOMEPM_ON' value='0' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="WELCOMEPM_MSG">Welcome PM Message:</label><br>
    <input type="text"  class="form-control" id="WELCOMEPM_MSG" name="WELCOMEPM_MSG" value="<?php echo Config::TT()['WELCOMEPM_MSG'] ?>"><br>
    <label for="UPLOADRULES">Upload Rules:</label><br>
    <input type="text"  class="form-control" id="UPLOADRULES" name="UPLOADRULES" value="<?php echo Config::TT()['UPLOADRULES'] ?>"><br>
    <label for="TORRENTTABLE_COLUMNS">Torrnt Table Columns:</label><br>
    <input type="text"  class="form-control" id="TORRENTTABLE_COLUMNS" name="TORRENTTABLE_COLUMNS" value="<?php echo Config::TT()['TORRENTTABLE_COLUMNS'] ?>"><br>
    <label for="CAPTCHA_ON">Google Captcha on/off :</label><br>
    <?php $checked = Config::TT()['CAPTCHA_ON'] == 1;
    print("<input name='CAPTCHA_ON' value='1' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='CAPTCHA_ON' value='0' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="CAPTCHA_KEY">Captcha Key:</label><br>
    <input type="text"  class="form-control" id="CAPTCHA_KEY" name="CAPTCHA_KEY" value="<?php echo Config::TT()['CAPTCHA_KEY'] ?>"><br>
    <label for="CAPTCHA_SECRET">Captcha Secret:</label><br>
    <input type="text"  class="form-control" id="CAPTCHA_SECRET" name="CAPTCHA_SECRET" value="<?php echo Config::TT()['CAPTCHA_SECRET'] ?>"><br>
    
    </div>
    <div class="col-md-2">

    <label for="MEMBERSONLY">Members Only:</label><br>
    <?php $checked = Config::TT()['"MEMBERSONLY'] == 1;
    print("<input name='MEMBERSONLY' value='1' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='MEMBERSONLY' value='0' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="MEMBERSONLY_WAIT">Members Wait:</label><br>
    <?php $checked = Config::TT()['MEMBERSONLY_WAIT'] == 1;
    print("<input name='MEMBERSONLY_WAIT' value='1' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='MEMBERSONLY_WAIT' value='0' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="ALLOWEXTERNAL">Allow External:</label><br>
    <?php $checked = Config::TT()['ALLOWEXTERNAL'] == 1;
    print("<input name='ALLOWEXTERNAL' value='1' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='ALLOWEXTERNAL' value='0' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="UPLOADERSONLY">Uploader Only:</label><br>
    <?php $checked = Config::TT()['UPLOADERSONLY'] == 1;
    print("<input name='UPLOADERSONLY' value='1' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='UPLOADERSONLY' value='0' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="INVITEONLY">Invite Only:</label><br>
    <?php $checked = Config::TT()['INVITEONLY'] == 1;
    print("<input name='INVITEONLY' value='1' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='INVITEONLY' value='0' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="ENABLEINVITES">Enable Invites:</label><br>
    <?php $checked = Config::TT()['ENABLEINVITES'] == 1;
    print("<input name='ENABLEINVITES' value='1' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='ENABLEINVITES' value='0' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="CONFIRMEMAIL">Confirm Email:</label><br>
    <?php $checked = Config::TT()['CONFIRMEMAIL'] == 1;
    print("<input name='CONFIRMEMAIL' value='1' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='CONFIRMEMAIL' value='0' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="ACONFIRM">Admin Confirm:</label><br>
    <?php $checked = Config::TT()['ACONFIRM'] == 1;
    print("<input name='ACONFIRM' value='1' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='ACONFIRM' value='0' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="ANONYMOUSUPLOAD">Anon Uploads:</label><br>
    <?php $checked = Config::TT()['ANONYMOUSUPLOAD'] == 1;
    print("<input name='ANONYMOUSUPLOAD' value='1' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='ANONYMOUSUPLOAD' value='0' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="UPLOADSCRAPE">Upload Scrape:</label><br>
    <?php $checked = Config::TT()['UPLOADSCRAPE'] == 1;
    print("<input name='UPLOADSCRAPE' value='1' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='UPLOADSCRAPE' value='0' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="FORUMS">Forums:</label><br>
    <?php $checked = Config::TT()['FORUMS'] == 1;
    print("<input name='FORUMS' value='1' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='FORUMS' value='0' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="FORUMS_GUESTREAD">Forum Guest Read:</label><br>
    <?php $checked = Config::TT()['FORUMS_GUESTREAD'] == 1;
    print("<input name='FORUMS_GUESTREAD' value='1' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='FORUMS_GUESTREAD' value='0' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="OLD_CENSOR">Old Censor:</label><br>
    <?php $checked = Config::TT()['OLD_CENSOR'] == 1;
    print("<input name='OLD_CENSOR' value='1' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='OLD_CENSOR' value='0' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="FORCETHANKS">Force Thanks:</label><br>
    <?php $checked = Config::TT()['FORCETHANKS'] == 1;
    print("<input name='FORCETHANKS' value='1' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='FORCETHANKS' value='0' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="ALLOWLIKES">Allow Likes:</label><br>
    <?php $checked = Config::TT()['ALLOWLIKES'] == 1;
    print("<input name='ALLOWLIKES' value='1' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='ALLOWLIKES' value='0' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="REQUESTSON">request On:</label><br>
    <?php $checked = Config::TT()['REQUESTSON'] == 1;
    print("<input name='REQUESTSON' value='1' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='REQUESTSON' value='0' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    
    </div>
    <div class="col-md-2">

    <label for="LEFTNAV">Left Blocks on/off:</label><br>
    <?php $checked = Config::TT()['LEFTNAV'] == 1;
    print("<input name='LEFTNAV' value='1' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='LEFTNAV' value='0' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="RIGHTNAV">Right Blocks on/off:</label><br>
    <?php $checked = Config::TT()['RIGHTNAV'] == 1;
    print("<input name='RIGHTNAV' value='1' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='RIGHTNAV' value='0' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="MIDDLENAV">Middle Blocks on/off:</label><br>
    <?php $checked = Config::TT()['MIDDLENAV'] == 1;
    print("<input name='MIDDLENAV' value='1' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='MIDDLENAV' value='0' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="SHOUTBOX">Shoutbox on/off:</label><br>
    <?php $checked = Config::TT()['SHOUTBOX'] == 1;
    print("<input name='SHOUTBOX' value='1' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='SHOUTBOX' value='0' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="NEWSON">News on/off:</label><br>
    <?php $checked = Config::TT()['NEWSON'] == 1;
    print("<input name='NEWSON' value='1' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='NEWSON' value='0' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="DONATEON">Donate on/off:</label><br>
    <?php $checked = Config::TT()['DONATEON'] == 1;
    print("<input name='DONATEON' value='1' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='DONATEON' value='0' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="DISCLAIMERON">Discliamer on/off:</label><br>
    <?php $checked = Config::TT()['DISCLAIMERON'] == 1;
    print("<input name='DISCLAIMERON' value='1' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='DISCLAIMERON' value='0' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="FORUMONINDEX">Forum On Index:</label><br>
    <?php $checked = Config::TT()['FORUMONINDEX'] == 1;
    print("<input name='FORUMONINDEX' value='1' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='FORUMONINDEX' value='0' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="FORUMONINDEX">Latest Forum Post On Index:</label><br>
    <?php $checked = Config::TT()['LATESTFORUMPOSTONINDEX'] == 1;
    print("<input name='LATESTFORUMPOSTONINDEX' value='1' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='LATESTFORUMPOSTONINDEX' value='0' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="IPCHECK">IP Check:</label><br>
    <?php $checked = Config::TT()['IPCHECK'] == 1;
    print("<input name='IPCHECK' value='1' type='radio' " . ($checked? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='IPCHECK' value='0' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="ACCOUNTMAX">Account Max:</label><br>
    <input type="text"  class="form-control" id="ACCOUNTMAX" name="ACCOUNTMAX" value="<?php echo Config::TT()['ACCOUNTMAX'] ?>"><br>
    <label for="YOU_TUBE">Youtube:</label><br>
    <?php $checked = Config::TT()['YOU_TUBE'] == 1;
    print("<input name='YOU_TUBE' value='1' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='YOU_TUBE' value='0' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="FREELEECHGBON">Free Leech On:</label><br>
    <?php $checked = Config::TT()['FREELEECHGBON'] == 1;
    print("<input name='FREELEECHGBON' value='1' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='FREELEECHGBON' value='0' type='radio' " . (!$checked? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="FREELEECHGB">Free Leech GB:</label><br>
    <input type="text"  class="form-control" id="FREELEECHGB" name="FREELEECHGB" value="<?php echo Config::TT()['FREELEECHGB'] ?>"><br>
    <label for="HIDEBBCODE">Hide BBcode:</label><br>
    <?php $checked = Config::TT()['HIDEBBCODE'] == 1;
    print("<input name='HIDEBBCODE' value='1' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='HIDEBBCODE' value='0' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>

    </div>
    <div class="col-md-2">

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
    <?php $checked = Config::TT()['RATIOWARNENABLE'] == 1;
    print("<input name='RATIOWARNENABLE' value='1' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='RATIOWARNENABLE' value='0' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="RATIOWARNMINRATIO">Warn Min Ration:</label><br>
    <input type="text"  class="form-control" id="RATIOWARNMINRATIO" name="RATIOWARNMINRATIO" value="<?php echo Config::TT()['RATIOWARNMINRATIO'] ?>"><br>
    <label for="RATIOWARN_MINGIGS">Warn Min Gigs:</label><br>
    <input type="text"  class="form-control" id="RATIOWARN_MINGIGS" name="RATIOWARN_MINGIGS" value="<?php echo Config::TT()['RATIOWARN_MINGIGS'] ?>"><br>
    <label for="RATIOWARN_DAYSTOWARN">Days To Warn:</label><br>
    <input type="text"  class="form-control" id="RATIOWARN_DAYSTOWARN" name="RATIOWARN_DAYSTOWARN" value="<?php echo Config::TT()['RATIOWARN_DAYSTOWARN'] ?>"><br>

    </div>
    <div class="col-md-2">

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
    <?php $checked = Config::TT()['mail_smtp_ssl'] == 1;
    print("<input name='mail_smtp_ssl' value='1' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='mail_smtp_ssl' value='0' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="mail_smtp_auth">SMTP Auth:</label><br>
    <?php $checked = Config::TT()['mail_smtp_auth'] == 1;
    print("<input name='mail_smtp_auth' value='1' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='mail_smtp_auth' value='0' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="mail_smtp_user">SMTP User:</label><br>
    <input type="text"  class="form-control" id="mail_smtp_user" name="mail_smtp_user" value="<?php echo Config::TT()['mail_smtp_user'] ?>"><br>
    <label for="mail_smtp_pass">SMTP Pass:</label><br>
    <input type="text"  class="form-control" id="mail_smtp_pass" name="mail_smtp_pass" value="<?php echo Config::TT()['mail_smtp_pass'] ?>"><br>

    </div>
  </div>
</div>

<center><input type="submit"  class="btn btn-sm ttbtn" value="Submit"></center>

</form>