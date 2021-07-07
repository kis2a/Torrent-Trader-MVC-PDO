<?php
class Admincensor extends Controller
{

    public function __construct()
    {
        $this->session = Auth::user(_MODERATOR, 2);
        // $this->userModel = $this->model('User');
        $this->logsModel = $this->model('Logs');
        
    }

    public function index()
    {
        if (OLD_CENSOR) {
            if ($_POST['submit'] == 'Add Censor') {
                DB::run("INSERT INTO censor (word, censor) VALUES (?,?)", [$_POST['word'], $_POST['censor']]);
            }
            if ($_POST['submit'] == 'Delete Censor') {
                DB::run("DELETE FROM censor WHERE word =? LIMIT 1", [$_POST['censor']]);
            }
            $sres = DB::run("SELECT word FROM censor ORDER BY word");
            $data = [
              'title' => Lang::T("Censor"),
              'sres' => $sres,
            ];
            View::render('censor/admin/oldcensor', $data, 'admin');
        } else {
            $to = isset($_GET["to"]) ? htmlentities($_GET["to"]) : $to = '';
            switch ($to) {
                case 'write':
                    if (isset($_POST["badwords"])) {
                        $f = fopen(LOGGER . "/censor.txt", "w+");
                        @fwrite($f, $_POST["badwords"]);
                        fclose($f);
                    }
                    Redirect::autolink(URLROOT . "/admincensor", Lang::T("SUCCESS"), "Censor Updated!");
                    break;
                case '':
                case 'read':
                default:
                    $f = @fopen(LOGGER . "/censor.txt", "r");
                    $badwords = @fread($f, filesize(LOGGER . "/censor.txt"));
                    @fclose($f);
                    $data = [
                      'title' => Lang::T("Censor"),
                      'badwords' => $badwords,
                    ];
                    View::render('censor/admin/newcensor', $data, 'admin');
                    break;
            }
        }
    }
}