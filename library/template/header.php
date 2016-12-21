<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta name="description" content="" />
	<meta name="author" content="" />
	
	<title><?=$title?></title>
	
	<link href="/assets/css/bootstrap.min.css" rel="stylesheet" />
	<link href="/assets/css/ie10-viewport-bug-workaround.css" rel="stylesheet" />
	<link href="/assets/css/styles.css" rel="stylesheet" />
</head>

<body>
	<nav class="navbar navbar-fixed-top navbar-inverse">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="/"><img src="/assets/images/Black-Hole-Logo-White.png" style="max-height:100%" /></a>
			</div>
			<div id="navbar" class="collapse navbar-collapse">
				<ul class="nav navbar-nav navbar-left">
					<li><a href="/tickets.php">View Tickets</a></li>
<?php
if (\App\Auth::isLoggedIn()) {
?>
					<li><a href="/logout.php">Logout</a></li>
<?php
} else {
?>
					<li><a href="/login.php">Login</a></li>
<?php
}
?>
				</ul>

				<form class="navbar-form navbar-right" method="GET" action="/search.php">
					<div class="input-group">
						<input type="text" class="form-control" name="q" placeholder="Search..." />
						<span class="input-group-btn">
							<button class="btn" type="button">&raquo;</button>
						</span>
					</div>
				</form>
			</div>
		</div><!-- /.container -->
	</nav><!-- /.navbar -->
	
	<div class="container">
		<div class="row row-offcanvas row-offcanvas-right">
