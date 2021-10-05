<?php
class Shoutbox
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
        <div class='shoutbox_contain'><table class='table table-striped'>
        <?php
        while ($row = $result->fetch(PDO::FETCH_LAZY)) {
            $ol3 = Users::selectAvatar($row["userid"]);
            $av = $ol3['avatar'];
            if (!empty($av)) {
                $av = "<img src='" . $ol3['avatar'] . "' alt='my_avatar' width='20' height='20'>";
            } else {
                $av = "<img src='" . URLROOT . "/assets/images/misc/default_avatar.png' alt='my_avatar' width='20' height='20'>";
            }
            if ($row['userid'] == 0) {
                $av = "<img src='" . URLROOT . "/assets/images/misc/default_avatar.png' alt='default_avatar' width='20' height='20'>";
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
            if (Users::has("edit_users") =="yes") {
                echo "&nbsp<a href='" . URLROOT . "/shoutbox/delete?id=" . $row['msgid'] . "''><i class='fa fa-remove' ></i></a>&nbsp";
                echo "&nbsp<a href='" . URLROOT . "/shoutbox/edit?id=" . $row['msgid'] . "''><i class='fa fa-pencil' ></i></a>&nbsp";
            }
            if (Users::has("edit_users") =="no" && Users::has('username') == $row['user']) {
                $ts = TimeDate::modify('date', $row['date'], "+1 day");
                if ($ts > TT_DATE) {
                echo "&nbsp<a href='" . URLROOT . "/shoutbox/edit?id=$row[msgid]&user=$row[userid]'><i class='fa fa-pencil' ></i></a>&nbsp";
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
        if ($_SESSION["shoutboxpos"] != 'yes' && $_SESSION['loggedin']) {
            //INSERT MESSAGE
            if (!empty(Input::get('message')) && $_SESSION['loggedin'] == true) {
                $message = Input::get('message');
                $row = Shoutboxs::checkFlood($message, $_SESSION['username']);
                if ($row[0] == '0') {
                    Shoutboxs::insertShout($_SESSION['id'], TimeDate::get_date_time(), $_SESSION['username'], $message);
                }
            }
        } else {
            Redirect::autolink(URLROOT, Lang::T("Shoutbox Banned"));
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
            if ($row && ($_SESSION["edit_users"] == "yes" || $_SESSION['username'] == $row[1])) {
                Logs::write("<b><font color='orange'>Shout Deleted:</font> Deleted by   " . $_SESSION['username'] . "</b>");
                Shoutboxs::deleteByShoutId($delete);
            }
        }
        Redirect::to(URLROOT);
    }

    public function edit()
    {
        $user = Input::get('user');
        if ($_SESSION['class'] > _UPLOADER || $_SESSION['id'] == $user) {
            $id = Input::get('id');
            $message = $_POST['message'];
            if ($message) {
                Shoutboxs::updateShout($message, $id);
                Redirect::autolink(URLROOT, Lang::T("Message edited"));
            }
            $edit = Shoutboxs::getByShoutId($id);
            $data = [
                'title' => 'Edit',
                'id' => $edit['msgid'],
                'message' => $edit['message'],
                'user' => $edit['userid'],
            ];
            View::render('shoutbox/edit', $data, 'user');
        } else {
            Redirect::autolink(URLROOT . '/logout', Lang::T("NO_PERMISSION"));
        }
    }

    public function history()
    {
        if (!$_SESSION['loggedin']) {
            Redirect::autolink(URLROOT . '/logout', Lang::T("NO_PERMISSION"));
        }
        $result = Shoutboxs::getAllShouts(80);
        $data = [
            'title' => 'History',
            'sql' => $result,
        ];
        View::render('shoutbox/history', $data, 'user');
    }

}