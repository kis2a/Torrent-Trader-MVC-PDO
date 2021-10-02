<form method='post' action='<?php echo  URLROOT ?>/admincategories/takeadd'>

<div class="row justify-content-md-center">
    <div class="col-2">
        <b>Parent Category:</b>
    </div>
    <div class="col-2">
        <input type='text' name='parent_cat' /><br>
    </div>
</div>

<div class="row justify-content-md-center">
    <div class="col-2">
        <b>Sub Category:</b>
    </div>
    <div class="col-2">
        <input type='text' name='name' /><br>
    </div>
</div>

<div class="row justify-content-md-center">
    <div class="col-2">
        <b>Sort:</b>
    </div>
    <div class="col-2">
        <input type='text' name='sort_index' /><br>
    </div>
</div>

<div class="row justify-content-md-center">
    <div class="col-2">
        <b>Image:</b>
    </div>
    <div class="col-2">
        <input type='text' name='image' /><br>
    </div>
</div>

<div class="text-center">
    <input type='submit' class="btn btn-sm ttbtn" value='<?php echo Lang::T("SUBMIT") ?>' />
</div>

</div>
</form>