<?php
forumheader('search');
?>
<center><font color=#ffff00>Search Forums</font></center>
<div class="row justify-content-md-center">
    <div class="col-6 border border-warning">
    <form method='get' action='<?php echo URLROOT; ?>/forums/result'>
        <center>
        Search For:<br><br>
        <input type='text' size='40' name='keywords' /><br /><br>
        <button type='submit' class='btn btn-sm btn-warning' value='Search'>Search</button><br><br>
        </center>
    </form>
    </div>
</div>