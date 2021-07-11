<?php
class Stylesheets
{

    public static function getStyleDropDown($stylesheet)
    {
        $stylesheets = '';
        $ss_r = DB::run("SELECT * from stylesheets");
        $ss_sa = array();
        while ($ss_a = $ss_r->fetch(PDO::FETCH_LAZY)) {
            $ss_id = $ss_a["uri"];
            $ss_name = $ss_a["name"];
            $ss_sa[$ss_name] = $ss_id;
        }
        ksort($ss_sa);
        reset($ss_sa);
        while (list($ss_name, $ss_id) = thisEach($ss_sa)) {
            if ($ss_id == $stylesheet) {
                $ss = " selected='selected'";
            } else {
                $ss = "";
            }
            $stylesheets .= "<option value='$ss_id'$ss>$ss_name</option>\n";
        }
        return $stylesheets;
    }

}