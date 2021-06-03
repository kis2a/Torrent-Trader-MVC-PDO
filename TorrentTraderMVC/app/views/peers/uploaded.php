<?php usermenu($data['id']);
if ($data['count']) {
    print($pagertop);
    torrenttable($data['res']);
    print($pagerbottom);
} else {
    print("<br><br><center><b>" . Lang::T("UPLOADED_TORRENTS_ERROR") . "</b></center><br />");
}