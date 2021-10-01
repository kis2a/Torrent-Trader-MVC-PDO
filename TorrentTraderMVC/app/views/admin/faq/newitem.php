<h1 align="center">Add Item</h1>
<form method='post' action='<?php echo URLROOT ?>/adminfaq/additem'>
<table border="0" cellspacing="0" cellpadding="10" align="center">
<tr><td class='table_col1'>Question:</td><td class='table_col1'>
    <input style="width: 300px;" type="text" name="question" value=""></td></tr>
<tr><td class='table_col2' style="vertical-align: top;">Answer:</td>
    <td class='table_col2'><textarea rows='3' cols='35' name="answer"></textarea></td></tr>
<tr><td class='table_col1'>Status:</td><td class='table_col1'>
    <select name="flag" style="width: 110px;">
    <option value="0" style="color: #ff0000;">Hidden</option>
    <option value="1" style="color: #000000;">Normal</option>
    <option value="2" style="color: #0000FF;">Updated</option>
    <option value="3" style="color: #008000;" selected="selected">New</option>
    </select></td></tr>
<tr><td class='table_col2'>Category:</td>
    <td class='table_col2'><select style="width: 300px;" name="categ">
    <?php
    while ($arr = $data['res']->fetch(PDO::FETCH_BOTH)) {
       $selected = ($arr['id'] == $_GET['inid']) ? " selected=\"selected\"" : "";
       print("<option value=\"$arr[id]\"" . $selected . ">$arr[question]</option>");
    } ?>
    </select></td></tr>
<tr><td colspan="2" align="center"><input type="submit" name="edit" value="Add" style="width: 60px;" /></td></tr>
</table></form>