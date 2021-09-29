<?php usermenu($data['id']);
if ($data['count']) {
    print($data['pagertop']);
    torrenttable($data['res']);
    print($data['pagerbottom']);
} else {
    print("<br><br><center><b>" . Lang::T("UPLOADED_TORRENTS_ERROR") . "</b></center><br />");
}