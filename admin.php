<?php
include __DIR__ . "/library/core.php";

use App\Template;

if (!\App\Auth::isLoggedIn()) {
    header("Location: /login.php");
    exit;
}

if ($user->rank !== "1") {
    header("Location: /");
    exit;
}

Template::header("Administration");

if (isset($_POST['create'])) {
    // too lazy to add error checking
    
    $createUser = $dbh->prepare("
        INSERT INTO users
        VALUES(null, :name, :password, UNIX_TIMESTAMP(NOW()), 0, NULL, :rank)
    ");
    $createUser->execute([
        ":name"     => trim($_POST['name']),
        ":password" => password_hash($_POST['password'], PASSWORD_DEFAULT),
        ":rank"     => intval($_POST['rank']),
    ]);
    
}


?>

<h1>View Accounts</h1>

<div class="table-responsive">
<table class="table table-bordered table-hover">
    <thead>
        <tr>
            <th>User ID</th>
            <th>Name</th>
            <th>Registered</th>
            <th>Rank</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
<?php
$getUsers = $dbh->query("SELECT * FROM users");
while ($row = $getUsers->fetch()) {
    echo "<tr>
            <td>" . $row['user_id'] . "</td>
            <td>" . htmlentities($row['name']) . "</td>
            <td>" . \App\Format::getTimeElapsed($row['date_created']) . "</td>
            <td>" . ($row['rank'] == "1" ? "Administrator" : "User") . "</td>
            <td><a href='/account.php?id=" . $row['user_id'] . "'>Modify</a></td>
        </tr>";
}
?>
    </tbody>
</table>

<hr />

<h1>Create Account</h1>

<div class="clear"></div>

<form action="" method="POST">
	<div class="form-group">
		<label for="name">Name:</label>
		<input type="text" name="name" class="form-control" id="name" value="" />
	</div>
	
	<div class="form-group">
		<label for="password">Password:</label>
		<input type="password" name="password" class="form-control" id="password" placeholder="" />
	</div>

	<div class="form-group">
		<label for="rank">Rank:</label>
		<select name="rank" class="form-control" id="rank">
            <option value="0">Normal User</option>
            <option value="1">Administrator</option>
    	</select>
	</div>

	<button type="submit" name="create" class="btn btn-primary">Create Account</button>

</form>


<?php
Template::footer();
