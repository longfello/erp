<?php

// https://aprill.ru/files/FUlH

$domain = '//jarvis.aprill.ru';

$uri = $_SERVER['REQUEST_URI'];
$uri = str_replace('frame', 'files', $uri);
$url = $domain.$uri;
?>
<html>
  <head>
	  <style>
		  html, body, iframe {
			  margin: 0;
			  padding: 0;
			  height : 100%;
		  }
		  iframe {
			  display: block;
			  width: 100%;
			  border: none;
		  }
	  </style>
  </head>
  <body>
    <iframe src="<?= $url ?>"></iframe>
  </body>
</html>
