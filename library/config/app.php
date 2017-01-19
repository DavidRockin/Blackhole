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
	"<em>&ldquo;Physics is like sex: Sure it might give some practical results, but that's not why we do it. &rdquo;</em> &ndash; Richard P. Feynman",
	"<em>May contain security vulnerabilities!</em>",
	"May contain nuts!",
	"NP is not in P!",
	"One day, somewhere in the future, my work will be quoted!",
	"<a href='https://www.youtube.com/watch?v=6FzQ_s-BjlM' target='_blank'><em>Middle-out Friendly!</em></a>",
	"If you keep screaming your name, it forces the assaailant to acknowlege you as a human.",
	"<em>Sometimes I have nightmares that I forgot to backup my systems.</em>",
	"<em>I know HTML &ndash; How to meet ladies</em>",
	"Investor-friendly",
	"<span style='font-size:14pt;color:gold'>Now accepts Bitcoin!</span>",
	"<em>Tabs friendly</em>",
	"<em>Built with Vim!</em>",
	"<em>Every day feels like I've died and gone to hell.</em>",
	"BlackHole is not a scam",
	"Pied Piper!",
	"You are the semicolon to my statements;",
	"My code doesn't works and I have no idea why. My code works and I have no idea why.",
	"<em>My code never has bugs, it just develops random unexpected features</em>",
	"<em><strong>Programmer</strong> <small>(noun.)</small></em> &ndash; A person who fixed a problem that you don't know you have in a way you don't understand.",
	"<em><strong>Programmer</strong> <small>(noun.)</small></em> &ndash; A machine that turns coffee into code.",
	"99 little bugs in the code, 99 litte bugs in the code. Take one down, patch it around. 127 little bugs in the code.",
	"<br /><img src='http://i.imgur.com/OPkKwoh.jpg' /><br />",
	"<em><strong>LIKE AND SUBSCRIBE!</strong></em>",
	"<em><strong>In this video...</strong></em>",
];

// date formatting
$config['dateFormatSimple']  = "F d, Y";
$config['dateFormatComplex'] = "F d, Y H:i:s P";

return $config;
