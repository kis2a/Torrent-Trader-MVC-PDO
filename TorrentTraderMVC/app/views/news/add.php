<form method='post' action='<?php echo URLROOT; ?>/adminnews/submit' name='news'>
<input type='hidden' name='action' value='news' />
<input type='hidden' name='do' value='takeadd' />
<center><b><?php echo Lang::T("CP_NEWS_TITLE"); ?>:</b> <input type='text' name='title' /><br /></center>
<?php include APPROOT . '/helpers/bbcode_helper.php'; ?>
<br /><?php echo textbbcode("news", "body"); ?><br />
<center><input type='submit' value='<?php echo Lang::T("SUBMIT"); ?>' /></center>
</form>