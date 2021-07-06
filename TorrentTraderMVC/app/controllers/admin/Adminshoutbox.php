<?php
class Adminshoutbox extends Controller
{

    public function __construct()
    {
        $this->session = (new Auth)->user(_MODERATOR, 2);
        // $this->userModel = $this->model('User');
        $this->logsModel = $this->model('Logs');
        $this->valid = new Validation();
    }

    public function history()
    {
        $result = DB::run("SELECT * FROM shoutbox WHERE staff = 1 ORDER BY msgid DESC LIMIT 80");
        $data = [
            'title' => 'Staff History',
            'sql' => $result,
        ];
        $this->view('shoutbox/history', $data, 'admin');
    }

    public function index()
    {
        $data = [
            'title' => 'Staff Chat',
        ];
        $this->view('shoutbox/admin/staffbox', $data, 'admin');
    }

    public function loadchat()
    {
        $query = 'SELECT * FROM shoutbox WHERE staff = 1 ORDER BY msgid DESC LIMIT 20';
        $result = DB::run($query);
        ?>
        <div class="shoutbox_contain">
            <div class="msg-wrap">
            <?php
            while ($row = $result->fetch(PDO::FETCH_LAZY)) {
                $ol3 = DB::run("SELECT avatar FROM users WHERE id=" . $row["userid"])->fetch(PDO::FETCH_ASSOC);
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
                <div class="media msg ">
                    <a class="pull-left d-none d-sm-block" href="#">
                    <?php echo $av ?>
                    <a class="pull-left" href="<?php echo URLROOT ?>/profile?id=<?php echo $row['userid'] ?>" target="_parent">
                    <b><?php echo Users::coloredname($row['user']) ?>:</b></a>
                    </a>
                    <?php
                    if ($_SESSION['class'] > _UPLOADER) {
                        echo "&nbsp<a href='" . URLROOT . "/shoutbox/delete?id=" . $row['msgid'] . "''><i class='fa fa-remove' aria-hidden='true'></i></a>&nbsp";
                    ?>
                    <!-- Trigger/Open The Model -->
                    <a data-toggle="modal" data-target="#editshout-<?=$row['msgid'];?>"><i class='fa fa-pencil' aria-hidden='true'></i></a>
                    <!-- The Modal -->
                    <div id="editshout-<?=$row['msgid'];?>" class="modal">
                    <!-- Modal content -->
                    <div class="modal-content">
                        <!-- Close Modal -->
                        <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal"><b>CLOSE</b></button><br>
                        <!-- The Message to edit -->
                        <form method="POST" action="<?php echo URLROOT; ?>/shoutbox/edit?id=<?php echo $row['msgid']; ?>">
                        <input class="form-control" type="text" name="message" value="<?php echo $row['message'] ?>" size="60" /><br>
                        <!-- The submit button -->
                        <center><input class="btn btn-sm btn-warning"  type="submit" value='<?php echo Lang::T("SUBMIT"); ?>'></center>
                        </form>
                    </div>
                    </div>
                    <?php
                    }?>
                    <div class="media-body">
                        <small class="pull-right time d-none d-sm-block"><i class="fa fa-clock-o"></i>&nbsp;<?php echo date('jS M,  g:ia', TimeDate::utc_to_tz_time($row['date'])); ?></small>
                        <small class="col-lg-10"><?php echo nl2br(format_comment($row['message'])); ?></small>
                    </div>
                </div>
            <?php
            } ?>
           </div>
       </div> <?php
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
        $this->view('shoutbox/admin/clear', $data, 'admin');
    }
}