<?php
require_once APPROOT . "/libraries/TMDB.php";

class Moviedatabase
{

    public function __construct()
    {
        $this->session = Auth::user(0, 2);
    }

    // Form For Torrent
    public function index()
    {
        $tmdb = new TMDB();
        var_dump($tmdb);
        $data = [
            'title' => 'Search TMDB Movie',
            'link' => 'msearch',
        ];
        View::render('moviedatabase/index', $data, 'user');
    }
    
    // Form For Person
    public function persons()
    {
        $data = [
            'title' => 'Search TMDB Person',
            'link' => 'psearch',
        ];
        View::render('moviedatabase/index', $data, 'user');
    }

    // Form For Show
    public function show()
    {
        $data = [
            'title' => 'Search TMDB Show',
            'link' => 'ssearch',
        ];
        View::render('moviedatabase/index', $data, 'user');
    }

    // Form For Torrent
    public function msearch()
    {
        Style::header('Movie Search Results');
        Style::begin('Person');
        $name = $_POST['inputsearch'];
        $tmdb = new TMDB();
        // 1. Search Movie
        echo '<ol><li><a id="searchMovie"><h3>Search Movie</h3></a><ul>';
        $movies = $tmdb->searchMovie($name);
        foreach ($movies as $movie) {
            echo '<li>' . $movie->getTitle() . ' (<a href="https://www.themoviedb.org/movie/' . $movie->getID() . '">' . $movie->getID() . '</a>)</li>';
        }
        echo '</ul></li><hr>';
        Style::end();
        Style::footer();
    }
    
    // Form For Person
    public function psearch()
    {
        Style::header('Person Search Results');
        Style::begin('Person');
        $name = $_POST['inputsearch'];
        $tmdb = new TMDB();
        // 1. Search Person
        echo '<ol><li><a id="searchPerson"><h3>Search Person</h3></a><ul>';
        $persons = $tmdb->searchPerson($name);
        foreach ($persons as $person) {
            echo '<li>' . $person->getName() . ' (<a href="https://www.themoviedb.org/person/' . $person->getID() . '">' . $person->getID() . '</a>)</li>';
        }
        echo '</ul></li><hr>';
        Style::end();
        Style::footer();
    }

    // Form For Show
    public function ssearch()
    {
        Style::header('Show Search Results');
        Style::begin('Show');
        $name = $_POST['inputsearch'];
        $tmdb = new TMDB();
        echo '<ol><li><a id="searchTVShow"><h3>Search TVShow</h3></a><ul>';
        $tvShows = $tmdb->searchMovie($name);
        foreach ($tvShows as $tvShow) {
            echo '<li>' . $tvShow->getTitle() . ' (<a href="https://www.themoviedb.org/tv/' . $tvShow->getID() . '">' . $tvShow->getID() . '</a>)</li>';
        }
        echo '</ul></li><hr>';
        Style::end();
        Style::footer();
    }


    // Search Person
    public function person()
    {
        Style::header('Person');
        Style::begin('Person');
        ?>
        <div class="margin-top20 text-center"><br>
            <a href="<?php echo URLROOT; ?>/moviedatabase/persons"><b><?php echo Lang::T("Search Person"); ?></b></a> | 
            <a href="<?php echo URLROOT; ?>/moviedatabase/show"><b><?php echo Lang::T("Search Show"); ?></b></a> | 
            <a href="<?php echo URLROOT ?>/moviedatabase/movie"><b><?php echo Lang::T("Search Movie"); ?></b></a><br>
            
            <a href="<?php echo URLROOT; ?>/moviedatabase/person"><b><?php echo Lang::T("Person"); ?></b></a> | 
            <a href="<?php echo URLROOT; ?>/moviedatabase/shows"><b><?php echo Lang::T("Show"); ?></b></a> | 
            <a href="<?php echo URLROOT ?>/moviedatabase/movies"><b><?php echo Lang::T("Movie"); ?></b></a>
        </div>
        <?php
        $tmdb = new TMDB();

        // 1. Search Person
        echo '<ol><li><a id="searchPerson"><h3>Search Person</h3></a><ul>';
        $persons = $tmdb->searchPerson("Johnny");
        foreach ($persons as $person) {
            echo '<li>' . $person->getName() . ' (<a href="https://www.themoviedb.org/person/' . $person->getID() . '">' . $person->getID() . '</a>)</li>';
        }
        echo '</ul></li><hr>';

        // 2. Full Person Info
        echo '<li><a id="personInfo"><h3>Full Person Info</h3></a><ul>';
        $person = $tmdb->getPerson(85);
        echo 'Now the <b>$person</b> var got all the data, check the <a href="http://code.octal.es/php/tmdb-api/class-Person.html">documentation</a> for the complete list of methods.<br><br>';
        echo '<b>' . $person->getName() . '</b><ul>';
        echo '  <li>ID: ' . $person->getID() . '</li>';
        echo '  <li>Birthday: ' . $person->getBirthday() . '</li>';
        echo '  <li>Popularity: ' . $person->getPopularity() . '</li>';
        echo '</ul>...';
        echo '<img src="' . $tmdb->getImageURL('w185') . $person->getProfile() . '"/>';
        echo '</ul></li><hr>';

        // 3. Get the roles
        echo '<li><a id="personRoles"><h3>Person Roles</h3></a>';
        echo 'Now each <b>$movieRole</b> var got all the data, check the <a href="http://code.octal.es/php/tmdb-api/class-MovieRole.html">documentation</a> for the complete list of methods.<br><br>';
        $movieRoles = $person->getMovieRoles();
        echo '<b>' . $person->getName() . '</b> - Roles in <b>Movies</b>: <ul>';
        foreach ($movieRoles as $movieRole) {
            echo '<li>' . $movieRole->getCharacter() . ' in <a href="https://www.themoviedb.org/movie/' . $movieRole->getMovieID() . '">' . $movieRole->getMovieTitle() . '</a></li>';
        }
        echo '</ul><br><br>';
        echo 'Now the <b>$tvShowRole</b> var got all the data, check the <a href="http://code.octal.es/php/tmdb-api/class-TVShowRole.html">documentation</a> for the complete list of methods.<br><br>';
        $tvShowRoles = $person->getTVShowRoles();
        echo '<b>' . $person->getName() . '</b> - Roles in <b>TVShows</b>: <ul>';
        foreach ($tvShowRoles as $tvShowRole) {
            echo '<li>' . $tvShowRole->getCharacter() . ' in <a href="https://www.themoviedb.org/tv/' . $tvShowRole->getTVShowID() . '">' . $tvShowRole->getTVShowName() . '</a></li>';
        }
        echo '</ul></li><hr>';

        // 4. Get the latest added Person
        echo '<li><a id="personLatest"><h3>Latest person</h3></a>';
        $person = $tmdb->getLatestPerson();
        echo 'Latest Person: ' . $person->getName() . ' (<a href="https://www.themoviedb.org/person/' . $person->getID() . '">' . $person->getID() . '</a>)</li>';
        echo '</li><hr>';

        // 5. Get the most popular Persons
        echo '<li><a id="personPopular"><h3>Popular persons</h3></a>';
        $persons = $tmdb->getPopularPersons();
        echo '<b>List of the most popular people, by simply doing $tmdb->getPopularPeople($page) you can switch between pages.</b>: <ol>';
        foreach ($persons as $person) {
            echo '<li>' . $person->getName() . ' (<a href="https://www.themoviedb.org/person/' . $person->getID() . '">' . $person->getID() . '</a>)</li>';
        }
        echo '</ol></li><hr>';
        Style::end();
        Style::footer();
    }

    // Search Shows
    public function shows()
    {
        Style::header('Shows');
        Style::begin('Shows');
        ?>
        <div class="margin-top20 text-center"><br>
            <a href="<?php echo URLROOT; ?>/moviedatabase/persons"><b><?php echo Lang::T("Search Person"); ?></b></a> | 
            <a href="<?php echo URLROOT; ?>/moviedatabase/show"><b><?php echo Lang::T("Search Show"); ?></b></a> | 
            <a href="<?php echo URLROOT ?>/moviedatabase/movie"><b><?php echo Lang::T("Search Movie"); ?></b></a><br>
            
            <a href="<?php echo URLROOT; ?>/moviedatabase/person"><b><?php echo Lang::T("Person"); ?></b></a> | 
            <a href="<?php echo URLROOT; ?>/moviedatabase/shows"><b><?php echo Lang::T("Show"); ?></b></a> | 
            <a href="<?php echo URLROOT ?>/moviedatabase/movies"><b><?php echo Lang::T("Movie"); ?></b></a>
        </div>
        <?php
        $tmdb = new TMDB();

        // 1. Search TVShow
        echo '<ol><li><a id="searchTVShow"><h3>Search TVShow</h3></a><ul>';
        $tvShows = $tmdb->searchMovie("breaking bad");
        foreach ($tvShows as $tvShow) {
            echo '<li>' . $tvShow->getTitle() . ' (<a href="https://www.themoviedb.org/tv/' . $tvShow->getID() . '">' . $tvShow->getID() . '</a>)</li>';
        }
        echo '</ul></li><hr>';

        // 2. Full Movie Info
        echo '<li><a id="tvShowInfo"><h3>Full TVShow Info</h3></a>';
        $tvShow = $tmdb->getTVShow(1396);
        echo 'Now the <b>$tvShow</b> var got all the data, check the <a href="http://code.octal.es/php/tmdb-api/class-TVShow.html">documentation</a> for the complete list of methods.<br><br>';
        echo '<b>' . $tvShow->getName() . '</b><ul>';
        echo '  <li>ID:' . $tvShow->getID() . '</li>';
        echo '  <li>Overview: ' . $tvShow->getOverview() . '</li>';
        echo '  <li>Number of Seasons: ' . $tvShow->getNumSeasons() . '</li>';
        echo '  <li>Seasons: <ul>';
        $seasons = $tvShow->getSeasons();
        foreach ($seasons as $season) {
            echo '<li><a href="https://www.themoviedb.org/tv/season/' . $season->getID() . '">Season ' . $season->getSeasonNumber() . '</a></li>';
        }
        echo ' </ul></ul>';
        echo '<img src="' . $tmdb->getImageURL('w185') . $tvShow->getPoster() . '"/><br>...<hr>';

        // 3 Get Season Info
        echo '<li><a id="seasonInfo"><h3>Full Season Info</h3></a>';
        $season = $tmdb->getSeason($tvShow->getID(), 2);
        echo 'Now the <b>$season</b> var got all the data, check the <a href="http://code.octal.es/php/tmdb-api/class-Season.html">documentation</a> for the complete list of methods.<br><br>';
        echo '<b>' . $season->getName() . '</b><ul>';
        echo '  <li>ID: ' . $season->getID() . '</li>';
        echo '  <li>AirDate: ' . $season->getAirDate() . '</li>';
        echo '  <li>Number of Episodes: ' . $season->getNumEpisodes() . '</li>';
        echo '  <li>Episodes: <ul>';
        $episodes = $season->getEpisodes();
        foreach ($episodes as $episode) {
            echo '<li><a href="https://www.themoviedb.org/tv/' . $episode->getTVShowID() . '/season/' . $episode->getSeasonNumber() . '/episode/' . $episode->getEpisodeNumber() . '">' . $episode->getEpisodeNumber() . ' - ' . $episode->getName() . '</a></li>';
        }
        echo ' </ul></ul>...<hr>';

        // 4 Get Episode Info
        echo '<li><a id="episodeInfo"><h3>Full Episode Info</h3></a>';
        $episode = $tmdb->getEpisode($tvShow->getID(), 2, 8);
        echo 'Now the <b>$episode</b> var got all the data, check the <a href="http://code.octal.es/php/tmdb-api/class-Episode.html">documentation</a> for the complete list of methods.<br><br>';
        echo '<b>' . $episode->getEpisodeNumber() . ' - ' . $episode->getName() . '</b><ul>';
        echo '  <li>ID: ' . $episode->getID() . '</li>';
        echo '  <li>AirDate: ' . $episode->getAirDate() . '</li>';
        echo '  <li>Vote Average: ' . $episode->getVoteAverage() . '</li>';
        echo '  <li>Vode Count: ' . $episode->getVoteCount() . '</li>';
        echo ' </ul></ul>...<hr>';
        Style::end();
        Style::footer();
    }

    // Search Movies
    public function movies()
    {
        Style::header('Movies');
        Style::begin('Movies');
        ?>
        <div class="margin-top20 text-center"><br>
            <a href="<?php echo URLROOT; ?>/moviedatabase/persons"><b><?php echo Lang::T("Search Person"); ?></b></a> | 
            <a href="<?php echo URLROOT; ?>/moviedatabase/show"><b><?php echo Lang::T("Search Show"); ?></b></a> | 
            <a href="<?php echo URLROOT ?>/moviedatabase/movie"><b><?php echo Lang::T("Search Movie"); ?></b></a><br>
            
            <a href="<?php echo URLROOT; ?>/moviedatabase/person"><b><?php echo Lang::T("Person"); ?></b></a> | 
            <a href="<?php echo URLROOT; ?>/moviedatabase/shows"><b><?php echo Lang::T("Show"); ?></b></a> | 
            <a href="<?php echo URLROOT ?>/moviedatabase/movies"><b><?php echo Lang::T("Movie"); ?></b></a>
        </div>
        <?php
        $tmdb = new TMDB();
        // 1. Search Movie
        echo '<ol><li><a id="searchMovie"><h3>Search Movie</h3></a><ul>';
        $movies = $tmdb->searchMovie("underworld");
        foreach ($movies as $movie) {
            echo '<li>' . $movie->getTitle() . ' (<a href="https://www.themoviedb.org/movie/' . $movie->getID() . '">' . $movie->getID() . '</a>)</li>';
        }
        echo '</ul></li><hr>';

        // 2. Full Movie Info
        echo '<li><a id="movieInfo"><h3>Full Movie Info</h3></a>';
        $movie = $tmdb->getMovie(11);
        echo 'Now the <b>$movie</b> var got all the data, check the <a href="http://code.octal.es/php/tmdb-api/class-Movie.html">documentation</a> for the complete list of methods.<br><br>';
        echo '<b>' . $movie->getTitle() . '</b><ul>';
        echo '  <li>ID:' . $movie->getID() . '</li>';
        echo '  <li>Tagline:' . $movie->getTagline() . '</li>';
        echo '  <li>cast:</li>';
        echo '  <li>Trailer: <a href="https://www.youtube.com/watch?v=' . $movie->getTrailer() . '">link</a></li>';
        echo '</ul>...';
        echo '<img src="' . $tmdb->getImageURL('w185') . $movie->getPoster() . '"/></li>';

        // 3. Now Playing Movies
        echo '<li><a id="nowPlayingMovies"><h3>Now Playing Movies</h3></a><ul>';
        $movies = $tmdb->nowPlayingMovies();
        foreach ($movies as $movie) {
            echo '<li>' . $movie->getTitle() . ' (<a href="https://www.themoviedb.org/movie/' . $movie->getID() . '">' . $movie->getID() . '</a>)</li>';
        }
        echo '</ul></li><hr>';

        // 4. Latest Movie

        echo '<li><a id="latestMovie"><h3>Latest Movie</h3></a>';
        $movie = $tmdb->getLatestMovie();
        echo '- ' . $movie->getTitle() . ' (<a href="https://www.themoviedb.org/movie/' . $movie->getID() . '">' . $movie->getID() . '</a>)<br>';
        echo '</li><hr>';

        // 5. Search Collection
        echo '<li><a id="searchCollection"><h3>Search Collection</h3></a><ul>';
        $collections = $tmdb->searchCollection("the hobbit");
        foreach ($collections as $collection) {
            echo '<li>' . $collection->getName() . ' (<a href="https://www.themoviedb.org/collection/' . $collection->getID() . '">' . $collection->getID() . '</a>)</li>';
        }
        echo '</ul></li><hr>';

        // 6. Full Collection Info
        echo '<li><a id="collectionInfo"><h3>Full Collection Info</h3></a>';
        $collection = $tmdb->getCollection(121938);
        echo 'Now the <b>$collection</b> var got all the data, check the <a href="http://code.octal.es/php/tmdb-api/class-Collection.html">documentation</a> for the complete list of methods.<br><br>';
        echo '<b>' . $collection->getName() . '</b><ul>';
        echo '  <li>ID:' . $collection->getID() . '</li>';
        echo '  <li>Overview:' . $collection->getOverview() . '</li>';
        echo '  <li>Movies<ul>';
        $movies = $collection->getMovies();
        foreach ($movies as $movie) {
            echo '<li>' . $movie->getTitle() . ' (<a href="https://www.themoviedb.org/movie/' . $movie->getID() . '">' . $movie->getID() . '</a>)</li>';
        }
        echo '  </ul></li>';
        echo '</ul>...';
        echo '<img src="' . $tmdb->getImageURL('w185') . $collection->getPoster() . '"/></li>';
        Style::end();
        Style::footer();
    }
}