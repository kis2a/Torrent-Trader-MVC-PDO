<?php
        if ($_GET['type'] == "news") {
            ?>
            <div class="row justify-content-md-center">
            <div class="col-8 border ttborder"><br>
            <?php
            echo htmlspecialchars($data['newstitle']) . "<br /><br />" . format_comment($data['newsbody']) . "<br />";
            ?><br>
            </div>
            </div><br>
            <?php
        }
        if ($_GET['type'] == "torrent") {
            echo torrentmenu($data['id']);
        }
        // Comments
        echo "<center><a href='".URLROOT."/comments/add?type=$data[type]&amp;id=$data[id]'><b>Add Comment</b></a></center><br>";
        
        if ($data['commcount']) {
            commenttable($data['commres'], $data['type']);
            print($data['pagerbottom']);
        } else {
            print("<br><center><b>" . Lang::T("NOCOMMENTS") . "</b></center><br>\n");
        }
        
        echo "<div class='form-group'>";
        echo "<form name='comment' method='post' action=\"comments/take?type=$data[type]&amp;id=$data[id]\">";
        echo textbbcode("comment", "body") . "<br>";
        echo "<center><input type=\"submit\" class=\"btn ttbtn\" value=\"" . Lang::T("ADDCOMMENT") . "\" /></center>";
        echo "</form></div>";