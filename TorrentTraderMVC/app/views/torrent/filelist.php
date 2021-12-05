<?php
torrentmenu($data['id'], $row['external']);
echo "<center><b>" . Lang::T("FILE_LIST") . ":</b></center>";
echo '<br><br><table cellpadding="1" cellspacing="2" class="table_table"><tr>';
echo "<table align='center' cellpadding='0' cellspacing='0' class='table_table' border='1' width='100%'>
<tr><th width='60' class='table_head' align='left'>&nbsp;" . Lang::T("Type") . "</th>
<th class='table_head' align='left'>&nbsp;" . Lang::T("FILE") . "</th>
<th width='120' class='table_head'>&nbsp;" . Lang::T("SIZE") . "</th>
</tr>";
if ($data['fres']->rowCount()) {
    while ($frow = $data['fres']->fetch(PDO::FETCH_ASSOC)) {
        $ext = pathinfo($frow['path'], PATHINFO_EXTENSION);
        $filetype_icon = getexttype($ext);
    echo "<tr><td class='table_col1'>" . $filetype_icon . "</td><td class='table_col2'>" . htmlspecialchars($frow['path']) . "</td><td class='table_col1'>" . mksize($frow['filesize']) . "</td></tr>";
    }
} else {
     echo "<tr><td class='table_col1'>" . htmlspecialchars($data["name"]) . "</td><td class='table_col2'>" . mksize($data["size"]) . "</td></tr>";
}
echo "</table>";