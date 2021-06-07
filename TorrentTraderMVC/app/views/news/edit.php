<form method='post' action='<?php echo URLROOT; ?>/adminnews/updated?id=<?php echo $data['newsid']; ?>' name='news'>
<?php
foreach ($data['res'] as $arr) { ?>
<b><?php echo  Lang::T("CP_NEWS_TITLE"); ?>: </b><input type='text' name='title' value="<?php echo $arr['title']; ?>" /><br />
<br /><?php echo  textbbcode("news", "body", $arr["body"]); ?><br />
<center>
<input type='submit' value='Okay' />
</center>
<?php
}
?>
</form>