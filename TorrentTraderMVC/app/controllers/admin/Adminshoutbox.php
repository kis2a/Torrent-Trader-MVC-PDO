<?php
class Adminshoutbox
{

    public function __construct()
    {
        $this->session = Auth::user(_MODERATOR, 2);
    }

    public function history()
    {
        $result = DB::run("SELECT * FROM shoutbox WHERE staff = 1 ORDER BY msgid DESC LIMIT 80");
        $data = [
            'title' => 'Staff History',
            'sql' => $result,
        ];
        View::render('shoutbox/history', $data, 'admin');
    }

    public function index()
    {
        $data = [
            'title' => 'Staff Chat',
        ];
        View::render('shoutbox/admin/staffbox', $data, 'admin');
    }

    public function loadchat()
    {
        $query = 'SELECT * FROM shoutbox WHERE staff = 1 ORDER BY msgid DESC LIMIT 20';
        $result = DB::run($query);
        ?>
        <div class='shoutbox_contain'><table class='table table-striped'>
        <tr>
        <?php
        while ($row = $result->fetch(PDO::FETCH_LAZY)) {
            $ol3 = Users::selectAvatar($row["userid"]);
            $av = $ol3['avatar'];
            if (!empty($av)) {
                $av = "<img src='" . $ol3['avatar'] . "' alt='my_avatar' width='20' height='20'>";
            } else {
                $av = "<img src='" . URLROOT . "/assets/images/default_avatar.png' alt='my_avatar' width='20' height='20'>";
            }
            if ($row['userid'] == 0) {
                $av = "<img src='" . URLROOT . "/assets/images/default_avatar.png' alt='default_avatar' width='20' height='20'>";
            }
            ?>
            
            <tr>
            <td class="shouttable">
            <small class="pull-left time d-none d-sm-block" style="width:99px;font-size:11px"><i class="fa fa-clock-o"></i>&nbsp;<?php echo date('jS M,  g:ia', TimeDate::utc_to_tz_time($row['date'])); ?></small>
            </td>
            <td class="shouttable">
            <a class="pull-left d-none d-sm-block" href="#"><?php echo $av ?></a>
            </td>
            <td class="shouttable">
            <a class="pull-left" href="<?php echo URLROOT ?>/profile?id=<?php echo $row['userid'] ?>" target="_parent">
            <b><?php echo Users::coloredname($row['user']) ?>:</b></a>&nbsp;
            <?php echo nl2br(format_comment($row['message'])); ?>
            <?php
            if ($_SESSION["edit_users"]=="yes") {
                echo "&nbsp<a href='" . URLROOT . "/shoutbox/delete?id=" . $row['msgid'] . "''><i class='fa fa-remove' aria-hidden='true'></i></a>&nbsp";
                echo "&nbsp<a href='" . URLROOT . "/shoutbox/edit?id=" . $row['msgid'] . "''><i class='fa fa-pencil' aria-hidden='true'></i></a>&nbsp";
            }
            if ($_SESSION["edit_users"]=="no" && $_SESSION['username'] == $row['user']) {
                $ts = TimeDate::modify('date', $row['date'], "+1 day");
                if ($ts > TT_DATE) {
                echo "&nbsp<a href='" . URLROOT . "/shoutbox/edit?id=$row[msgid]&user=$row[userid]'><i class='fa fa-pencil' aria-hidden='true'></i></a>&nbsp";
                }
            } ?>
            </td>
            </tr>
            
            <?php 
        } ?>
        
        </table></div>
        <?php
    }

    public function add()
    {
        if ($_SESSION["shoutboxpos"] == 'no') {
            //INSERT MESSAGE
            if (!empty($_POST['message']) && $_SESSION['loggedin'] == true) {
                $_POST['message'] = $_POST['message'];
                $result = DB::run("SELECT COUNT(*) FROM shoutbox WHERE message=? AND user=? AND UNIX_TIMESTAMP(?)-UNIX_TIMESTAMP(date) < ?", [$_POST['message'], $_SESSION['username'], TimeDate::get_date_time(), 30]);
                $row = $result->fetch(PDO::FETCH_LAZY);
                if ($row[0] == '0') {
                    $qry = DB::run("INSERT INTO shoutbox (msgid, user, message, date, userid, staff) VALUES (?, ?, ?, ?, ?, ?)", [null, $_SESSION['username'], $_POST['message'], TimeDate::get_date_time(), $_SESSION['id'], 1]);
                }
            }
        }
        Redirect::to(URLROOT . '/adminshoutbox');
    }

    public function clear()
    {
        $do = $_GET['do'];
        if ($do == "delete") {
            DB::run("TRUNCATE TABLE `shoutbox`");
            Logs::write("Shoutbox cleared by $_SESSION[username]");
            $msg_shout = "[color=#ff0000]" . Lang::T("SHOUTBOX_CLEARED_MESSAGE") . "[/color]";
            DB::run("INSERT INTO shoutbox (userid, date, user, message) VALUES(?,?,?,?)", [0, TimeDate::get_date_time(), 'System', $msg_shout]);
            Redirect::autolink(URLROOT . "/admincp", "<b><font color='#ff0000'>Shoutbox Cleared....</font></b>");
        }
        $data = [
            'title' => Lang::T("CLEAR_SHOUTBOX"),
        ];
        View::render('shoutbox/admin/clear', $data, 'admin');
    }

}