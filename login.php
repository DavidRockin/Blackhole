<?php
include __DIR__ . "/library/core.php";

use App\Template,
	App\User
;

if (isset($_POST['login'])) {
	// TODO: authentication
	
	$fetchUser = $dbh->prepare("
		SELECT *
		FROM users
		WHERE `name` = :name
	");
	$fetchUser->execute([
		":name" => trim($_POST['name']),
	]);
	
	if ($fetchUser->rowCount() !== 0) {
		$userData = $fetchUser->fetch();
		
		if (password_verify($_POST['password'], $userData['password'])) {
			$_SESSION['LOGGED_IN']  = true;
			$_SESSION['USER_ID']    = $userData['user_id'];
			$_SESSION['LOGIN_TIME'] = time();
			
			header("Location: /");
			exit;
		} else {
			// TODO
		}
	} else {
		// TODO
	}
}

Template::header("Login");
?>


<h1>Login</h1>

<div class="clear"></div>

<form action="" method="POST">
	<div class="form-group">
		<label for="name">Your Name:</label>
		<input type="text" name="name" class="form-control" id="name" />
	</div>
	
	<div class="form-group">
		<label for="password">Password:</label>
		<input type="password" name="password" class="form-control" id="password" />
	</div>
	
	<button type="submit" name="login" class="btn btn-primary">Login</button>
	<a href="/" class="btn btn-default">Cancel</a>

</form>


<?php
Template::footer();
