<?php
class Request
{
    public function __construct()
    {
        $this->session = Auth::user(0, 2);
    }

    public function checks()
    {
        if ($_SESSION["view_torrents"] == "no") {
            Redirect::autolink(URLROOT, Lang::T("NO_PERMISSION_TO_VIEW_AREA"));
        }
        if (!Config::TT()['REQUESTSON']) {
            Redirect::autolink(URLROOT, Lang::T("REQ_OFF"));
        }
    }

    public function index()
    {
        $this->checks();
        $count = DB::run("SELECT count(requests.id) FROM requests inner join categories on requests.cat = categories.id inner join users on requests.userid = users.id")->fetchColumn();;
        list($pagertop, $pagerbottom, $limit) = pager(30, $count, URLROOT . "/request?" . "category=" . $_GET["category"] . "&sort=" . $_GET["sort"] . "&");
        $res = DB::run("SELECT users.downloaded, users.uploaded, users.username, users.privacy, requests.filled, requests.comments,
            requests.filledby, requests.id, requests.userid, requests.request, requests.added, requests.hits, categories.name as cat,
             categories.parent_cat as parent_cat
             FROM requests inner join categories on requests.cat = categories.id inner join users on requests.userid = users.id");
        $num = $res->rowCount();
        $data = [
            'title' => Lang::T('REQUESTS'),
            'pagertop' => $pagertop,
            'pagerbottom' => $pagerbottom,
            'num' => $num,
            'res' => $res,
        ];
        View::render('request/index', $data, 'user');
    }

    public function edit()
    {
        $this->checks();
        // Get Inputs
        $id = (int) Input::get("id");
        if (!Validate::Id($id)) {
            Redirect::autolink(URLROOT . "/request", Lang::T("CP_INVALID_ID"));
        }
        $descr = Input::get("desc");
        $cat = Input::get("cat");
        $filled = Input::get("filled");
        $request = Input::get("request");
        $filledby = Input::get("filledby");
        if (Input::exist()) {
            if (!$filled) {
                DB::run("UPDATE requests SET cat=?, request=?, descr=?, filledby =?, filled=? WHERE id = ?", [$cat, $request, $descr, $filledby, $filled, $id]);
            } else {
                DB::run("UPDATE requests SET cat=?, filledby =?, request=?, descr=?, filled =?  WHERE id =? ", [$cat, $filledby, $request, $descr, $filled, $id]);
            }
            Redirect::to(URLROOT . "/request/reqdetails?id=$id");
        }
        
        $res = DB::run("SELECT * FROM requests WHERE id =$id");
        $data = [
            'title' => Lang::T('REQUESTS'),
            'res' => $res,
        ];
        View::render('request/edit', $data, 'user');
    }

    public function delete()
    {
        $this->checks();
        $delreq = Input::get('delreq');

        if (($_SESSION['class']) > _UPLOADER) {
            if (!$delreq) {
                Redirect::autolink(URLROOT . "/request", Lang::T("NOTHING_SELECTED"));
                die;
            }
            DB::run("DELETE FROM requests WHERE id IN (" . implode(", ", $_POST['delreq']) . ")");
            DB::run("DELETE FROM addedrequests WHERE requestid IN (" . implode(", ", $_POST['delreq']) . ")");
            Redirect::autolink(URLROOT . "/request", Lang::T("_SUCCESS_DEL_"));
        } else {
            // maybe single delte for user
            foreach ($_POST['delreq'] as $del_req) {
                $query = DB::run("SELECT * FROM requests WHERE userid=$_SESSION[id] AND id = $del_req");
                $num = $query->rowCount();
                if ($num > 0) {
                    DB::run("DELETE FROM requests WHERE id IN ($del_req)");
                    DB::run("DELETE FROM addedrequests WHERE requestid IN ($del_req)");
                    Redirect::autolink(URLROOT . "/request", "Request ID $del_req Deleted", URLROOT . "/request");
                } else {
                    Redirect::autolink(URLROOT . "/request", "No Permission to delete Request ID $del_req");
                }
            }
        }
    }

    public function makereq()
    {
        $data = [
            'title' => Lang::T('REQUESTS'),
        ];
        View::render('request/makereq', $data, 'user');
    }

    public function confirmreq()
    {
        if ($_SESSION['class'] < _MODERATOR) {
            Redirect::autolink(URLROOT . "/request/makereq", "Only Moderators can request - For show only");
        }
        $requesttitle = Input::get("requesttitle");
        if (!$requesttitle) {
            Redirect::autolink(URLROOT . "/request/makereq", "You must enter a request!");
        }
        $cat = Input::get("cat");
        if ($cat == 0) {
            Redirect::autolink(URLROOT . "/request/makereq", "Category cannot be empty!");
        }
        $descr = Input::get("descr");
        DB::run("INSERT INTO requests (hits, userid, cat, request, descr, added) VALUES(?,?,?,?,?,?)", [1, $_SESSION['id'], $cat, $requesttitle, $descr, TimeDate::get_date_time()]);
        $id = DB::lastInsertId();
        DB::run("INSERT INTO addedrequests (requestid,userid) VALUES($id, $_SESSION[id])");
        DB::run("INSERT INTO shoutbox (user,message,date,userid) VALUES('System', '$_SESSION[username] has made a request for [url=" . URLROOT . "/request/reqdetails?id=" . $id . "]" . $requesttitle . "[/url]', now(), '0')");
        Logs::write("$requesttitle was added to the Request section");
        Redirect::to(URLROOT . "/request");
    }

    public function reqdetails()
    {
        $id = (int) Input::get("id");
        $res = DB::run("SELECT * FROM requests WHERE id = $id");
        if ($res->rowCount() != 1) {
            Redirect::autolink(URLROOT . "/request", "That request id doesn't exist.");
        }

        $commcount = DB::run("SELECT COUNT(*) FROM comments WHERE req = $id")->fetchColumn();
        if ($commcount) {
            $commquery = "SELECT comments.id, text, user, comments.added, editedby, editedat, avatar, warned, username, title, class, donated FROM comments LEFT JOIN users ON comments.user = users.id WHERE req = $id ORDER BY comments.id";
            $commres = DB::run($commquery);
        } else {
            unset($commres);
        }
        $data = [
            'title' => Lang::T('REQUESTS'),
            'id' => $id,
            'res' => $res,
            'commcount' => $commcount,
            'commres' => $commres,
        ];
        View::render('request/details', $data, 'user');
    }

    public function reqfilled()
    {
        $filledurl = Input::get("filledurl");
        $requestid = (int) Input::get("requestid");
        $res = DB::run("SELECT users.username, requests.userid, requests.request FROM requests inner join users on requests.userid = users.id where requests.id = $requestid");
        $arr = $res->fetch(PDO::FETCH_ASSOC);
        $res2 = DB::run("SELECT username FROM users where id =" . $_SESSION['id']);
        $arr2 = $res2->fetch(PDO::FETCH_ASSOC);
        $msg = "Your request $requestid ";
        $msg2 = "Your Request Filled";
        DB::run("UPDATE requests SET filled = '$filledurl', filledby = $_SESSION[id] WHERE id = $requestid");
        DB::run("INSERT INTO messages (poster, sender, receiver, added, subject, msg) VALUES (?,?,?,?,?,?)", [0, 0, $arr['userid'], TimeDate::get_date_time(), $msg2, $msg]);
        Redirect::autolink(URLROOT . "/request", "Request $requestid was successfully filled with <a href=$filledurl>$filledurl</a>.  User <a href=" . URLROOT . "/profile?id=$arr[userid]><b>$arr[username]</b></a> automatically PMd.  <br>Filled that accidently? No worries, <a href=" . URLROOT . "/request/reqreset?requestid=$requestid>CLICK HERE</a> to mark the request as unfilled.  Do <b>NOT</b> follow this link unless you are sure there is a problem.");
    }

    public function votesview()
    {
        $requestid = (int) Input::get('requestid');
        $res = DB::run("select users.id as userid,users.username, users.downloaded,users.uploaded, requests.id as requestid, requests.request from addedrequests inner join users on addedrequests.userid = users.id inner join requests on addedrequests.requestid = requests.id WHERE addedrequests.requestid =$requestid");
        if (!$res->rowCount() == 0) {
            $data = [
                'title' => Lang::T('REQUESTS'),
                'requestid' => $requestid,
                'res' => $res,
            ];
            View::render('request/voteview', $data, 'user');
        } else {
            Redirect::autolink(URLROOT . "/request", Lang::T('No Votes Yet'));
        }
    }

    public function addvote()
    {
        $requestid = (int) Input::get("id");
        $userid = (int) $_SESSION["id"];
        $res = DB::run("SELECT * FROM addedrequests WHERE requestid=$requestid and userid = $userid");
        $arr = $res->fetch(PDO::FETCH_ASSOC);
        $voted = $arr;
        if ($voted) {
            Redirect::autolink(URLROOT . "/request", 'Youve already voted for this request only 1 vote for each request is allowed');
        } else {
            DB::run("UPDATE requests SET hits = hits + 1 WHERE id=$requestid");
            DB::run("INSERT INTO addedrequests VALUES(0, $requestid, $userid)");
            Redirect::autolink(URLROOT . "/request", "<p>Successfully voted for request $requestid</p><p>Back to <a href=" . URLROOT . "/request><b>requests</b></a></p>");
        }
    }

    public function reqreset()
    {
        $requestid = (int) Input::get("requestid");
        $res = DB::run("SELECT userid, filledby FROM requests WHERE id =$requestid");
        $arr = $res->fetch(PDO::FETCH_ASSOC);
        if (($_SESSION['id'] == $arr['userid']) || ($_SESSION["class"] >= 4) || ($_SESSION['id'] == $arr['filledby'])) {
            DB::run("UPDATE requests SET filled='', filledby=0 WHERE id =$requestid");
            Redirect::autolink(URLROOT . "/request", "Request $requestid successfully reset.");
        } else {
            Redirect::autolink(URLROOT . "/request", "Sorry, cannot reset a request when you are not the owner");
        }
    }

}
