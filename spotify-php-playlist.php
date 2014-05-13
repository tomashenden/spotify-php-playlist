<?php

/**
* spotify-php-playlist
*
* PHP Script to pull track information from any public 
* Spotify playlist.
*
* Author: Thomas Ashenden
* Website: tomashenden.com
*/


/**
* Get URI from GET request, and ensure it is sanitised
*/
$spotURI = $_GET['uri'];
// Match exact Spotify playlist URI pattern
if ($c=preg_match_all("/".'(spotify)'.'(:)'.'(user)'.'(:)'.'((?:[A-Za-z0-9_-]*))'.'(:)'.'(playlist)'.'(:)'.'((?:[A-Za-z0-9]*))'."/is", $spotURI, $matches)) {
      $userID = $matches[5][0];
      $playlistID = $matches[9][0];
}
else {
	echo 'Error: Could not parse URI. Script needs ?uri= with the Spotify playlist URI.';
	die;
}


/**
* Get the HTML from the embed player
*/
$uri = "https://embed.spotify.com/?uri=spotify:user:" . $userID . ":playlist:" . $playlistID;
$src = getsrc($uri);

$playlistName = getPlaylistName($src);
// An associative array with all track information
$playlistMusic = getMusic($src);


/**
* Display the output as requested, with HTML as default
*/
$output = (isset($_GET['output']) ? $_GET['output'] : "");
switch($output) {
	case "xml" : 
		displayAsXML($playlistName, $playlistMusic);
		break;
	case "json" :
		displayAsJSON($playlistName, $playlistMusic);
		break;
	default :
		displayAsHTML($playlistName, $playlistMusic);
		break;
}


die;



/*******************
* FUNCTIONS
*******************/

/**
* Display output as requested, default is HTML in a table
*/
function displayAsHTML($playlistName, $playlistMusic)
{
	if(strlen($playlistName) > 0) {
		echo '<h1>'.$playlistName.'</h1>';
	}
	// If playlist name and tracks exist print them, otherwise show empty
	if(count($playlistMusic) > 0 && strlen($playlistName) > 0) {
		echo '<table>';
		echo '<tr><th>ID</th><th>Title</th><th>Artist</th><th>Duration</th><th>Link</th></tr>';
		foreach($playlistMusic as $track) {
			echo '<tr><td>' . $track['id'] . '</td>';
			echo '<td>' . $track['title'] . '</td>';
			echo '<td>' . $track['artist'] . '</td>';
			echo '<td>' . $track['duration'] . '</td>';
			echo '<td><a href="' . $track['link'] . '">Link</a></td></tr>';
		}
		echo '</table>';
	}
	else {
		echo 'Playlist is empty.';
	}
}


/**
* Display output as XML in format
 <xml>
 	<playlist>
		<name><value>PlaylistName</value></name>
		<tracks>
			<track>
				<id><value>trackID</value></id>
				<title><value>trackTitle</value></title>
				<artist><value>trackArtist</value></artist>
				<duration><value>trackDuration</value></duration>
			</track>
			<track>
				...
			</track>
		</tracks
	</playlist>
  </xlm>
*/	
function displayAsXML($playlistName, $playlistMusic)
{
	$xml = new SimpleXMLElement('<xml />');
	$xml->addAttribute('encoding', 'UTF-8');

	$playlist = $xml->addChild('playlist');
	if(strlen($playlistName) > 0) {		
		$playlistNameXML = $playlist->addChild('name');
		$playlistNameXML->value = $playlistName;
	}
	if(count($playlistMusic) > 0 && strlen($playlistName) > 0) {
		$tracks = $playlist->addChild('tracks');
		foreach($playlistMusic as $track) {	
			$trackXML = $tracks->addChild('track');
			
			$idXML = $trackXML->addChild('id');
			$idXML->value = $track['id'];
			
			$titleXML = $trackXML->addChild('title');
			$titleXML->value = $track['title'];
			
			$artistXML = $trackXML->addChild('artist');
			$artistXML->value = $track['artist'];
			
			$durationXML = $trackXML->addChild('duration');
			$durationXML->value = $track['duration'];
			
			$linkXML = $trackXML->addChild('link');
			$linkXML->value = $track['link'];
		}
	}
	else {
		$tracks = $playlist->addChild('tracks');
	}

	Header('Content-type: text/xml');
	echo $xml->asXML();
}


/*
* Displays output as JSON in format:
{ "name":"PlaylistName",
	"tracks": [
			{ "id":trackID,
			  "title":"trackTitle",
			  "artist":"trackArtist",
			  "duration":"duration"
			}, { ... }
		] }
*/
function displayAsJSON($playlistName, $playlistMusic)
{
	$jsonArray = array("name"=>$playlistName);
	$jsonArray['tracks'] = $playlistMusic;
	
	Header('Content-type: application/json');
	echo json_encode($jsonArray);
}


/**
* Pulls information from Spotify embed page.
*/
function getMusic($src) 
{
	$playlistMusic = array();
	$explodedSrc = explode('<ul class="track-info">',$src);
	$explodedTrackIDs = explode('data-track=',$src);

	for($i=1; $i < count($explodedSrc); $i++) {
		$tmp = array();
		$tmp['id'] = $i;
		
		$explodeTmp = explode('<li class="track-title',$explodedSrc[$i]);
		$tmp['title'] = substr(strstr(getInnerText($explodeTmp[1], '">', '</li>'),'.'),1);
		
		$explodeTmp = explode('<li class="artist',$explodedSrc[$i]);
		$tmp['artist'] = getInnerText($explodeTmp[1], '">', '</li>');
		
		$explodeTmp = explode('<li class="duration" rel="',$explodedSrc[$i]);
		$tmp['duration'] = getInnerText($explodeTmp[1], '">', '</li>');
		
		$tmp['link'] = 'http://open.spotify.com/track/' . getInnerText($explodedTrackIDs[$i+1], '"', '"');
		$playlistMusic[] = $tmp;
	}
	return $playlistMusic;
}
	

/**
* Gets the playlist name from the src HTML
*/
function getPlaylistName($src) 
{
	return getInnerText($src, '<div class="title-content ellipsis">', '</div>');
}


/**
* Gets a given string from between two other strings
*/
function getInnerText($src,$start,$end)
{
    $startTag = explode($start, $src);
    $endTag = explode($end, $startTag[1]);
    return trim($endTag[0]);
}

/**
* Pulls HTML from a URL
*/
function getsrc($url)
{
    $c = curl_init();
    $user_agent = "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.116 Safari/537.36";
    curl_setopt($c, CURLOPT_URL, $url);
    curl_setopt($c, CURLOPT_USERAGENT, $user_agent);
    curl_setopt($c, CURLOPT_POST, 0);
    curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($c, CURLOPT_FRESH_CONNECT, true);
    $html = curl_exec($c);
	curl_close($c);
    
    return $html;
}