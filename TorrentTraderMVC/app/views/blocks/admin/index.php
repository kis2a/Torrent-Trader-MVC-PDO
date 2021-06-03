
<table align="center" width="70%"><tr><td>

<center><?php echo Lang::T("_BLC_ENABLED_") ?></center>

<table class='table table-striped table-bordered table-hover'><thead><tr>
<th><?php echo Lang::T("NAME") ?></th>
<th>Description</th>
<th>Position</th>
<th>Order</th>
<th>Preview</th>
</tr></thead><tbody> <?php
while ($blocks = $data['enabled']->fetch(PDO::FETCH_LAZY)) {
    if (!$setclass) {
        $class = "table_col2";
        $setclass = true;} else {
        $class = "table_col1";
        $setclass = false;}

        print("<tr>" .
            "<td class=\"$class\" valign=\"top\">" . $blocks["named"] . "</td>" .
            "<td class=\"$class\">" . $blocks["description"] . "</td>" .
            "<td class=\"$class\" align=\"center\">" . $blocks["position"] . "</td>" .
            "<td class=\"$class\" align=\"center\">" . $blocks["sort"] . "</td>" .
            "<td class=\"$class\" align=\"center\">[<a href=\"".URLROOT."/adminblocks/preview?name=" . $blocks["name"] . "#" . $blocks["name"] . "\" target=\"_blank\">preview</a>]</td>" .
            "</tr>");
    }
    print("<tr><td colspan=\"5\" align=\"center\" class=\"table_head\"><form action='".URLROOT."/adminblocks/edit'><input type='submit' value='Edit' /></form></td></tr>");
    print("</tbody></table>");
    print("</td></tr></table>");

    print("<hr />");
    $setclass = false;
    print("<table align=\"center\" width=\"600\"><tr><td>");
    print("<table class=\"table_table\" cellspacing=\"1\" align=\"center\" width=\"100%\">" .
        "<tr>" .
        "<th class=\"table_head\"><center>Disabled Blocks</center></th>" .
        "</tr>" .
        "</table>" .
        "<table class='table'>
        <thead>" .
        "<tr>" .
        "<th class=\"table_head\">" . Lang::T("NAME") . "</th>" .
        "<th class=\"table_head\">Description</th>" .
        "<th class=\"table_head\">Position</th>" .
        "<th class=\"table_head\">Order</th>" .
        "<th class=\"table_head\">Preview</th>" .
        "</tr></thead><tbody>");
    while ($blocks = $data['disabled']->fetch(PDO::FETCH_LAZY)) {
        if (!$setclass) {
            $class = "table_col2";
            $setclass = true;
        } else {
            $class = "table_col1";
            $setclass = false;
        }

        print("<tr>" .
            "<td class='$class' valign=\"top\">" . $blocks["named"] . "</td>" .
            "<td class='$class'>" . $blocks["description"] . "</td>" .
            "<td class='$class' align=\"center\">" . $blocks["position"] . "</td>" .
            "<td class='$class' align=\"center\">" . $blocks["sort"] . "</td>" .
            "<td class='$class' align=\"center\">[<a href=\"".URLROOT."/adminblocks/preview?name=" . $blocks["name"] . "#" . $blocks["name"] . "\" target=\"_blank\">preview</a>]</td>" .
            "</tr>");
    }
    print("<tr><td colspan=\"5\" align=\"center\" valign=\"bottom\" class=\"table_head\"><form action='".URLROOT."/adminblocks/upload'><input type='submit' value='Upload new Block' /></form></td></tr>");
    print("</tbody></table>");
    print("</td></tr></table>");