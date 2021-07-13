<?php
forumheader('search');
?>
<div class="row justify-content-md-center">
    <div class="col-6 border ttborder">
    <form method='get' action='<?php echo URLROOT; ?>/forums/result'>
        <center>
        <?php echo Lang::T("SEARCH") ?>:<br><br>
        <input type='text' size='40' name='keywords' /><br /><br>
        <button type='submit' class='btn btn-sm ttbtn' value='Search'>Search</button><br><br>
        </center>
    </form>
    </div>
</div>