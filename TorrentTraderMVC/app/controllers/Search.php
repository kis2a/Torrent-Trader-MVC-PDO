<?php

class Search
{

    public function __construct()
    {
        $this->session = Auth::user(0, 1);
    }

    public function index()
    {
        //check permissions
        if (Config::TT()['MEMBERSONLY']) {
            if ($_SESSION["view_torrents"] == "no") {
                Redirect::autolink(URLROOT, Lang::T("NO_TORRENT_VIEW"));
            }
        }

        // The Gets
        $keyword = $_GET['keyword'] ?? '';
        $cats = (int) $_GET['cat'] ?? 0;
        $incldead = (int) $_GET['incldead'] ?? 0;
        $freeleech = (int) $_GET['freeleech'] ?? 0;
        $inclexternal = (int) $_GET['inclexternal'] ?? 0;
        $lang = (int) $_GET['lang'] ?? 0;
        // default where & url & prepared statement vars
        $url = "?"; // assign url
        $wherea = []; // assign conditions
        $params = []; // assign vars

        if (!$keyword == '') {
            $keys = explode(" ", $keyword);
            foreach ($keys as $k) {
                $ssa[] = " torrents.name LIKE '%$k%' ";
            }
            $wherea[] = '(' . implode(' OR ', $ssa) . ')';
            $url .= "keyword=" . urlencode($keyword) . "&";
        }

        if (!$cats == 0) {
            $wherea[] = "category = $cats";
            $url .= "cat=" . urlencode($cats) . "&";
        }

        if ($incldead == 1) {
            $url .= "incldead=1&";
        } elseif ($incldead == 2) {
            $params[] = 'no';
            $wherea[] = "visible = ?";
            $url .= "incldead=2&";
        } else {
            $params[] = 'yes';
            $wherea[] = "visible = ?";
        }

        if ($freeleech == 1) {
            $params[] = 0;
            $wherea[] = "freeleech = ?";
            $url .= "freeleech=1&";
        } elseif ($freeleech == 2) {
            $params[] = 1;
            $wherea[] = "freeleech = ?";
            $url .= "freeleech=2&";
        }

        if ($inclexternal == 1) {
            $params[] = 'no';
            $wherea[] = "external = ?";
            $url .= "inclexternal=1&";
        } elseif ($inclexternal == 2) {
            $params[] = 'yes';
            $wherea[] = "external = ?";
            $url .= "inclexternal=2&";
        }

        if ($lang) {
            $params[] = $lang;
            $wherea[] = "torrentlang = ?";
            $url .= "lang=" . urlencode($lang) . "&";
        }

        $where = implode(' AND ', $wherea);
        if ($where != '') {
            $where = 'WHERE ' . $where;
        }

        $sortmod = $this->sortMod();
        $orderby = 'ORDER BY torrents.' . $sortmod['column'] . ' ' . $sortmod['by'];
        $pagerlink = $sortmod['pagerlink'];

        $count = DB::run("SELECT COUNT(*) FROM torrents " . $where, $params)->fetchcolumn();
        if ($count) {
            list($pagertop, $pagerbottom, $limit) = pager(25, $count, URLROOT . "/search?$url$pagerlink");
            $res = Torrents::search($where, $orderby, $limit, $params);

            if (!$keyword == '') {
                $title = Lang::T("SEARCH_RESULTS_FOR") . " \"" . htmlspecialchars($keyword) . "\"";
            } else {
                $title = Lang::T("SEARCH");
            }

            $data = [
                'title' => $title,
                'res' => $res,
                'pagerbottom' => $pagerbottom,
                'keyword' => $keyword,
                'url' => $url,
            ];
            View::render('torrent/search', $data, 'user');

        } else {
            Style::header(Lang::T("SEARCH"));
            Style::begin(Lang::T("SEARCH"));
            echo 'Nothing Found !';
            Style::end();
            Style::footer();
        }
    }

    public static function sortMod()
    {
        $sort = $_GET['sort'] ?? '';
        $order = $_GET['order'] ?? '';
        switch ($sort) {
            case 'id':$column = "id";
                break;
            case 'name':$column = "name";
                break;
            case 'comments':$column = "comments";
                break;
            case 'size':$column = "size";
                break;
            case 'times_completed':$column = "times_completed";
                break;
            case 'seeders':$column = "seeders";
                break;
            case 'leechers':$column = "leechers";
                break;
            case 'category':$column = "category";
                break;
            default:$column = "id";
                break;
        }

        switch ($order) {
            case 'asc':$ascdesc = "ASC";
                break;
            case 'desc':$ascdesc = "DESC";
                break;
            default:$ascdesc = "DESC";
                break;
        }

        $orderby = 'ORDER BY torrents.' . $column . ' ' . $ascdesc;
        $pagerlink = "sort=" . $column . "&amp;order=" . strtolower($ascdesc) . "&amp;";

        return [
            'orderby' => $orderby, 'pagerlink' => $pagerlink,
            'column' => $column, 'by' => $ascdesc,
        ];
    }

    public function needseed()
    {
        if ($_SESSION["view_torrents"] == "no") {
            Redirect::autolink(URLROOT, Lang::T("NO_TORRENT_VIEW"));
        }
        $res = DB::run("SELECT torrents.id, torrents.name, torrents.owner, torrents.external, torrents.size, torrents.seeders, torrents.leechers, torrents.times_completed, torrents.added, users.username FROM torrents LEFT JOIN users ON torrents.owner = users.id WHERE torrents.banned = 'no' AND torrents.leechers > 0 AND torrents.seeders <= 1 ORDER BY torrents.seeders");
        if ($res->rowCount() == 0) {
            Redirect::autolink(URLROOT, Lang::T("NO_TORRENT_NEED_SEED"));
        }
        $title = Lang::T("TORRENT_NEED_SEED");
        $data = [
            'title' => $title,
            'res' => $res,
        ];
        View::render('torrent/needseed', $data, 'user');
    }

    public function today()
    {
        //check permissions
        if (Config::TT()['MEMBERSONLY']) {
            if ($_SESSION["view_torrents"] == "no") {
                Redirect::autolink(URLROOT, Lang::T("NO_TORRENT_VIEW"));
            }
        }

        $date_time = TimeDate::get_date_time(TimeDate::gmtime() - (3600 * 24)); // the 24 is the hours you want listed
        $catresult = Torrents::getCatSort();

        Style::header(Lang::T("TODAYS_TORRENTS"));
        Style::begin(Lang::T("TODAYS_TORRENTS"));
        while ($cat = $catresult->fetch(PDO::FETCH_ASSOC)) {
            $orderby = "ORDER BY torrents.sticky ASC, torrents.id DESC"; //Order
            $where = "WHERE banned = 'no' AND category='$cat[id]' AND visible='yes'";
            $limit = "LIMIT 10"; //Limit

            $res = Torrents::getCatSortAll($where, $date_time, $orderby, $limit);
            $numtor = $res->rowCount();
            if ($numtor != 0) {
                echo "<b><a href=" . URLROOT . "/torrent/browse?cat=" . $cat["id"] . "'>$cat[name]</a></b>";
                torrenttable($res);
                echo "<br />";
            }
        }
        Style::end();
        Style::footer();
    }

    public function browse()
    {
        //check permissions
        if (Config::TT()['MEMBERSONLY']) {
            if ($_SESSION["view_torrents"] == "no") {
                Redirect::autolink(URLROOT, Lang::T("NO_TORRENT_VIEW"));
            }
        }
        $cats = (int) $_GET['cat'] ?? 0;
        $parent_cat = $_GET['parent_cat'] ?? '';
        $url = "?"; // assign url
        $wherea = []; // assign conditions
        $params = []; // assign vars

        if (!$cats == 0) {
            $params[] = $cats;
            $wherea[] = "category = ?";
            $url .= "cat=" . urlencode($_GET["cat"]) . "&";
        }

        if (!$parent_cat == '') {
            $params[] = $parent_cat;
            $wherea[] = "categories.parent_cat= ?";
            $url .= "parent_cat=" . urlencode($_GET["parent_cat"]) . "&";
        }

        $where = implode(" AND ", $wherea);
        $wherecatina = array();
        $wherecatin = "";
        $res = Torrents::getCatById();
        while ($row = $res->fetch(PDO::FETCH_LAZY)) {
            if ($_GET["c$row[id]"]) {
                $wherecatina[] = $row["id"];
                $url .= "c$row[id]=1&";
            }
            $wherecatin = implode(", ", $wherecatina);
        }

        if ($wherecatin) {
            $where .= ($where ? " AND " : "") . "category IN(" . $wherecatin . ")";
        }

        if ($where != "") {
            $where = "WHERE $where";
        }

        $sortmod = $this->sortMod();
        $orderby = 'ORDER BY torrents.' . $sortmod['column'] . ' ' . $sortmod['by'];
        $pagerlink = $sortmod['pagerlink'];

        // Get Total For Pager
        $count = DB::run("SELECT COUNT(*) FROM torrents LEFT JOIN categories ON category = categories.id $where", $params)->fetchColumn();
        // Get cats
        $catsquery = Torrents::getCatByParent();

        //get sql info
        if ($count) {
            list($pagertop, $pagerbottom, $limit) = pager(25, $count, URLROOT."/search/browse" . $url.$pagerlink);
            $res = DB::run("SELECT torrents.id, torrents.anon, torrents.announce, torrents.category, torrents.sticky, torrents.leechers, torrents.nfo, torrents.seeders, torrents.name, torrents.times_completed, torrents.tube, torrents.imdb, torrents.size, torrents.added, torrents.comments, torrents.numfiles, torrents.filename, torrents.owner, torrents.external, torrents.freeleech, categories.name AS cat_name, categories.parent_cat AS cat_parent, categories.image AS cat_pic, users.username, users.privacy, IF(torrents.numratings < 2, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating FROM torrents LEFT JOIN categories ON category = categories.id LEFT JOIN users ON torrents.owner = users.id $where $orderby $limit", $params);
        } else {
            unset($res);
        }
        /*
        // Get cats
        $catsquery = Torrents::getCatByParent();
        $catbypar = Torrents::getCatByParentName();
        $subcatsquery = DB::run("SELECT id, name, parent_cat FROM categories WHERE parent_cat= ? ORDER BY name", [$parent_cat]);

        $data = [
            'title' => Lang::T("BROWSE_TORRENTS"),
            'res' => $res,
            'pagerbottom' => $pagerbottom,
            'catsquery' => $catsquery,
            'catbypar' => $catbypar,
            'url' => $url,
            'parent_cat' => $parent_cat,
            'subcatsquery' => $subcatsquery,
            'count' => $count,
            'wherecatin' => $wherecatin
        ];
        View::render('torrent/browse', $data, 'user');
    */
        Style::header(Lang::T("BROWSE_TORRENTS"));
        Style::begin(Lang::T("BROWSE_TORRENTS"));

        // get all parent cats
        echo "<center><b>" . Lang::T("CATEGORIES") . ":</b> ";
        
        echo " - <a href=" . URLROOT . "/search/browse>" . Lang::T("SHOW_ALL") . "</a>";
        while ($catsrow = $catsquery->fetch(PDO::FETCH_ASSOC)) {
            $parenturl = urlencode($catsrow['parent_cat']);
            echo " - <a href=" . URLROOT . "/search/browse?parent_cat=$parenturl>$catsrow[parent_cat]</a>";
        }
        ?>
                <br /><br />
                <form method="get" action="<?php echo URLROOT ?>/search/browse">
                <table align="center">
                <tr align='right'>
                <?php
$i = 0;
        $cats = Torrents::getCatByParentName();
        while ($cat = $cats->fetch(PDO::FETCH_ASSOC)) {
            $catsperrow = 5;
            print(($i && $i % $catsperrow == 0) ? "</tr><tr align='right'>" : "");
            print("<td style=\"padding-bottom: 2px;padding-left: 2px\"><a href=" . URLROOT . "/search/browse?cat=$cat[id]>" . htmlspecialchars($cat["parent_cat"]) . " - " . htmlspecialchars($cat["name"]) . "</a> <input name='c{$cat["id"]}' type=\"checkbox\" " . (in_array($cat["id"], $wherecatina) || $_GET["cat"] == $cat["id"] ? "checked='checked' " : "") . "value='1' /></td>\n");
            $i++;
        }
        echo "</tr><tr align='center'><td colspan='$catsperrow' align='center'><input type='submit' value='" . Lang::T("GO") . "' /></td></tr>";
        echo "</table></form>";

        //if we are browsing, display all subcats that are in same cat
        if ($parent_cat) {
            $url .= "parent_cat=" . urlencode($parent_cat) . "&amp;";
            echo "<br /><br /><b>" . Lang::T("YOU_ARE_IN") . ":</b> <a href='" . URLROOT . "/search/browse?parent_cat=" . urlencode($parent_cat) . "'>" . htmlspecialchars($parent_cat) . "</a><br /><b>" . Lang::T("SUB_CATS") . ":</b> ";
            $subcatsquery = DB::run("SELECT id, name, parent_cat FROM categories WHERE parent_cat= ? ORDER BY name", [$parent_cat]);
            while ($subcatsrow = $subcatsquery->fetch(PDO::FETCH_ASSOC)) {
                $name = $subcatsrow['name'];
                echo " - <a href=" . URLROOT . "/search/browse?cat=$subcatsrow[id]>$name</a>";
            }
        }

        if (Validate::Id($_GET["page"])) {
            $url .= "page=$_GET[page]&amp;";
        }

        echo "</center><br /><br />"; //some spacing
        // New code (TorrentialStorm)
        echo "<div align='right'><form id='sort' action=''>" . Lang::T("SORT_BY") . ": <select name='sort' onchange='window.location=\"{$url}sort=\"+this.options[this.selectedIndex].value+\"&amp;order=\"+document.forms[\"sort\"].order.options[document.forms[\"sort\"].order.selectedIndex].value'>";
        echo "<option value='id'" . ($_GET["sort"] == "id" ? " selected='selected'" : "") . ">" . Lang::T("ADDED") . "</option>";
        echo "<option value='name'" . ($_GET["sort"] == "name" ? " selected='selected'" : "") . ">" . Lang::T("NAME") . "</option>";
        echo "<option value='comments'" . ($_GET["sort"] == "comments" ? " selected='selected'" : "") . ">" . Lang::T("COMMENTS") . "</option>";
        echo "<option value='size'" . ($_GET["sort"] == "size" ? " selected='selected'" : "") . ">" . Lang::T("SIZE") . "</option>";
        echo "<option value='times_completed'" . ($_GET["sort"] == "times_completed" ? " selected='selected'" : "") . ">" . Lang::T("COMPLETED") . "</option>";
        echo "<option value='seeders'" . ($_GET["sort"] == "seeders" ? " selected='selected'" : "") . ">" . Lang::T("SEEDERS") . "</option>";
        echo "<option value='leechers'" . ($_GET["sort"] == "leechers" ? " selected='selected'" : "") . ">" . Lang::T("LEECHERS") . "</option>";
        echo "</select>&nbsp;";
        echo "<select name='order' onchange='window.location=\"{$url}order=\"+this.options[this.selectedIndex].value+\"&amp;sort=\"+document.forms[\"sort\"].sort.options[document.forms[\"sort\"].sort.selectedIndex].value'>";
        echo "<option selected='selected' value='asc'" . ($_GET["order"] == "asc" ? " selected='selected'" : "") . ">" . Lang::T("ASCEND") . "</option>";
        echo "<option value='desc'" . ($_GET["order"] == "desc" ? " selected='selected'" : "") . ">" . Lang::T("DESCEND") . "</option>";
        echo "</select>";
        echo "</form></div>";
        // End

        if ($count) {
            torrenttable($res);
            print($pagerbottom);
        } else {
            print(Lang::T("NOTHING_FOUND") . "&nbsp;&nbsp;");
            print Lang::T("NO_UPLOADS");
        }
        if ($_SESSION) {
            DB::run("UPDATE users SET last_browse=? WHERE id=?", [TimeDate::gmtime(), $_SESSION['id']]);
        }
        Style::end();
        Style::footer();
    }

}