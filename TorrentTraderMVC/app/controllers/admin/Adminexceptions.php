<?php
class Adminexceptions extends Controller
{
    public function __construct()
    {
        $this->session = (new Auth)->user(_ADMINISTRATOR, 2);
    }

    public function index() {
        $exceptionfilelocation = LOGGER."/exception_log.txt";
        $filegetcontents = file_get_contents($exceptionfilelocation);
        $errorlog = htmlspecialchars($filegetcontents);

        function make_content_file($exceptionfilelocation, $content, $opentype = "w")
        {
            $fp_file = fopen($exceptionfilelocation, $opentype);
            fputs($fp_file, $content);
            fclose($fp_file);
        }

        if ($_POST) {
            $newcontents = $_POST['newcontents'];
            make_content_file($exceptionfilelocation, $newcontents);
        }
        $filecontents = file_get_contents($exceptionfilelocation);
        
        $data = [
            'title' => 'Exception Log',
            'filecontents' => $filecontents,
            'errorlog' => $errorlog,
        ];
        $this->view('error/admin/admin', $data, 'admin');
    }

}