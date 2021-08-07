<?php
forumheader('search'); 
?>
<div class="row justify-content-md-center">
    <div class="col-6 border ttborder">
    <form method='get' action='<?php echo URLROOT; ?>/forums/result'>
        <center>
        <?php echo Lang::T("SEARCH") ?>:<br><br>
        <input type='text' size='40' name='keywords' /><br /><br>
        <button type='submit' class='btn btn-sm ttbtn' value='Search'>Search Topics</button>&nbsp;&nbsp;
        <button  type='Submit' class='btn btn-sm ttbtn' name='type' value='deep'>Search Posts</button><br><br>
        </center>
    </form>
    </div>
</div>
<?php
print("<br><p>Search Phrase: <b>" . htmlspecialchars($data['keywords']) . "</b></p>\n");

print("<p><center><div class='table'><table class='table table-striped'><thead>");
print("<th align='left'>Topic Subject</th>
  <th align='left'>Forum</th>
  <th align='left'>Added</th>
  <th align='left'>Posted By</th>
  </tr></thead>");

foreach ($data['res'] as $row) {
$res2 = DB::run("SELECT name,minclassread, guest_read FROM forum_forums WHERE id=$row[forumid]");
$forum = $res2->fetch(PDO::FETCH_ASSOC);
if ($forum["name"] == "" || ($forum["minclassread"] > $_SESSION["class"] && $forum["guest_read"] == "no")) {
    continue;
}

$res2 = DB::run("SELECT username FROM users WHERE id=$row[userid]");
$user = $res2->fetch(PDO::FETCH_ASSOC);
if ($user["username"] == "") {
$user["username"] = "Deluser";
}

print("<tr>");
print("<td align='left' width='15%'><a href='".URLROOT."/forums/viewtopic&topicid=$row[topicid]'>$row[subject]</a></td>");
print("<td align='left' width='15%'><a href='".URLROOT."/forums/viewforum&forumid=$row[forumid]'>$forum[name]</a></td>");
print("<td align='left' width='10%'>$row[added]</td>");
print("<td align='left' width='10%'><a href='".URLROOT."/profile?id=".$row['userid']."'>".Users::coloredname($user['username'])."</a></td>");
print("</tr>");
}

print("</table></div></center></p>\n");
print("$data[pagerbottom]\n");
echo "&nbsp<center><a href='" . URLROOT . "/forums/search' class='btn ttbtn''>Search Again</a></center>&nbsp";