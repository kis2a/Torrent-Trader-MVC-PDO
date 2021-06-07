<h1 align="center">Add Section</h1>
<form method="post" action="<?php echo URLROOT ?>/adminfaq/newsection">
<input type=hidden name=action value=addnewsect>
<table border="0" class="table_table" cellspacing="0" cellpadding="10" align="center">
<tr><td class='table_col1'>Title:</td>
    <td class='table_col1'><input style="width: 300px;" type="text" name="title" value="" /></td></tr>
<tr><td class='table_col2'>Status:</td>
    <td class='table_col2'><select name="flag" style="width: 110px;">
                           <option value="0" style="color: #ff0000;">Hidden</option>
                           <option value="1" style="color: #000000;" selected="selected">Normal</option>
                           </select></td></tr>
<tr><td colspan="2" align="center"><input type="submit" name="edit" value="Add" style="width: 60px;" /></td></tr>
</table>
</form>