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
            View::render('search/search', $data, 'user');

        } else {
            Redirect::autolink(URLROOT."/search", "Nothing Found Try Again");
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
        View::render('search/needseed', $data, 'user');
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
        $cats = (int) Input::get('cat') ?? 0;
        $parent_cat = Input::get('parent_cat') ?? '';
        $url = "?"; // assign url
        $wherea = []; // assign conditions
        $params = []; // assign vars

        if (!$cats == 0) {
            $params[] = $cats;
            $wherea[] = "category = ?";
            $url .= "cat=" . urlencode(Input::get("cat")) . "&";
        }

        if (!$parent_cat == '') {
            $params[] = $parent_cat;
            $wherea[] = "categories.parent_cat= ?";
            $url .= "parent_cat=" . urlencode(Input::get("parent_cat")) . "&";
        }

        $where = implode(" AND ", $wherea);
        $wherecatina = array();
        $wherecatin = "";
        $res = Torrents::getCatById();
        while ($row = $res->fetch(PDO::FETCH_LAZY)) {
            if (Input::get("c$row[id]")) {
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
            $res = DB::run("SELECT torrents.id, torrents.anon, torrents.announce, torrents.category, torrents.sticky, torrents.leechers, torrents.nfo, torrents.seeders, torrents.name, torrents.times_completed, torrents.tube, torrents.tmdb, torrents.size, torrents.added, torrents.comments, torrents.numfiles, torrents.filename, torrents.owner, torrents.external, torrents.freeleech, categories.name AS cat_name, categories.parent_cat AS cat_parent, categories.image AS cat_pic, users.username, users.privacy, IF(torrents.numratings < 2, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating FROM torrents LEFT JOIN categories ON category = categories.id LEFT JOIN users ON torrents.owner = users.id $where $orderby $limit", $params);
        } else {
            unset($res);
        }
        $cats = Torrents::getCatByParentName();

        $data = [
            'title' => Lang::T("BROWSE_TORRENTS"),
            'res' => $res,
            'pagerbottom' => $pagerbottom,
            'catsquery' => $catsquery,
            'url' => $url,
            'parent_cat' => $parent_cat,
            'count' => $count,
            'wherecatina' => $wherecatina,
            'cats' => $cats
        ];
        View::render('search/browse', $data, 'user');
    }

}