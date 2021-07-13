<a href='<?php echo URLROOT ?>/request'><button  class='btn btn-sm ttbtn'>All Request</button></a>&nbsp;
<a href='<?php echo URLROOT ?>/request?requestorid=<?php echo $_SESSION['id'] ?>'><button  class='btn btn-sm ttbtn'>View my requests</button></a>
<center><table width=600 border=0 cellspacing=0 cellpadding=3>
<tr><td align=left><B><?php echo Lang::T('REQUEST') ?>: </B></td><td width=70% align=left><?php echo $data['request'] ?></td></tr>
<tr><td align=left><B>Category: </B></td><td width=70% align=left><?php echo $data['ncat'] ?></td></tr>
<?php
if ($data["descr"]) {
    print("<tr><td align=left><B>" . Lang::T('COMMENTS') . ": </B></td><td width=70% align=left>$data[descr]</td></tr>");
}
?>
<tr><td align=left><B><?php echo Lang::T('DATE_ADDED') ?>: </B></td><td width=70% align=left><?php echo $data['added'] ?></td></tr>
<tr><td align=left><B>Requested by: </B></td><td width=70% align=left><?php echo $data['username'] ?></td></tr>
<?php
if ($num["filled"] == null) {
            print("<tr><td align=left><B>" . Lang::T('VOTE_FOR_THIS') . ": </B></td><td width=50% align=left><a href=".URLROOT."/request/addvote?id=$id><b>" . Lang::T('VOTES') . "</b></a></tr></tr>");
            print("<form method=get action=".URLROOT."/request/reqfilled>");
            print("<tr><td align=left><B>To Fill This Request:</B> </td><td>Enter the <b>full</b> direct URL of the torrent i.e. http://infamoustracker.org/torrents-details.php?id=134 (just copy/paste from another window/tab) or modify the existing URL to have the correct ID number</td></tr>");
            print("</table>");
            print("<input type=text size=80 name=filledurl value=TYPE-DIRECT-URL-HERE>\n");
            print("<input type=hidden value=$data[id] name=requestid>");
            print("<button  class='btn btn-sm ttbtn'>Fill Request</button></form>");
            print("<p><hr></p><form method=get action=".URLROOT."/request/makereq#add>Or <button  class='btn btn-sm ttbtn'>Add A New Request</button></form></center>");
} else {
    print("<tr><td align=left><B>URL: </B></td><td width=50% align=left><a href=$data[filled] target=_new>$data[filled]</a></td></tr>");
    print("</table>");
}

Style::end();
Style::begin("comments");
if ($data['commcount']) {
    $commentbar = "<p align=center><a class=index href=".URLROOT."/comments?type=req&id=$data[id]>Add comment</a></p>\n";
    print($commentbar);
    commenttable($data['commres'], 'req');
} else {
    $commentbar = "<p align=center><a class=index href=".URLROOT."/comments/add?id=$data[id]&type=req>Add comment</a></p>\n";
    print($commentbar);
    print("<br /><b>" . Lang::T("NOCOMMENTS") . "</b><br />\n");
}