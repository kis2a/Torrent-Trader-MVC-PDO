<?php
class Style
{
    public function __construct(){
        //$this->db = new DB
    }

    public static function header($title = "")
    {
        // Site online check
        if (!SITE_ONLINE) {
            if ($_SESSION["control_panel"] != "yes") {
                echo '<br /><br /><br /><center>' . stripslashes(OFFLINEMSG) . '</center><br /><br />';
                die;
            } else {
                echo '<br /><br /><br /><center><b><font color="#ff0000">SITE OFFLINE, STAFF ONLY VIEWING! DO NOT LOGOUT</font></b><br />If you logout please edit app/config/config.php and set SITE_ONLINE to true </center><br /><br />';
            }
        }
        if (!$_SESSION['loggedin'] == true) {
            Guest::guestadd();
        }
        if ($title == "") {
            $title = SITENAME;
        } else {
            $title = SITENAME . " : " . htmlspecialchars($title);
        }
        require_once APPROOT . "/views/inc/" . ($_SESSION['stylesheet'] ?: DEFAULTTHEME) . "/header.php";
    }
    
    public static function footer()
    {
        require_once APPROOT . "/views/inc/" . ($_SESSION['stylesheet'] ?: DEFAULTTHEME) . "/footer.php";
    }
    
    public static function begin($caption = "-", $align = "justify")
    {
        $blockId = 'f-' . sha1($caption);
        ?>
        <div class="card">
            <div class="card-header frame-header">
                <?php echo $caption ?>
                <a data-toggle="collapse" href="#" class="showHide" id="<?php echo $blockId; ?>" style="float: right;"></a>
            </div>
            <div class="card-body frame-body slidingDiv<?php echo $blockId; ?>">
        <?php
    }
    
    public static function end()
    {
        ?>
            </div>
        </div>
        <?php
    }


    public static function adminheader($title = "")
    {
        // Site online check
        if (!SITE_ONLINE) {
            if ($_SESSION["control_panel"] != "yes") {
                echo '<br /><br /><br /><center>' . stripslashes(OFFLINEMSG) . '</center><br /><br />';
                die;
            } else {
                echo '<br /><br /><br /><center><b><font color="#ff0000">SITE OFFLINE, STAFF ONLY VIEWING! DO NOT LOGOUT</font></b><br />If you logout please edit app/config/config.php and set SITE_ONLINE to true </center><br /><br />';
            }
        }
        if (!$_SESSION['loggedin'] == true) {
            Guest::guestadd();
        }
        if ($title == "") {
            $title = SITENAME;
        } else {
            $title = SITENAME . " : " . htmlspecialchars($title);
        }
        require_once APPROOT . "/views/admin/header.php";
    }
    
    public static function adminfooter()
    {
        require_once APPROOT . "/views/admin/footer.php";
    }

    public static function adminnavmenu()
    {
        //Get Last Cleanup
        $row = DB::run("SELECT last_time FROM tasks WHERE task =?", ['cleanup'])->fetchColumn();
        if (!$row) {
            $lastclean = "never done...";
        } else {
            $lastclean = TimeDate::get_elapsed_time($row);
        }?><br>
        <div class="card w-100 ">
        <div class="border ttborder">
        <?php
        echo "<center>Last cleanup performed: " . $lastclean . " ago [<a href='" . URLROOT . "/admintask/cleanup'><b>" . Lang::T("FORCE_CLEAN") . "</b></a>]</center>";
        /*
        if (VERSION != "PDO") {
        $file = @file_get_contents('https://www.torrenttradertest.uk/ttversion.php');
        if (VERSION >= $file) {
        echo "<br /><center><b>" . Lang::T("YOU_HAVE_LATEST_VER_INSTALLED") . " VERSION</b></center>";
        } else {
        echo "<br /><center><b><font class='error'>" . Lang::T("NEW_VERSION_OF_TT_NOW_AVAIL") . ": v" . $file . " you have " . VERSION . "<br /> Please visit <a href=http://www.torrenttrader.xyz/>TorrentTrader.xyz</a> to upgrade.</font></b></center>";
        }
        }
         */
        $row = DB::run("SELECT VERSION() AS version")->fetch();
        $mysqlver = $row['version'];
        function apache_version()
        {
            $ver = explode(" ", $_SERVER["SERVER_SOFTWARE"], 3);
            return ($ver[0] . " " . $ver[1]);
        }
        $newstaffmessage = get_row_count("staffmessages", "WHERE answered = '0'");
        echo "<center><b>" . Lang::T("New Staff Messages") . ":</b> <a href='" . URLROOT . "/admincontactstaff/staffbox'><b>($newstaffmessage)</b></a></center>";
        $pending = get_row_count("users", "WHERE status = 'pending' AND invited_by = '0'");
        echo "<center><b>" . Lang::T("USERS_AWAITING_VALIDATION") . ":</b> <a href='" . URLROOT . "/Adminusers/confirm'><b>($pending)</b></a></center>";
        echo "<center>" . Lang::T("VERSION_MYSQL") . ": <b>" . $mysqlver . "</b>&nbsp;-&nbsp;" . Lang::T("VERSION_PHP") . ": <b>" . phpversion() . "</b>&nbsp;-&nbsp;" . Lang::T("Apache Version") . ": <b>" . apache_version() . "</b></center>";
        echo "<center><a href=" . URLROOT . "/admintask/cache><b>Purge Cache</b></a><br></center>";
        echo '</div></div><br>';
    }

    public static function size()
    {
        $size = 8;
        if (!RIGHTNAV || !LEFTNAV) {
            $size = 10;
        }
        if (!RIGHTNAV && !LEFTNAV) {
            $size = 12;
        }
        return $size;
    }

    public static function block_begin($caption = "-", $align = "justify")
    {
        $blockId = 'b-' . sha1($caption);
        ?>
        <div class="card">
            <div class="card-header block-header">
                <?php echo $caption ?>
                <a data-toggle="collapse" href="#" class="showHide" id="<?php echo $blockId; ?>" style="float: right;"></a>
            </div>
            <div class="card-body block-body slidingDiv<?php echo $blockId; ?>">
            <?php
    }

    public static function block_end()
    {
            ?>
            </div>
        </div>
        <?php
    }

    public static function error_header($title = "")
    {
        // Site online check
        if (!SITE_ONLINE) {
            if ($_SESSION["control_panel"] != "yes") {
                echo '<br /><br /><br /><center>' . stripslashes(OFFLINEMSG) . '</center><br /><br />';
                die;
            } else {
                echo '<br /><br /><br /><center><b><font color="#ff0000">SITE OFFLINE, STAFF ONLY VIEWING! DO NOT LOGOUT</font></b><br />If you logout please edit app/config/config.php and set SITE_ONLINE to true </center><br /><br />';
            }
        }
        if (!$_SESSION['loggedin'] == true) {
            Guest::guestadd();
        }
        if ($title == "") {
            $title = SITENAME;
        } else {
            $title = SITENAME . " : " . htmlspecialchars($title);
        }
        require_once APPROOT . "/views/error/error_header.php";
    }
    
    public static function error_footer()
    {
        require_once APPROOT . "/views/error/error_footer.php";
    }
    
}