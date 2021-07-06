<?php
forumheader('search');
print("<br><p>Search Phrase: <b>" . htmlspecialchars($data['keywords']) . "</b></p>\n");
echo $data['max'];

print("<p><center><div class='table'><table class='table table-striped'><thead>");
print("<tr><th>Post ID</th><th align='left'>Topic</th><th align='left'>Forum</th><th align='left'>Posted by</th></tr></tbody>");
for ($i = 0; $i < $data['num']; ++$i) {
    $post = $data['res']->fetch(PDO::FETCH_ASSOC);
    $res2 = DB::run("SELECT forumid, subject FROM forum_topics WHERE id=$post[topicid]");
    $topic = $res2->fetch(PDO::FETCH_ASSOC);
    $res2 = DB::run("SELECT name,minclassread, guest_read FROM forum_forums WHERE id=$topic[forumid]");
    $forum = $res2->fetch(PDO::FETCH_ASSOC);
    if ($forum["name"] == "" || ($forum["minclassread"] > $_SESSION["class"] && $forum["guest_read"] == "no")) {
        continue;
    }
    $res2 = DB::run("SELECT username FROM users WHERE id=$post[userid]");
    $user = $res2->fetch(PDO::FETCH_ASSOC);
    if ($user["username"] == "") {
        $user["username"] = "Deluser";
    }
    print("<tr><td>$post[id]</td><td align='left'><a href='" . URLROOT . "/forums/viewtopic&amp;topicid=$post[topicid]#post$post[id]'><b>" . htmlspecialchars($topic["subject"]) . "</b></a></td><td align='left'><a href='" . URLROOT . "/forums/viewforum&amp;forumid=$topic[forumid]'><b>" . htmlspecialchars($forum["name"]) . "</b></a></td><td align='left'><a href='" . URLROOT . "/profile?id=$post[userid]'><b>$user[username]</b></a><br />at " . TimeDate::utc_to_tz($post["added"]) . "</td></tr>\n");
}
print("</table></div></center></p>\n");
echo "&nbsp<a href='" . URLROOT . "/forums/search' class='btn btn-warning''>Search Again</a>&nbsp";