<?php
include __DIR__ . "/library/core.php";

use App\Template,
	App\User
;

$errors = [];

if (isset($_POST['login'])) {
	if (isset($_POST['name']) && !empty($_POST['name'])) {
		$name = trim($_POST['name']);
	} else {
		$errors[] = "You must specify your name";
	}
	
	if (isset($_POST['password']) && !empty($_POST['password'])) {
		$password = trim($_POST['password']);
	} else {
		$errors[] = "You must specify your password";
	}
	
	if (empty($errors)) {
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
				$errors[] = "Invalid username or password";
			}
		} else {
			$errors[] = "The account specified does not exist";
		}
	}
}

Template::header("Login");

if (!empty($errors)) {
	echo "<div class='alert alert-danger'>
		<strong>An error has occurred!</strong> " . $errors[0] .
	"</div>";
}
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
