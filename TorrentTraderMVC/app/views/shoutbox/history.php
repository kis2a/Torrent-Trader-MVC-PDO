<div class="shoutbox_contain h-100">
    <div class="msg-wrap">
    <?php
    while ($row = $data['sql']->fetch(PDO::FETCH_LAZY)) {
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
            <small class="pull-left time d-none d-sm-block"><i class="fa fa-clock-o"></i>&nbsp;<?php echo date('jS M,  g:ia', TimeDate::utc_to_tz_time($row['date'])); ?></small>&nbsp;
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
                        <center><input class="btn btn-sm ttbtn"  type="submit" value='<?php echo Lang::T("SUBMIT"); ?>'></center>
                        </form>
                    </div>
                </div>
                <?php
            } ?>
            <div class="media-body">
                <small class="col-lg-10"><?php echo nl2br(format_comment($row['message'])); ?></small>
            </div>
        </div>
        <?php
    } ?>
    </div>
</div>