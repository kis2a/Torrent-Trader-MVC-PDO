<div class='table-responsive'>
<table class='table table-striped'><thead><tr>
<th><b>Date</b></th>
<th><b>Time</b></th>
<th><b>Size</b></th>
<th><b>Hash</b></th>
<th><b>Download</b></th>
<th><b>Delete</b></th>
</tr></thead><tbody><?php
for ($x = count($Namebk) - 1; $x >= 0; $x--) {
    $data = explode('_', $Namebk[$x]);
    echo ("<tr bgcolor='#CCCCCC'>"); // Start table row
    echo ("<td>" . $data[1] . "</td>"); // Date
    echo ("<td>" . $data[2] . "</td>"); // Time
    echo ("<td>" . $Sizebk[$x] . " KByte</td>"); // Size
    echo ("<td>" . $data[3] . "</td>"); // Hash
    echo ("<td><a href='" . URLROOT . "/backups/" . $Namebk[$x] . ".sql'>SQL</a> - <a href='" . URLROOT . "/backups/" . $Namebk[$x] . ".sql.gz'>GZ</a></td>"); // Download
    echo ("<td><a href='" . URLROOT . "/adminbackupdelete?filename=" . $Namebk[$x] . ".sql'><img src='assets/images/delete.png'></a></td>"); // Delete
    echo ("</tr>"); // End table row
} ?>
</tbody></table>
</div>
<br><br><center><a href='<?php echo URLROOT ?>/adminbackup/submit'>Backup Database</a> (or create a CRON task on <?php echo URLROOT ?>/adminbackup/submit)</center>