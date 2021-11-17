<div class="row justify-content-center">
    <form method="post" action="<?php echo URLROOT; ?>/moviedatabase/<?php echo $data['link']; ?>" autocomplete="off">

    <div class="text-center">
    <label for="inputsearch" class="col-form-label"><?php echo Lang::T("Search"); ?>:</label>
       <input id="inputsearch" type="text" class="form-control" name="inputsearch" minlength="3" maxlength="40" required autofocus><br>
        <button type="submit" class="btn ttbtn "><?php echo Lang::T("Search"); ?></button><br>
	</div>

    <div class="margin-top20 text-center"><br>
        <a href="<?php echo URLROOT; ?>/moviedatabase/person"><b><?php echo Lang::T("Person"); ?></b></a> | 
        <a href="<?php echo URLROOT; ?>/moviedatabase/shows"><b><?php echo Lang::T("Show"); ?></b></a> | 
        <a href="<?php echo URLROOT ?>/moviedatabase/movies"><b><?php echo Lang::T("Movie"); ?></b></a>
	</div>

    </form>
</div>