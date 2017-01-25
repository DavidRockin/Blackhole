<?php
include __DIR__ . "/library/core.php";

use App\Template;

Template::header("Create Ticket");

$errors = [];

if (isset($_POST['create'])) {
    if (isset($_POST['name']) && !empty($_POST['name'])) {
    	$name = trim($_POST['name']);
    	$_SESSION['name'] = $name;
    } else {
    	$errors[] = "Please specify a name";
    }
    
    if (isset($_POST['message']) && !empty($_POST['message'])) {
    	$message = trim($_POST['message']);
    } else {
    	$errors[] = "Please specify a message";
    }
    
    if (isset($_POST['subject']) && !empty($_POST['subject'])) {
    	$subject = trim($_POST['subject']);
    } else {
    	$errors[] = "Please specify a subject";
    }
    
    if (isset($_POST['category']) && !empty($_POST['category']) && ctype_digit($_POST['category'])) {
    	$category = intval($_POST['category']);
    } else {
    	$errors[] = "Please select a valid category";
    }
    
    // TODO file upload validation here ...
    
    if (empty($errors)) {
	    // insert ticket info
	    $createTicket = $dbh->prepare("
	        INSERT INTO tickets
	        VALUES (NULL, :subject, :name, :userId, :category, UNIX_TIMESTAMP(NOW()), UNIX_TIMESTAMP(NOW()), 0)
	    ");
	    $createTicket->execute([
	        ":subject"  => $subject,
	        ":name"     => $name,
	        ":category" => $category,
	        ":userId"   => \App\Auth::getUserId(),
	    ]);

	    // ticket id
	    $ticketId = $dbh->lastInsertId();
	    
	    // create message
	    $createMessage = $dbh->prepare("
	        INSERT INTO ticket_messages
	        VALUES (null, :ticketId, :name, :userId, UNIX_TIMESTAMP(NOW()), :message)
	    ");
	    $createMessage->execute([
	        ":ticketId" => $ticketId,
	        ":name"     => $name,
	        ":message"  => $message,
	        ":userId"   => \App\Auth::getUserId(),
	    ]);

		// get the message id
		$messageId = $dbh->lastInsertId();

		processAttachments($messageId);
		$_SESSION['q'] = true;

		// redirect to ticket page
		header("Location: /ticket.php?id=" . $ticketId);
		exit;
	}
}

if (!empty($errors)) {
	echo "<div class='alert alert-danger'>
		<strong>An error has occurred!</strong> " . $errors[0] .
	"</div>";
}
?>

<h1>Create Ticket</h1>

<div class="btn-group">
    <a href="/tickets.php?status=open" class="btn btn-default">View Opened Tickets</a>
    <a href="/tickets.php?status=all" class="btn btn-default">View All Tickets</a>
    <a href="/tickets.php?status=closed" class="btn btn-default">View Closed Tickets</a> 
</div>

<div class="clear"></div>

<form action="/create.php" method="POST" class="dropzone" style="border:0px" id="dropzone" enctype="multipart/form-data">
	<input type="hidden" name="create" value="hack" />

	<div class="form-group">
		<label for="name">Your Name:</label>
		<input type="text" name="name" class="form-control" id="name" value="<?=htmlentities(isset($_SESSION['name']) ? $_SESSION['name'] : (isset($_POST['name']) ? trim($_POST['name']) : ""))?>" />
	</div>
	
	<div class="form-group">
		<label for="subject">Subject:</label>
		<input type="text" name="subject" class="form-control" id="subject" value="<?=htmlentities(isset($subject) ? $subject : '')?>" />
	</div>
	
	
	<div class="form-group">
		<label for="category">Category:</label>
		<select name="category" class="form-control" id="category">
<?php
$catId = isset($category) ? $category : 0;

$categories = getCategories();
foreach ($categories as $category)
	echo "<option value='" . $category['category_id'] . "'" .
		($category['category_id'] == $catId ? " selected": "") . ">"
	. $category['name'] . "</option>" . PHP_EOL;
?>
		</select>
	</div>
	
	<div class="form-group">
		<label for="message">Message:</label>
		<textarea name="message" class="form-control" id="message" style="height:200px"><?=isset($message) ? htmlentities($message) : ""?></textarea>
	</div>
	
	<div class="dropzone-previews"></div>
	<div class="fallback">
		<input name="file" type="file" multiple />
	</div>
	
	<button type="submit" class="btn btn-primary">Create</button>

</form>


<?php
Template::footer();
