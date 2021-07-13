<a href='<?php echo URLROOT ?>/request'><button  class='btn btn-sm ttbtn'>All Request</button></a>&nbsp;
<a href='<?php echo URLROOT ?>/request?requestorid=<?php echo $_SESSION['id'] ?>'><button  class='btn btn-sm ttbtn'>View my requests</button></a>
<br><br>
<b><font color='#ff9900'>If this is abused, it will be for VIP only!</font></b>
<br><br>
    <b>* Before posting a request, please make sure to search the site first to make sure it's not already posted.</b><br>
    <b>* 1 request per day per member. Any more than that will be deleted by a moderator.</b><br>
    <b>* When possible, please provide a full scene release name.</b><br>

<form method=post action='<?php echo URLROOT ?>/request/confirmreq'><a name=add id=add></a>
<CENTER>
<table border=0 width=600 cellspacing=0 cellpadding=3>
<tr><td class=colhead align=center><b><?php echo Lang::T('MAKE_REQUEST') ?></b></a></td><tr>
<tr><td align=center><b>Title: </b><input type=text size=40 name=requesttitle>
    <select name="cat">
    <option value="0"><?php echo "(" . Lang::T("ALL") . " " . Lang::T("TYPES") . ")"; ?></option>
    <?php
    $cats = genrelist();
    $catdropdown = "";
    foreach ($cats as $cat) {
        $catdropdown .= "<option value=\"" . $cat["id"] . "\"";
            if ($cat["id"] == $_GET["cat"]) {
                $catdropdown .= " selected=\"selected\"";
            }
            $catdropdown .= ">" . htmlspecialchars($cat["parent_cat"]) . ": " . htmlspecialchars($cat["name"]) . "</option>\n";
    }
    echo $catdropdown?>
    </select>
<tr><td align=center>Additional Information <b>(Optional - but be generous!</b>)<br>
<textarea class="form-control" id="descr" name="descr" rows="7"></textarea>
<tr><td align=center><button  class='btn btn-sm ttbtn'><?php echo Lang::T('SUBMIT') ?></button>
</form>
</table></CENTER>