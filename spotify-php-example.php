<?php

/**
* spotify-php-playlist-example
*
* Pulls the playlist information from the API and
* assigns them to variables.
*
* Author: Thomas Ashenden
* Website: tomashenden.com
*/



// Get json from script
$uri = "http://localhost/spotify-php-playlist.php?uri=spotify:user:11120006795:playlist:3aPdhb6UvgYp0kpxnokNTH&output=json";
$src = getsrc($uri);

$data = json_decode($src);

// Playlist name
$playlistName = $data->name;
// Playlist music
$playlistMusic = $data->tracks;
// Access individual tracks
foreach($playlistMusic as $track) {
	$trackId = $track->id;
	$trackTitle = $track->title;
	$trackArtist = $track->artist;
	$trackDuration = $track->duration;
	$trackLink = $track->link;
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