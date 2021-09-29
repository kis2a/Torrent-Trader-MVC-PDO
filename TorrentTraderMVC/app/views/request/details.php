<?php
$num = $data['res']->fetch(PDO::FETCH_ASSOC);
$catn = DB::run("SELECT parent_cat,name FROM categories WHERE id='$num[cat]' ");
$catname = $catn->fetch(PDO::FETCH_ASSOC);
$pcat = $catname["parent_cat"];
$ncat = $catname["name"];
$cres = DB::run("SELECT username FROM users WHERE id=$num[userid]");
if ($cres->rowCount() == 1) {
    $carr = $cres->fetch(PDO::FETCH_ASSOC);
}
?>
<a href='<?php echo URLROOT ?>/request'><button  class='btn btn-sm ttbtn'>All Request</button></a>&nbsp;
<a href='<?php echo URLROOT ?>/request?requestorid=<?php echo $_SESSION['id'] ?>'><button  class='btn btn-sm ttbtn'>View my requests</button></a>

<div class="container">
<div class="row justify-content-md-center">
    <div class="col-2">
        <B><?php echo Lang::T('REQUEST') ?>: </B>
    </div>
    <div class="col-4">
        <?php echo $num['request'] ?>
    </div>
</div>
<div class="row justify-content-md-center">
    <div class="col-2">
        <B>Category: </B>
    </div>
    <div class="col-4">
        <?php echo $catname["name"] ?>
    </div>
</div>
    
<?php
if ($num["descr"]) {
    print("<div class='row justify-content-md-center'><div class='col-2'><B>" . Lang::T('COMMENTS') . ": </B></div><div class='col-4'>$num[descr]</div></div>");
}
?>

<div class="row justify-content-md-center">
    <div class="col-2">
        <B><?php echo Lang::T('DATE_ADDED') ?>: </B>
    </div>
    <div class="col-4">
        <?php echo $num['added'] ?>
    </div>
</div>
<div class="row justify-content-md-center">
    <div class="col-2">
        <B>Requested by: </B>
    </div>
    <div class="col-4">
        <?php echo $carr['username'] ?>
    </div>
</div>
    <?php
    if ($num["filled"] == null) {
        print("<div class='row justify-content-md-center'><div class='col-2'><B>" . Lang::T('VOTE_FOR_THIS') . ": </B></div><div class='col-4'><a href=" . URLROOT . "/request/addvote?id=$id><b>" . Lang::T('VOTES') . "</b></a></div></div>");
   
        print("<form method=get action=" . URLROOT . "/request/reqfilled>");
        print("<div class='row justify-content-md-center'><div class='col-2'><B>To Fill This Request:</B> </div><div class='col-4'>Enter the <b>full</b> direct URL of the torrent i.e. http://infamoustracker.org/torrents-details.php?id=134 (just copy/paste from another window/tab) or modify the existing URL to have the correct ID number</div></div>");
        print("</div>");

        print("<div class='text-center'>");
        print("<input type=text size=80 name=filledurl value=TYPE-DIRECT-URL-HERE>\n");
        print("<input type=hidden value=$data[id] name=requestid>");
        print("<button  class='btn btn-sm ttbtn'>Fill Request</button></form>");
        print("</div>");

        print("<center><hr><button  class='btn btn-sm ttbtn'>Add A New Request</button></center>");
    } else {
        print("<div class='row justify-content-md-center'><div class='col-2'><B>URL: </B></div><div class='col-4'><a href=$num[filled] target=_new>$num[filled]</a></div></div>");
        print("</div>");
    }

Style::end();
Style::begin("comments");
if ($data['commcount']) {
    $commentbar = "<p align=center><a class=index href=" . URLROOT . "/comments?type=req&id=$data[id]>Add comment</a></p>\n";
    print($commentbar);
    commenttable($data['commres'], 'req');
} else {
    $commentbar = "<p align=center><a class=index href=" . URLROOT . "/comments/add?id=$data[id]&type=req>Add comment</a></p>\n";
    print($commentbar);
    print("<br /><b>" . Lang::T("NOCOMMENTS") . "</b><br />\n");
}