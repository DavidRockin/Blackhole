<?php
include __DIR__ . "/library/core.php";

use App\Template;

Template::header("Create Ticket");

if (isset($_POST['create'])) {
    // TODO: add validation
    
    $_SESSION['name'] = trim($_POST['name']);
    
    // insert ticket info
    $createTicket = $dbh->prepare("
        INSERT INTO tickets
        VALUES (NULL, :subject, :name, :userId, :category, UNIX_TIMESTAMP(NOW()), UNIX_TIMESTAMP(NOW()), 0)
    ");
    $createTicket->execute([
        ":subject" => trim($_POST['subject']),
        ":name" => trim($_POST['name']),
        ":category" => intval($_POST['category']),
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
        ":name"     => trim($_POST['name']),
        ":message"  => trim($_POST['message']),
        ":userId"   => \App\Auth::getUserId(),
    ]);
    
    
    // redirect to ticket page
    header("Location: /ticket.php?id=" . $ticketId);
    exit;
    
}
?>

<h1>Create Ticket</h1>

<div class="btn-group">
    <a href="/tickets.php?status=open" class="btn btn-default">View Opened Tickets</a>
    <a href="/tickets.php?status=all" class="btn btn-default">View All Tickets</a>
    <a href="/tickets.php?status=closed" class="btn btn-default">View Closed Tickets</a> 
</div>

<div class="clear"></div>

<form action="" method="POST">
	<div class="form-group">
		<label for="name">Your Name:</label>
		<input type="text" name="name" class="form-control" id="name" value="<?=isset($_SESSION['name']) ? htmlentities($_SESSION['name']) : ""?>"/>
	</div>
	
	<div class="form-group">
		<label for="subject">Subject:</label>
		<input type="text" name="subject" class="form-control" id="subject" />
	</div>
	
	
	<div class="form-group">
		<label for="category">Category:</label>
		<select name="category" class="form-control" id="category">
<?php
$categories = getCategories();
foreach ($categories as $category)
	echo "<option value='" . $category['category_id'] . "'>" . $category['name'] . "</option>" . PHP_EOL;
?>
		</select>
	</div>
	
	<div class="form-group">
		<label for="message">Message:</label>
		<textarea name="message" class="form-control" id="message" style="height:200px"></textarea>
	</div>
	
	<button type="submit" name="create" class="btn btn-primary">Create</button>

</form>


<?php
Template::footer();
