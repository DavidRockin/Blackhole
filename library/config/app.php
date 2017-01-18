<?php

$config = [];

// production server
if (gethostname() === "server") {
	$config['dsn']  = "mysql:host=127.0.0.1;port=3306;dbname=blackhole"; //;unix_socket=/var/run/mariadb/mariadb.sock";
	$config['user'] = "blackhole";
	$config['pass'] = "53cur3p455w0rd69!";
} else {
	$config['dsn']  = "mysql:host=127.0.0.1;port=3306;dbname=blackhole";
	$config['user'] = "root";
	$config['pass'] = "";
}

$config['memes'] = [
	'A legally "illegal" tech company in the hot Brantford Tech Valley.',
	'BlackHole is not production-ready!',
	'Distance conversion is the powerhouse of physics!',
	'If the objects with higher mass have more gravitational pull, does that mean the fattest person in the world is also the most attractive?',
	"I'll have you know, I took physics... And only cried myself to sleep every night.",
	"Watt is love? Baby don't hertz me...",
	"<em>&ldquo;Physics is like sex: Sure it might give some practical results, but that's not why we do it. &rdquo;</em> &ndash; Richard P. Feynman"
];

// date formatting
$config['dateFormatSimple']  = "F d, Y";
$config['dateFormatComplex'] = "F d, Y H:i:s P";

return $config;
