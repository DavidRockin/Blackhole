<?php
include __DIR__ . "/library/core.php";

use App\Template;

if (!\App\Auth::isLoggedIn()) {
    header("Location: /login.php");
    exit;
}

Template::header("Update Account");

$errors = [];

$userId = \App\Auth::getUserId();
$userId = ($user->rank === "1" ? (isset($_GET['id']) ? intval($_GET['id']) : $userId) : $userId);

$account = new \App\User($dbh, $userId);

if (isset($_POST['update'])) {
    // too lazy to add error checking
    
    $updateUser = $dbh->prepare("
        UPDATE users
        SET name = :name,
            `password` = :password,
            rank = :rank
        WHERE user_id = :userId
    ");
    $updateUser->execute([
        ":name"     => trim($_POST['name']),
        ":password" => !(isset($_POST['password']) && !empty($_POST['password'])) ? $account->password : password_hash($_POST['password'], PASSWORD_DEFAULT),
        ":rank"     => ($user->rank === "1" && isset($_POST['rank']) ? intval($_POST['rank']) : $account->rank),
        ":userId"   => $account->user_id,
    ]);
    
    // lazily update user info
    $account = new \App\User($dbh, $userId);
}


?>

<h1>Update Account</h1>

<div class="clear"></div>

<form action="<?=(isset($_GET['id']) ? "?id=" . $userId : "")?>" method="POST">
	<div class="form-group">
		<label for="name">Name:</label>
		<input type="text" name="name" class="form-control" id="name" value="<?=htmlentities($account->name)?>" />
	</div>
	
	<div class="form-group">
		<label for="password">Password:</label>
		<input type="password" name="password" class="form-control" id="password" placeholder="Specify new password to change" />
	</div>
	
<?php
if ($user->rank === "1") {
?>
	<div class="form-group">
		<label for="rank">Rank:</label>
		<select name="rank" class="form-control" id="rank">
            <option value="0"<?=$user->rank == 0 ? " selected" : ""?>>Normal User</option>
            <option value="1"<?=$user->rank == 1 ? " selected" : ""?>>Administrator</option>
    	</select>
	</div>
<?php
}
?>

	<button type="submit" name="update" class="btn btn-primary">Update</button>

</form>


<?php
Template::footer();
