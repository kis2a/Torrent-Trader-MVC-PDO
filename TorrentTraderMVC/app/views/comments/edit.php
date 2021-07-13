<?php
print("<b> " . Lang::T("EDITCOMMENT") . " </b><p>\n");
print("<form method=\"post\" name=\"comment\" action=\"" . URLROOT . "/comments/edit?type=$data[type]&save=1&amp;id=$data[id]\">\n");
print textbbcode("comment", "text", htmlspecialchars($data["text"]));
print("<p><center><input type=\"submit\"  class='btn btn-sm ttbtn'  value=\"Submit Changes\" /></center></p></form>\n");