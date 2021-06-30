<?php
class Adminbackup extends Controller
{

    public function __construct()
    {
        $this->user = (new Auth)->user(_ADMINISTRATOR, 2);
        // $this->userModel = $this->model('User');
        $this->logsModel = $this->model('Logs');
        $this->valid = new Validation();
    }

    public function index()
    {
        $title = "Back ups";
        require APPROOT . '/views/admin/header.php';
        Style::adminnavmenu();
        Style::begin("Backups");
        $Namebk = array();
        $Sizebk = array();
        // CHECK ALL SQL FILES INTO THE BACKUPS FOLDER AND CREATE AN LIST
        $dir = opendir(BACUP . "/");
        while ($dir && ($file = readdir($dir)) !== false) {
            $ext = explode('.', $file);
            if ($ext[1] == "sql") {
                if ($ext[2] != "gz") {
                    $Namebk[] = $ext[0];
                    $Sizebk[] = round(filesize(BACUP . "/" . $file) / 1024, 2);
                }
            }
        }
        // SORT THE LIST
        sort($Namebk);
        // OPEN TABLE
        echo ("<br/><br/><table style='text-align:center;' width='100%'>");
        // TABLE HEADER
        echo ("<tr bgcolor='#3895D3'>"); // Start table row
        echo ("<th scope='colgroup'><b>Date</b></th>"); // Date
        echo ("<th scope='colgroup'><b>Time</b></th>"); // Time
        echo ("<th scope='colgroup'><b>Size</b></th>"); // Size
        echo ("<th scope='colgroup'><b>Hash</b></th>"); // Hash
        echo ("<th scope='colgroup'><b>Download</b></th>"); // Download
        echo ("<th></th>"); // Delete
        echo ("</tr>"); // End table row
        // TABLE ROWS
        for ($x = count($Namebk) - 1; $x >= 0; $x--) {
            $data = explode('_', $Namebk[$x]);
            echo ("<tr bgcolor='#CCCCCC'>"); // Start table row
            echo ("<td>" . $data[1] . "</td>"); // Date
            echo ("<td>" . $data[2] . "</td>"); // Time
            echo ("<td>" . $Sizebk[$x] . " KByte</td>"); // Size
            echo ("<td>" . $data[3] . "</td>"); // Hash
            echo ("<td><a href='" . URLROOT . "/backups/" . $Namebk[$x] . ".sql'>SQL</a> - <a href='" . URLROOT . "/backups/" . $Namebk[$x] . ".sql.gz'>GZ</a></td>"); // Download
            echo ("<td><a href='" . URLROOT . "/adminbackupdelete?filename=" . $Namebk[$x] . ".sql'><img src='assets/images/delete.png'></a></td>"); // Delete
            echo ("</tr>"); // End table row
        }
        // CLOSE TABLE
        echo ("</table>");
        // CREATE BACKUP LINK
        echo ("<br><br><center><a href='" . URLROOT . "/adminbackup/submit'>Backup Database</a> (or create a CRON task on " . URLROOT . "/adminbackup/submit)</center>");
        Style::end();
        require APPROOT . '/views/admin/footer.php';
    }

    public function backupsdelete()
    {
        $filename = $_GET["filename"];
        $delete_error = false;
        if (!unlink(BACUP . '/' . $filename)) {$delete_error = true;}
        header("Refresh: 3 ;url=" . URLROOT . "/adminbackup");
        $title = "Back up";
        require APPROOT . '/views/admin/header.php';
        show_error_msg(Lang::T("SUCCESS"), "Selected Backup Files deleted", 0);
        if ($delete_error) {
            echo ("<br><center><b>Has encountered a problem during the deletion</b></center><br><br><br>");
        } else {
            echo ("<br><center><b>$filename<br><br><br>DELETED !!!</b></center><br><br><br>");
        }
        echo ("<center>You'll be redirected in about 3 secs. If not, click <a href='/adminbackup'>here</a></center>");
        require APPROOT . '/views/admin/footer.php';
    }

    public function submit()
    {
        $DBH = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . "; charset=utf8", DB_USER, DB_PASS);
        //put table names you want backed up in this array or leave empty to do all
        $tables = array();
        $this->backup_tables($DBH, $tables);
    }

    private function backup_tables($DBH, $tables)
    {
        $DBH->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_NATURAL);
        //Script Variables
        $compression = false;
        $BACKUP_PATH = BACUP . "/";
        $nowtimename = time();
        //create/open files
        if ($compression) {
            $zp = gzopen($BACKUP_PATH . $nowtimename . '.sql.gz', "a9");
        } else {
            $handle = fopen($BACKUP_PATH . $nowtimename . '.sql', 'a+');
        }
        //array of all database field types which just take numbers
        $numtypes = array('tinyint', 'smallint', 'mediumint', 'int', 'bigint', 'float', 'double', 'decimal', 'real');
        //get all of the tables
        if (empty($tables)) {
            $pstm1 = $DBH->query('SHOW TABLES');
            while ($row = $pstm1->fetch(PDO::FETCH_NUM)) {
                $tables[] = $row[0];
            }
        } else {
            $tables = is_array($tables) ? $tables : explode(',', $tables);
        }
        //cycle through the table(s)
        foreach ($tables as $table) {
            $result = $DBH->query("SELECT * FROM $table");
            $num_fields = $result->columnCount();
            $num_rows = $result->rowCount();
            $return = "";
            //uncomment below if you want 'DROP TABLE IF EXISTS' displayed
            //$return.= 'DROP TABLE IF EXISTS `'.$table.'`;';
            //table structure
            $pstm2 = $DBH->query("SHOW CREATE TABLE $table");
            $row2 = $pstm2->fetch(PDO::FETCH_NUM);
            $ifnotexists = str_replace('CREATE TABLE', 'CREATE TABLE IF NOT EXISTS', $row2[1]);
            $return .= "\n\n" . $ifnotexists . ";\n\n";
            if ($compression) {
                gzwrite($zp, $return);
            } else {
                fwrite($handle, $return);
            }
            $return = "";
            //insert values
            if ($num_rows) {
                $return = 'INSERT INTO `' . $table . '` (';
                $pstm3 = $DBH->query("SHOW COLUMNS FROM $table");
                $count = 0;
                $type = array();
                while ($rows = $pstm3->fetch(PDO::FETCH_NUM)) {
                    if (stripos($rows[1], '(')) {
                        $type[$table][] = stristr($rows[1], '(', true);
                    } else {
                        $type[$table][] = $rows[1];
                    }
                    $return .= "`" . $rows[0] . "`";
                    $count++;
                    if ($count < ($pstm3->rowCount())) {
                        $return .= ", ";
                    }
                }
                $return .= ")" . ' VALUES';
                if ($compression) {
                    gzwrite($zp, $return);
                } else {
                    fwrite($handle, $return);
                }
                $return = "";
            }
            $count = 0;
            while ($row = $result->fetch(PDO::FETCH_NUM)) {
                $return = "\n\t(";
                for ($j = 0; $j < $num_fields; $j++) {
                    //$row[$j] = preg_replace("\n","\\n",$row[$j]);
                    if (isset($row[$j])) {
                        //if number, take away "". else leave as string
                        if ((in_array($type[$table][$j], $numtypes)) && (!empty($row[$j]))) {
                            $return .= $row[$j];
                        } else {
                            $return .= $DBH->quote($row[$j]);
                        }
                    } else {
                        $return .= 'NULL';
                    }
                    if ($j < ($num_fields - 1)) {
                        $return .= ',';
                    }
                }
                $count++;
                if ($count < ($result->rowCount())) {
                    $return .= "),";
                } else {
                    $return .= ");";
                }
                if ($compression) {
                    gzwrite($zp, $return);
                } else {
                    fwrite($handle, $return);
                }
                $return = "";
            }
            $return = "\n\n-- ------------------------------------------------ \n\n";
            if ($compression) {
                gzwrite($zp, $return);
            } else {
                fwrite($handle, $return);
            }
            $return = "";
        }

        $error1 = $pstm2->errorInfo();
        $error2 = $pstm3->errorInfo();
        $error3 = $result->errorInfo();
        echo $error1[2];
        echo $error2[2];
        echo $error3[2];

        if ($compression) {
            gzclose($zp);
        } else {
            fclose($handle);
        }
    }
}