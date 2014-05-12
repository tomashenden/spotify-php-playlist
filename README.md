spotify-php-playlist
====================

spotify-php-playlist is a PHP API to pull track details in either HTML, XML or JSON format, from any public Spotify playlist.

How to use
==========
Run the php page with the following GET parameters:

output - The disired output format, either html, xml or json

uri - The uri value is from right clicking the desired playlist and clicking "Copy Spotify URI"

e.g. /spotify-php-playlist.php?output=json&uri=spotify:user:11120006795:playlist:3aPdhb6UvgYp0kpxnokNTH


Outputs
=======
?output=xml
Displays track information in the following layout:
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

?output=json
Displays track information in the following layout:
{ "name":"PlaylistName",
	"tracks": [
			{ "id":trackID,
			  "title":"trackTitle",
			  "artist":"trackArtist",
			  "duration":"duration"
			}, { ... }
		] 
}

?output=
By default the track information is displayed in a HTML table.


Specifying Spotify Playlist URI
===============================
Right click on a playlist in Spotify and click "Copy Spotify URI", and paste it with the parameter uri=


Example Use
===========

You can see an example of the script running with an example playlist here:
http://tomashenden.com/<link here>

You can see how this script could be utilised using xml from spotify-php-playlist-test.php
