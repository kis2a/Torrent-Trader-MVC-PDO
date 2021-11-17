<?php
/**
 * 	This class handles all the data you can get from a Movie
 *
 * 	@author Alvaro Octal | <a href="https://twitter.com/Alvaro_Octal">Twitter</a>
 * 	@version 0.1
 * 	@date 09/01/2015
 * 	@link https://github.com/Alvaroctal/TMDB-PHP-API
 * 	@copyright Licensed under BSD (http://www.opensource.org/licenses/bsd-license.php)
 */

class Movie{
    const MEDIA_TYPE_MOVIE = 'movie';
    const CREDITS_TYPE_CAST = 'cast';
    const MEDIA_TYPE_TV = 'tv';
	//------------------------------------------------------------------------------
	// Class Variables
	//------------------------------------------------------------------------------

	//private $_data;
	private $_tmdb;

	/**
	 * 	Construct Class
	 *
	 * 	@param array $data An array with the data of the Movie
	 */
	public function __construct($data) {
		$this->_data = $data;
	}

	//------------------------------------------------------------------------------
	// Get Variables
	//------------------------------------------------------------------------------

	/** 
	 * 	Get the Movie's id
	 *
	 * 	@return int
	 */
	public function getID() {
		return $this->_data['id'];
	}

	/** 
	 * 	Get the Movie's title
	 *
	 * 	@return string
	 */
	public function getTitle() {
		return $this->_data['title'];
	}

	/** 
	 * 	Get the Movie's tagline
	 *
	 * 	@return string
	 */
	public function getTagline() {
		return $this->_data['tagline'];
	}


	/////////////////////////


	public function date() {
		$date = $this->_data['release_date'];
		$date_fr = strftime('%d-%m-%Y',strtotime($date));
		return $date_fr;
	} 
	

	public static function duree($time){
		$tabTemps = array("jours" => 86400,"h." => 60,"min." => 1);
		$result = "";
		foreach($tabTemps as $uniteTemps => $nombreSecondesDansUnite){
		$$uniteTemps = floor($time/$nombreSecondesDansUnite);
		$time = $time%$nombreSecondesDansUnite;
		if($$uniteTemps > 0 || !empty($result))
		$result .= $$uniteTemps." $uniteTemps ";
		}
		return $result;
		}
	/** 
	 * 	Get duration
	 *
	 * 	@return string
	 */
	public function duration() {
		$duration = $this->_data['runtime'];
		$duration = self::duree($duration);
		return $duration;
	} 
	
	
   public function getplot() {
	   return $this->_data['overview'];
   }
   
   
  public function getGenres() {
	  $genres = array();

	  foreach ($this->_data['genres'] as $data) {
		  $genres[] = new Genre($data);
		  
	  }

	  return $genres;
  }
  //genre
  public function genre() {
	  $nom ='';
	  $genres = $this->_data['genres'];
	  foreach($genres as $genre)
	  {
		  $nom .= $genre['name']. ', ';
	  }
	  return substr($nom, 0, -2); 
  }
  //actors
  public function actors() {
	  return $this->_data['credits'];
  }

  /** 
   * 	Get the Movie's trailer
   *
   * 	@return string
   */
  public function actor() {
	  $role ='';
	  $nom='';
	  $img='';
	  $actor = $this->actors();
	  for($i=0;$i<=3;$i++){
		  $role .= $actor['cast'][$i]['character'].' * ';
		  $nom .= $actor['cast'][$i]['name'].' + ';
		  $img .= 'http://image.tmdb.org/t/p/w92'.$actor['cast'][$i]['profile_path'].' & ';
	  }
	  
	  return array(substr($role, 0, -2),substr($nom, 0, -2),substr($img, 0, -2)); 
  }


//////////////////////////////// ?????????  	
  public function getCast(){
	return $this->getCredits('cast');
}

/**
 * Get the Cast or the Crew of an ApiObject
 * @param string $key
 * @return array of Person
 */
public function getCredits($key){
	$persons = [];

	foreach ($this->_data['cast'][$key] as $data) {
		$persons[] = new Person($data);
	}

	return $persons;
}

/**
 * Get the ApiObject crew
 * @return array of Person
 */
public function getCrew(){
	return $this->getCredits('cast');
}

  public function getDirectorIds() {

	$director_ids = [];

	$crew = $this->getCrew();

	/** @var Person $crew_member */
	foreach ($crew as $crew_member) {

		if ($crew_member->getJob() === Person::JOB_DIRECTOR){
			$director_ids[] = $crew_member->getID();
		}
	}
	return $director_ids;
}

public function getDirectorName() {

	$director_ids = [];

	$crew = $this->getCrew();

	/** @var Person $crew_member */
	foreach ($crew as $crew_member) {

		if ($crew_member->getJob() === Person::JOB_DIRECTOR){
			$director_ids[] = $crew_member->getName();
		}
	}
	return $director_ids;
}

public function creator() {
	$test = $this->get('director');
	return $test;
}

///////////////////////////////////////////////////////
   /** 
	 * 	Get the Movie's Poster
	 *
	 * 	@return string
	 */
	public function getPoster() {
		return $this->_data['poster_path'];
	}

	/** 
	 * 	Get the Movie's vote average
	 *
	 * 	@return int
	 */
	public function getVoteAverage() {
		return $this->_data['vote_average'];
	}

	/** 
	 * 	Get the Movie's vote count
	 *
	 * 	@return int
	 */
	public function getVoteCount() {
		return $this->_data['vote_count'];
	}

	/** 
	 * 	Get the Movie's trailers
	 *
	 * 	@return array
	 */
	public function getTrailers() {

		if (empty($this->_data['trailers']) && isset($this->_tmdb)){
			$this->loadTrailer();
		}

		return $this->_data['trailers'];
	}

	/** 
	 * 	Get the Movie's trailer
	 *
	 * 	@return string
	 */
	public function getTrailer() {
		return $this->getTrailers()['youtube'][0]['source'];
	}

	/**
	 *  Get Generic.<br>
	 *  Get a item of the array, you should not get used to use this, better use specific get's.
	 *
	 * 	@param string $item The item of the $data array you want
	 * 	@return array
	 */
	public function get($item = ''){
		return (empty($item)) ? $this->_data : $this->_data[$item];
	}

	//------------------------------------------------------------------------------
	// Load Variables
	//------------------------------------------------------------------------------

	/**
	 * 	Load the images of the Movie
	 *	Used in a Lazy load technique
	 */
	public function loadImages(){
		$this->_data['images'] = $this->_tmdb->getMovieInfo($this->getID(), 'images', false);
	}

	/**
	 * 	Load the trailer of the Movie
	 *	Used in a Lazy load technique
	 */
	public function loadTrailer() {
		$this->_data['trailers'] = $this->_tmdb->getMovieInfo($this->getID(), 'trailers', false);
	}

	/**
	 * 	Load the casting of the Movie
	 *	Used in a Lazy load technique
	 */
	public function loadCasting(){
		$this->_data['casts'] = $this->_tmdb->getMovieInfo($this->getID(), 'casts', false);
	}

	/**
	 * 	Load the translations of the Movie
	 *	Used in a Lazy load technique
	 */
	public function loadTranslations(){
		$this->_data['translations'] = $this->_tmdb->getMovieInfo($this->getID(), 'translations', false);
	}

	//------------------------------------------------------------------------------
	// Import an API instance
	//------------------------------------------------------------------------------

	/**
	 *	Set an instance of the API
	 *
	 *	@param TMDB $tmdb An instance of the api, necessary for the lazy load
	 */
	public function setAPI($tmdb){
		$this->_tmdb = $tmdb;
	}

	//------------------------------------------------------------------------------
	// Export
	//------------------------------------------------------------------------------

	/** 
	 * 	Get the JSON representation of the Movie
	 *
	 * 	@return string
	 */
	public function getJSON() {
		return json_encode($this->_data, JSON_PRETTY_PRINT);
	}
}
?>