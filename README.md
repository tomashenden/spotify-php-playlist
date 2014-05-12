spotify-php-playlist
====================

spotify-php-playlist is a lightweight PHP API to pull track details in either HTML, XML or JSON format, from any public Spotify playlist. The API can pull the playlist name, track titles, artists, duration, and links to play on the Spotify website.

How to use
==========
Run the php page with the following GET parameters:

output - The disired output format, either html, xml or json

uri - The uri value is from right clicking the desired playlist and clicking "Copy Spotify URI"

e.g. /spotify-php-playlist.php?output=json&uri=spotify:user:11120006795:playlist:3aPdhb6UvgYp0kpxnokNTH


Output
=======
To see the structure of each format, please refer to the comments in spotify-php-playlist

?output=xml

?output=json

?output=html


Example Use
===========

You can see an example of the script running with an example playlist here:
http://tomashenden.com/get-spotify-playlists-in-php-as-html-xml-or-json

You can see how this script could be utilised using xml from the example file spotify-php-example.php
