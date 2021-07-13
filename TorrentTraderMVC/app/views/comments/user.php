<?php
$commcount = DB::run("SELECT COUNT(*) FROM comments WHERE user =?", [$data['id']])->fetchColumn();
if ($commcount) {
    list($pagertop, $pagerbottom, $limit) = pager(10, $commcount, URLROOT."/comments/user?id=$data[id]&");
    $commres = DB::run("SELECT comments.id, text, user, comments.added, avatar, signature, username, title, class, uploaded, downloaded, privacy, donated FROM comments LEFT JOIN users ON comments.user = users.id WHERE user = $data[id] ORDER BY comments.id $limit"); // $limit
} else {
    unset($commres);
}
if ($commcount) {
    print($pagertop);
    commenttable($commres);
    print($pagerbottom);
} else {
    print("<br><b>" . Lang::T("NOCOMMENTS") . "</b><br>\n");
}