<?php
foreach  ($data['res'] as $arr) {
    $userid = $arr->user;
    $username = Users::coloredname($arr->username);
    $data = $arr->added;
    $tid = $arr->torrent;
    $nid = $arr->news;
    $title = ($arr->title) ? $arr->title : $arr->name;
    $comentario = stripslashes(format_comment($arr->text));
    $cid = $arr->id;

    $type = 'Torrent: <a href="'.URLROOT.'/torrent?id=' . $tid . '">' . $title . '</a>';
    if ($nid > 0) {
        $type = 'News: <a href="'.URLROOT.'/comments?id=' . $nid . '&amp;type=news">' . $title . '</a>';
    }

    echo "<table class='table_table' align='center' cellspacing='0' width='100%'><tr><th class='table_head' align='center'>" . $type . "</td></tr><tr><td class='table_col2'>" . $comentario . "</th></tr><tr><td class='table_col1' align='center'>Posted in <b>" . $data . "</b> by <a href=\"" . URLROOT . "/users/profile?id=" . $userid . "\">" . $username . "</a><!--  [ <a href=\"edit-/comments?cid=" . $cid . "\">edit</a> | <a href=\"edit-/comments?action=delete&amp;cid=" . $cid . "\">delete</a> ] --></td></tr></table><br />";
}
/*
if ($data['count'] > 10) {
    echo $data['pagerbottom'];
}
*/