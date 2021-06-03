<?php
class Adminclient extends Controller
{

    public function __construct()
    {
        Auth::user();
        Auth::isStaff();
        // $this->userModel = $this->model('User');
    }

    public function index()
    {
        if (isset($_POST['ban'])) {
            DB::run("INSERT INTO clients (agent_name, hits, ins_date) VALUES (?,?,?)", [$_POST['ban'], 1, TimeDate::get_date_time()]);
        }
        $res11 = DB::run("SELECT client, peer_id FROM peers GROUP BY client");
        $data = [
            'title' => Lang::T("Clients"),
            'res11' => $res11,
        ];
        $this->view('client/index', $data, 'admin');
    }

    public function banned()
    {
        if (isset($_POST['unban'])) {
            foreach ($_POST['unban'] as $deleteid) {
                DB::run("DELETE FROM clients WHERE agent_id=?", [$deleteid]);
            }
        }

        $sql = DB::run("SELECT * FROM clients")->fetchAll(PDO::FETCH_ASSOC);
        $data = [
            'title' => Lang::T("Clients"),
            'sql' => $sql,
        ];
        $this->view('client/banned', $data, 'admin');
    }
}