<?php
echo "<div class='form-group'>";
echo "<form name='comment' method='post' action='".URLROOT."/comments/take?type=$data[type]&amp;id=$data[id]'>";
echo textbbcode("comment", "body") . "<br>";
echo "<center><input type=\"submit\"  value=\"" . Lang::T("ADDCOMMENT") . "\" /></center>";
echo "</form></div>";