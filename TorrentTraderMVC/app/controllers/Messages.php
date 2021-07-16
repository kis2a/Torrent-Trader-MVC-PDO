<?php
class Messages extends Controller
{

    public function __construct()
    {
        $this->session = Auth::user(0, 2);
    }

    public function overview()
    {
        $arr = Message::countmsg();
        $data = [
            'title' => Lang::T('MY_MESSAGES'),
            'inbox' => $arr['inbox'],
            'unread' => $arr['unread'],
            'outbox' => $arr['outbox'],
            'draft' => $arr['draft'],
            'template' => $arr['template'],
        ];
        View::render('message/overview', $data, 'user');
    }

    public function create()
    {
        $id = (int) Input::get('id');
        $data = [
            'title' => Lang::T('ACCOUNT_SEND_MSG'),
            'id' => $id,
        ];
        View::render('message/create', $data, 'user');
    }

    public function submit()
    {
        $receiver = Input::get('receiver');
        $subject = Input::get('subject');
        $body = Input::get('body');
        if ($body == "") {
            Redirect::autolink(URLROOT . "/messages/overview", Lang::T('EMPTY_BODY'));
        }
        if ($receiver == "") {
            Redirect::autolink(URLROOT . "/messages/overview", Lang::T('EMPTY_RECEIVER'));
        }
        if ($subject == "") {
            Redirect::autolink(URLROOT . "/messages/overview", Lang::T('EMPTY_SUBJECT'));
        }
        $receiver = Users::getIdByUsername($receiver);
        // Button Switch
        $this->insertbytype($_REQUEST['Update'], $receiver['id'], $subject, $body);
    }

    public function insertbytype($type, $receiver, $subject, $body)
    {
        switch ($type) {
            case 'create':
                if (isset($_POST['save'])) {
                    Message::insertmessage($_SESSION['id'], $receiver, TimeDate::get_date_time(), $subject, $body, 'yes', 'both');
                } else {
                    Message::insertmessage($_SESSION['id'], $receiver, TimeDate::get_date_time(), $subject, $body, 'yes', 'in');
                }
                Redirect::autolink(URLROOT . "/messages?type=outbox", Lang::T('MESSAGES_SENT'));
                break;
            case 'draft':
                Message::insertmessage($_SESSION['id'], $receiver, TimeDate::get_date_time(), $subject, $body, 'no', 'draft');
                Redirect::autolink(URLROOT . "/messages?type=draft", Lang::T('SAVED_DRAFT'));
                break;
            case 'template':
                Message::insertmessage($_SESSION['id'], $receiver, TimeDate::get_date_time(), $subject, $body, 'no', 'template');
                Redirect::autolink(URLROOT . "/messages?type=templates", Lang::T('SAVED_TEMPLATE'));
                break;
        }
    }

    public function read()
    {
        // Get Message Id from url
        $id = (int) Input::get('id');
        // Get Page from url
        $type = isset($_GET['type']) ? $_GET['type'] : null;
        // Set button condition
        if ($type == 'templates' || $type == 'draft') {
            $button = "<a href='" . URLROOT . "/messages/update?type=$type&id=$id'><button  class='btn btn-sm btn-success'>Edit</button></a>";
        } elseif ($type == 'inbox' || $type == 'outbox') {
            $button = " <a href='" . URLROOT . "/messages/reply?type=$type&id=$id'><button  class='btn btn-sm btn-success'>Reply</button></a>
                        <a href='" . URLROOT . "/messages/update?type=$type&id=$id'><button  class='btn btn-sm btn-success'>Edit</button></a>";
        }
        // get row
        $res = DB::run("SELECT * FROM messages WHERE id=$id");
        $arr = $res->fetch(PDO::FETCH_ASSOC);
        // check if user involved in conversation
        if ($arr["sender"] != $_SESSION['id'] && $arr["receiver"] != $_SESSION['id']) {
            Redirect::autolink(URLROOT, Lang::T('NO_PERMISSION'));
        }
        // mark read
        if ($arr["unread"] == "yes" && $arr["receiver"] == $_SESSION['id']) {
            Message::updateRead($arr['id'], $_SESSION['id']);
        }
        $data = [
            'title' => Lang::T('MESSAGE'),
            'id' => $id,
            'button' => $button,
            'sender' => $arr['sender'],
            'subject' => $arr['subject'],
            'added' => $arr['added'],
            'msg' => $arr['msg'],
        ];
        View::render('message/read', $data, 'user');
    }

    public function reply()
    {
        // Get Stuff from URL
        $url_id = isset($_GET['id']) ? $_GET['id'] : null;
        $type = isset($_GET['type']) ? $_GET['type'] : null;

        $row = Message::getallmsg($url_id);
        if ($type == 'inbox') {
            $arr2 = DB::run("SELECT username,id FROM users WHERE id=?", [$row['sender']])->fetch(PDO::FETCH_LAZY);
        } else {
            $arr2 = DB::run("SELECT username,id FROM users WHERE id=?", [$row['receiver']])->fetch(PDO::FETCH_LAZY);
        }
        // check if user involved in conversation
        if ($row["sender"] != $_SESSION['id'] && $row["receiver"] != $_SESSION['id']) {
            Redirect::autolink(URLROOT, Lang::T('NO_PERMISSION'));
        }
        $username = $arr2["username"];
        $msg = $row['msg'];

        $data = [
            'username' => $username,
            'userid' => $arr2['id'],
            'msg' => $msg,
            'subject' => $row['subject'],
            'id' => $row['id'],
        ];
        View::render('message/reply', $data, 'user');
    }

    public function update()
    {
        $url_id = $_GET['id'];
        $row = Message::getallmsg($url_id);
        // check if user involved in conversation
        if ($row["sender"] != $_SESSION['id'] && $row["receiver"] != $_SESSION['id']) {
            Redirect::autolink(URLROOT, Lang::T('NO_PERMISSION'));
        }
        if (!$row) {
            Redirect::autolink(URLROOT . '/messages?type=inbox', Lang::T("INVALID_ID"));
        }
        // Submit edit
        if (isset($_GET['id'])) {
            if (!empty($_POST)) {
                $id = isset($_GET['id']) ? $_GET['id'] : null;
                $msg = isset($_POST['msg']) ? $_POST['msg'] : '';
                // Update the record
                Message::updateMessage($msg, $id);
                Redirect::autolink(URLROOT . '/messages?type=inbox', "Edited Successfully !");
            }
        }

        $data = [
            'title' => 'Edit Message',
            'msg' => $row['msg'],
            'subject' => $row['subject'],
            'id' => $row['id'],
        ];
        View::render('message/edit', $data, 'user');
    }

    public function index()
    {

        $type = isset($_GET['type']) ? $_GET['type'] : null;

        // Get page array
        $arr = Message::msgPagination($type);
        // Now pass to sql and view
        $res = DB::run("SELECT * FROM messages WHERE $arr[where] $arr[limit]");
        $data = [
            'title' => $type,
            'pagename' => $arr['pagename'],
            'pagerbottom' => $arr['pagerbottom'],
            'mainsql' => $res,
        ];
        View::render('message/index', $data, 'user');
    }

    public function delete()
    {
        $type = isset($_GET['type']) ? $_GET['type'] : null;

        if ($_POST["read"]) {
            //var_dump($_POST);   die();
            if (!isset($_POST["del"])) {
                Redirect::autolink(URLROOT . "/messages?type=$type", Lang::T("NOTHING_SELECTED"));
            }
            $ids = array_map("intval", $_POST["del"]);
            $ids = implode(", ", $ids);
            DB::run("UPDATE messages SET `unread` = 'no' WHERE `id` IN ($ids)");
            Redirect::autolink(URLROOT . "/messages?type=$type", Lang::T("COMPLETED"));
        } else {
            if (!isset($_POST["del"])) {
                Redirect::autolink(URLROOT . "/messages?type=$type", Lang::T("NOTHING_SELECTED"));
            }
            $ids = array_map("intval", $_POST["del"]);
            $ids = implode(", ", $ids);
            if ($type == 'inboxbox') {
                DB::run("DELETE FROM messages WHERE `location` = 'in' AND `receiver` = $_SESSION[id] AND `id` IN ($ids)");
                DB::run("UPDATE messages SET `location` = 'out' WHERE `location` = 'both' AND `receiver` = $_SESSION[id] AND `id` IN ($ids)");
            } elseif ($type == 'outbox') {
                DB::run("UPDATE messages SET `location` = 'in' WHERE `location` = 'both' AND `sender` = $_SESSION[id] AND `id` IN ($ids)");
                DB::run("DELETE FROM messages WHERE `location` IN ('out', 'draft', 'template') AND `sender` = $_SESSION[id] AND `id` IN ($ids)");
            } elseif ($type == 'templates') {
                DB::run("DELETE FROM messages WHERE `sender` = $_SESSION[id] AND `location` = 'template' AND `id` IN ($ids)");
            } elseif ($type == 'draft') {
                DB::run("DELETE FROM messages WHERE `sender` = $_SESSION[id] AND `location` = 'draft' AND `id` IN ($ids)");
            }
            Redirect::autolink(URLROOT . "/messages?type=$type", Lang::T("COMPLETED"));
            die;
        }
    }
}
