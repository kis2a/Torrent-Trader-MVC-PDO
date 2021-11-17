<?php
require_once APPROOT . "/libraries/tmdb/TMDB.php";

class Tmdbscraper
{
    public static function getId($url)
    {
        $parts = explode('/', $url);
        $id_tmdb = strtok($parts[4], '-');
        return $id_tmdb;
    }
    
    public static function createFilm($id_tmdb, $id)
    {
        // Get TMDB Api
        $tmdb = new TMDB(_TMDBAPIKEY, 'en'); // , true
        $film = $tmdb->getMovie($id_tmdb);
        // Debug
        //var_dump($film);
        // Get & Save Image
        $image_url = $tmdb->getImageURL('w185') . "" . $film->getPoster();
        $image = $tmdb->saveImage($image_url, UPLOADDIR . "/tmdb/film/", $id_tmdb);
        // Get Data
        $trailer = $film->getTrailer();
        $title = $film->getTitle();
        $date = $film->date();
        $duration = $film->duration();
        $producer = '$film->creator()';
        $prod = $producer[0];
        $genre = $film->genre();
        $plot = $film->getPlot();
        $actors = $film->actor();
        $casting = $actors;
        $casting_role = $casting[0];
        $casting_role = explode('*', $casting_role);
        $casting_nom = $casting[1];
        $casting_nom = explode('+', $casting_nom);
        $casting_img = $casting[2];
        $casting_img = explode('&', $casting_img);
        for ($i = 0; $i <= 3; $i++) {
            //$bdd = '';
            $bdd .= "$casting_nom[$i]*$casting_role[$i]*$casting_img[$i]&";
        }

        // Insert Data
        DB::run("INSERT INTO tmdbfilm 
        (id_tmdb, title, duration, producer, genre, plot, actor, trailer, date, image)
                 VALUES (?,?,?,?,?,?,?,?,?,?)",
        [$id_tmdb, $title, $duration, $prod, $genre, $plot, $bdd, $trailer, $date, $image]);
        // Return To Torrent
        Redirect::to(URLROOT . "/torrent?id=$id");

    }

    public static function getFilm($id_tmdb)
    {
        $TTCache = new Cache();
        $expires = 1728000; // Cache time in seconds
        if (($_data = $TTCache->Get("tmdb/film/$id_tmdb", $expires)) === false) {
            // Get Data From DB
            $res = DB::run("SELECT * FROM `tmdbfilm` WHERE id_tmdb = " . $id_tmdb . "");
            $film = $res->fetch(PDO::FETCH_ASSOC);
            // Data
            $_data["title"] = $film["title"];
            $_data["duration"] = $film["duration"];
            $_data["producer"] = $film["producer"];
            $_data["genre"] = $film["genre"];
            $_data["plot"] = $film["plot"];
            $_data["actors"] = $film["actor"];
            $_data["trailer"] = $film["trailer"];
            $_data["date"] = $film["date"];
            $_data["poster"] = $film["image"];
            // Set Data In Cache
            $TTCache->Set("tmdb/film/$id_tmdb", $_data, $expires);
        }
        ?>
        <legend><b><?php echo Lang::T("TMDB"); ?> - <?php echo $_data["title"]; ?></b></legend>
        <?php
        print("<tr><td class='browsebg' align='center' colspan='2'><center>");
        print("<div><table cellpadding='3' width='80%'>
        <tr><td width='25%' class='browsebg' align='center' rowspan='8'><img src='" . data_uri(UPLOADDIR . "/tmdb/film/" . $_data["poster"], $_data["poster"]) . "'/></td>
        <td width='150px' class='browsebg' align='right'><b> Title : </b></td><td class='browsebg' align='left'> " . $_data["title"] . " </td></tr>
        <tr><td class='browsebg' align='right'><b> Date : </b></td><td class='browsebg' align='left'> " . $_data["date"] . " </td></tr>
        <tr><td class='browsebg' align='right'><b> Duration : </b></td><td class='browsebg' align='left'> " . $_data["duration"] . " </td></tr>
        
		<td class='browsebg' align='right'><b> Genre : </b></td><td class='browsebg' align='left'> " . $_data["genre"] . " </td></tr></td></tr>
        <tr><td class='browsebg' align='right'><b> Plot : </b></td><td class='browsebg' align='left'> " . $_data["plot"] . " </td></tr>
        <tr><td class='browsebg' align='right'><b> Actors : </b></td><td class='browsebg' align='left'><table width='100%'><tr>");
        $casting = explode('&', $_data["actors"]);
        for ($i = 0; $i <= 3; $i++) {
            list($pseudo, $role, $image) = explode("*", $casting[$i]);;
            print("<td class='browsebg' align='center' widht='33%'> " . $pseudo . " <br /><img class='avatar3' src='" . $image . "' /><br /> Role : <br /> " . $role . " </td>");
        }
        print("</tr></table></td></tr></table><br /><br />");
        print('<iframe width="400px" height="230px" src="https://www.youtube.com/embed/' . $_data["trailer"] . '" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe></div></center><br /><br />');
    }



    public static function createSerie($id_tmdb, $id)
    {
        // Get TMDB Api
        $tmdb = new TMDB(_TMDBAPIKEY, 'en'); // , true
        $serie = $tmdb->getTVShow($id_tmdb);
        // Debug
        //var_dump($film);
        // Get & Save Image
        $image_url = $tmdb->getImageURL('w185') . "" . $serie->getPoster();
        $image = $tmdb->saveImage($image_url, UPLOADDIR . "/tmdb/serie/", $id_tmdb);
        // Get Data
        $titre = $serie->getName();
        $season = $serie->getNumSeasons();
        $episode = $serie->getNumEpisodes();
        $status = $serie->getInProduction();
        $date = $serie->date();
        $creator = $serie->creator();
        $genre = $serie->genre();
        $plot = $serie->getOverview();
        $actors = $serie->actor();
        $casting = $actors;
        $casting_role = $casting[0];

        
        $casting_role = explode('*', $casting_role);
        $casting_nom = $casting[1];
        $casting_nom = explode('+', $casting_nom);
        $casting_img = $casting[2];
        $casting_img = explode('&', $casting_img);
        for ($i = 0; $i <= 3; $i++) {
            //$bdd = '';
            $bdd .= "$casting_nom[$i]*$casting_role[$i]*$casting_img[$i]&";
        }
        // Insert Data
        DB::run("INSERT INTO tmdbshow (id_tmdb, title, image, season, episodes, status, date, creator, genre, plot, actor)
	             VALUES (?,?,?,?,?,?,?,?,?,?,?)",
                [$id_tmdb, $titre, $image, $season, $episode, $status, $date, $creator, $genre, $plot, str_replace("http:", "https:", $bdd)]);
        // Return To Torrent
        Redirect::to(URLROOT . "/torrent?id=$id");
    }

    public static function getSerie($id_tmdb)
    {
        $TTCache = new Cache();
        $expires = 1728000; // Cache time in seconds
        if (($_data = $TTCache->Get("tmdb/serie/$id_tmdb", $expires)) === false) {
            // Get Data From DB
            $res = DB::run("SELECT * FROM `tmdbshow` WHERE id_tmdb = " . $id_tmdb . "");
            $serie = $res->fetch(PDO::FETCH_ASSOC);
            // Data
            $_data["poster"] = $serie["image"];
            $_data["title"] = $serie["title"];
            $_data["season"] = $serie["season"];
            $_data["episode"] = $serie["episodes"];
            $_data["status"] = $serie["status"];
            $_data["date"] = $serie["date"];
            $_data["creator"] = $serie["creator"];
            $_data["genre"] = $serie["genre"];
            $_data["plot"] = $serie["plot"];
            $_data["actor"] = $serie["actor"];
            // Set Data In Cache
            $TTCache->Set("tmdb/serie/$id_tmdb", $_data, $expires);
        }
        ?>
        <legend><b><?php echo Lang::T("TMDB"); ?> - <?php echo $_data["title"]; ?></b></legend>
        <?php
        print("<tr><td  class='browsebg' align='center' colspan='2'><center>");
        print("<div><table cellpadding='3' width='80%'>
        <tr><td width='25%' class='browsebg' align='center' rowspan='8'><img class='allocine' src='" . data_uri(UPLOADDIR . "/tmdb/serie/" . $_data["poster"], $_data["poster"]) . "'/></td>
		<td width='150px' class='browsebg' align='right'><b> Title : </b></td><td class='browsebg' align='left'> " . $_data["title"] . " </td></tr>
		<tr><td class='browsebg' align='right'><b> Seasons : </b></td><td class='browsebg' align='left'> " . $_data["season"] . " (" . $_data["episode"] . " épisodes) </td></tr>
		<tr><td class='browsebg' align='right'><b> Status : </b></td><td class='browsebg' align='left'> " . $_data["status"] . " </td></tr>
		<tr><td class='browsebg' align='right'><b> Date : </b></td><td class='browsebg' align='left'> " . $_data["date"] . " </td></tr>
		<tr><td class='browsebg' align='right'><b> Creator : </b></td><td class='browsebg' align='left'> " . $_data["creator"] . " </td></tr>
		<tr><td class='browsebg' align='right'><b> Genre : </b></td><td class='browsebg' align='left'> " . $_data["genre"] . " </td></tr>
		<tr><td class='browsebg' align='right'><b> Plot : </b></td><td class='browsebg' align='left'> " . $_data["plot"] . " </td></tr>
        <tr><td class='browsebg' align='right'><b> Actors : </b></td><td class='browsebg' align='left'><table width='100%'><tr>");
        $casting = explode('&', $_data["actor"]);
        for ($i = 0; $i <= 3; $i++) {
            list($pseudo, $role, $image) = explode("*", $casting[$i]);;
            print("<td class='browsebg' align='center' widht='33%'> " . $pseudo . " <br /><img class='avatar3' src='" . $image . "' /><br /> Role : <br /> " . $role . " </td>");
        }
        print("</tr></table></td></tr></table></div></center><br /><br />");
    }

}