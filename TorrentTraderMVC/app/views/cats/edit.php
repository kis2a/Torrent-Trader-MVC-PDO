<?php
$sql = $data['res']->fetch(PDO::FETCH_ASSOC);
?>
<form method='post' action='<?php echo URLROOT ?>/admincategories/edit?id=<?php echo $data['id'] ?>&amp;save=1'>
<center><table border='0' cellspacing='0' cellpadding='5'>
<tr><td align='left'><b>Parent Category: </b><input type='text' name='parent_cat' value="<?php echo $sql['parent_cat'] ?>" /> All Subcats with EXACTLY the same parent cat are grouped</td></tr>
<tr><td align='left'><b>Sub Category: </b><input type='text' name='name' value="<?php echo $sql['name'] ?>"></td></tr>
<tr><td align='left'><b>Sort: </b><input type='text' name='sort_index' value="<?php echo $sql['sort_index'] ?>" /></td></tr>
<tr><td align='left'><b>Image: </b><input type='text' name='image' value="<?php echo $sql['image'] ?>" > single filename</td></tr>
<tr><td align='center'><input type='submit' value='<?php echo Lang::T("SUBMIT") ?>' ></td></tr>
</table></center>
</form>