<?php
foreach ($data['res'] as $row) {
    if ($row->public == "yes") { ?>
        <div class="row justify-content-center">
            <div class="col-10 border ttborder">
            <center><?php echo $row['title']; ?></center>
            <br>
            <?php echo format_comment($row['text']); ?>
            </div>
        </div><br>
        <?php
    } else if ($row['public'] == "no" && $row['class'] <= $_SESSION["class"]) { ?>
        <div class="row justify-content-center">
            <div class="col-10 border ttborder">
            <center><?php echo $row['title']; ?></center>
            <br>
            <?php echo format_comment($row['text']); ?>
            </div>
        </div><br>
        <?php
    }
}