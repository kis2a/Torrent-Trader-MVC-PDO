<?php
class Shoutbox extends Controller
{
    public function __construct()
    {
        $this->session = Auth::user(0, 0);
    }

    public function index()
    {
        Redirect::to(URLROOT);
    }

    public function chat()
    {
        $result = Shoutboxs::getAllShouts();
        ?>
        <div class="shoutbox_contain">
        <div class="msg-wrap">
        <?php
        while ($row = $result->fetch(PDO::FETCH_LAZY)) {
            $ol3 = Users::selectAvatar($row["userid"]);
            //$ol3 = DB::run("SELECT avatar FROM users WHERE id=" . $row["userid"])->fetch(PDO::FETCH_ASSOC);
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
            if ($this->session['class'] > _UPLOADER) {
                echo "&nbsp<a href='" . URLROOT . "/shoutbox/delete?id=" . $row['msgid'] . "''><i class='fa fa-remove' aria-hidden='true'></i></a>&nbsp";
                ?>
                <!-- Trigger/Open The Model -->
                <a data-toggle="modal" data-target="#editshout-<?=$row['msgid'];?>">
                    <i class='fa fa-pencil' aria-hidden='true'></i>
                </a>
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
        }?>
        </div>
        </div>
        <?php
    }

    public function add()
    {
        if ($this->session["shoutboxpos"] == 'no') {
            //INSERT MESSAGE
            if (!empty(Input::get('message')) && $_SESSION['loggedin'] == true) {
                $message = Input::get('message');
                $row = Shoutboxs::checkFlood($message, $this->session['username']);
                if ($row[0] == '0') {
                    Shoutboxs::insertShout($this->session['id'], TimeDate::get_date_time(), $this->session['username'], $message);
                }
            }
        }
        Redirect::to(URLROOT);
    }

    public function delete()
    {
        $delete = Input::get('id');
        if ($delete) {
            if (is_numeric($delete)) {
                $row = Shoutboxs::getByShoutId($delete);
            } else {
                echo "Failed to delete, invalid msg id";
                exit;
            }
            if ($row && ($this->session["edit_users"] == "yes" || $this->session['username'] == $row[1])) {
                Logs::write("<b><font color='orange'>Shout Deleted:</font> Deleted by   " . $this->session['username'] . "</b>");
                Shoutboxs::deleteByShoutId($delete);
            }
        }
        Redirect::to(URLROOT);
    }

    public function edit()
    {
        if ($this->session['class'] > _UPLOADER) {
            $id = Input::get('id');
            $message = $_POST['message'];
            if ($message) {
                Shoutboxs::updateShout($message, $id);
                Redirect::autolink(URLROOT, Lang::T("Message edited"));
            }
        } else {
            Redirect::autolink(URLROOT . '/logout', Lang::T("You do not have permission"));
        }
    }

    public function history()
    {
        $result = Shoutboxs::getAllShouts(80);
        $data = [
            'title' => 'History',
            'sql' => $result,
        ];
        View::render('shoutbox/history', $data, 'user');
    }

}