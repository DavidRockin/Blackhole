<?php
include __DIR__ . "/library/core.php";

use App\Template;

Template::header("Ticket");

if (isset($_GET['id']))
    $id = intval($_GET['id']);

$getTicket = $dbh->prepare("
    SELECT t.*, q.rank, c.name as category_name
    FROM tickets t
    LEFT JOIN (
        SELECT *
        FROM (
                SELECT *, @rank := @rank + 1 as rank
                FROM tickets, (SELECT @rank := 0) r
                WHERE status = 0
                ORDER BY date_updated
        ) t
        WHERE t.ticket_id = :ticketId
    ) q
    ON q.ticket_id = t.ticket_id
    LEFT JOIN categories c
    ON c.category_id = t.category_id
    WHERE t.ticket_id = :ticketId
");
$getTicket->execute([
    ":ticketId" => $id,
]);

if ($getTicket->rowCount() === 0) {
    echo "<h1>No ticket found</h1>";
    exit;
}

$ticket = $getTicket->fetch(\PDO::FETCH_ASSOC);

if (isset($_GET['action'])) {
	if ($_GET['action'] === "close" || ($user->rank === "1" || (\App\Auth::isLoggedIn() && \App\Auth::getUserId() === $ticket['user_id']))) {
		$updateTicket = $dbh->prepare("
			UPDATE tickets
			SET status = 1
			WHERE ticket_id = :ticketId
		");
		$updateTicket->execute([
			":ticketId" => $ticket['ticket_id'],
		]);
		
		$ticket['status'] = 1;
	}
}


if (isset($_POST['reply']) && $ticket['status'] == 1) {
    // TODO: add validation
    
    $_SESSION['name'] = trim($_POST['name']);
    
    // create message
    $createMessage = $dbh->prepare("
        INSERT INTO ticket_messages
        VALUES (null, :ticketId, :name, :userId, UNIX_TIMESTAMP(NOW()), :message)
    ");
    $createMessage->execute([
        ":ticketId" => $ticket['ticket_id'],
        ":name"     => trim($_POST['name']),
        ":message"  => trim($_POST['message']),
        ":userId"   => \App\Auth::getUserId(),
    ]);
    
	// update the ticket
	$updateTicket = $dbh->prepare("
		UPDATE tickets SET date_updated = UNIX_TIMESTAMP(NOW())
		WHERE ticket_id = :ticketId
	");
	$updateTicket->execute([
		":ticketId" => $ticket['ticket_id'],
	]);
	
}

?>

<div class="row">
	<div class="col-md-8">
		<h1 style="margin:0px;padding:0px"><?=htmlentities($ticket['subject'])?></h1>
	</div>
	<div class="col-md-4">

<?php
if ($user->rank === "1" || (\App\Auth::isLoggedIn() && \App\Auth::getUserId() === $ticket['user_id'])) {
?> 
		<a href="?action=close&id=<?=$ticket['ticket_id']?>" class="btn btn-danger pull-right">Close Ticket</a>
<?php
}

if ($user->rank === "1") {
?> 
		<a href="?action=merge&id=<?=$ticket['ticket_id']?>" class="btn btn-warning pull-right">Merge Ticket</a>
<?php
}
?>
		<a href="/tickets.php?status=open" class="btn btn-primary pull-right">&laquo; Return</a>
	</div>
</div>

<div class="clear"></div>

<?php
$getMessages = $dbh->prepare("
    SELECT tm.*, u.rank
    FROM ticket_messages tm
	LEFT JOIN users u
	ON u.user_id = tm.author_id
    WHERE tm.ticket_id = :ticketId
    ORDER BY ABS(tm.date_created);
");
$getMessages->execute([
    ":ticketId" => $ticket['ticket_id'],
]);

while ($message = $getMessages->fetch()) {
?>

<div class="clear"></div>

<div class="panel panel-<?=($message['rank'] === "1" ? "danger" : "primary")?>">
	<div class="panel-heading"><?=htmlentities($message['author_name']) . " " . date("r", $message['date_created'])?></div>
	<div class="panel-body">
		<?=htmlentities($message['message'])?>
	</div>
</div>

<?php
}
?>

<div class="clear"></div>

<hr />

<div class="panel panel-info">
	<div class="panel-heading">Ticket Overview</div>
	<div class="panel-body">
		<div class="row">
			<div class="col-md-3">
				<strong>Total Replies:</strong>
				<span class="label label-primary pull-right"><?=number_format($getMessages->rowCount() - 1)?></span>
			</div>
			
			<div class="col-md-3">
				<strong>Created:</strong>
				<span class="label label-primary pull-right"><?=\App\Format::formatSimpleTimestamp($ticket['date_created'])?></span>
			</div>
			
			<div class="col-md-3">
				<strong>Updated:</strong>
				<span class="label label-primary pull-right"><?=\App\Format::formatSimpleTimestamp($ticket['date_updated'])?></span>
			</div>
			
			<div class="col-md-3">
				<strong>Category:</strong>
				<span class="label label-primary pull-right"><?=htmlentities($ticket['category_name'])?></span>
			</div>
		</div>
		
		<div class="row">
			<div class="col-md-3">
				<strong>Position in Queue:</strong>
				<span class="label label-primary pull-right"><?=number_format($getMessages->rowCount() - 1)?></span>
			</div>
			
			<div class="col-md-3">
				<strong>Status:</strong>
				<?=getStatus($ticket['status'], " pull-right")?>
			</div>
			
			<div class="col-md-3">
				<strong>Average Response Time:</strong>
				<span class="label label-primary pull-right">TBD</span>
			</div>
		</div>
	</div>
</div>

<?php
if ($ticket['status'] != 1) {
?>

<div class="clear"></div>

<div class="panel panel-info">
	<div class="panel-heading">Reply to Ticket</div>
	<div class="panel-body">
		<form action="" method="POST">
			<div class="form-group">
				<label for="name">Your Name:</label>
				<input type="text" name="name" class="form-control" id="name" value="<?=isset($_SESSION['name']) ? htmlentities($_SESSION['name']) : ""?>" />
			</div>
			
			<div class="form-group">
				<label for="message">Message:</label>
				<textarea name="message" class="form-control" id="message"></textarea>
			</div>
			
			<button type="submit" name="reply" class="btn btn-primary">Reply</button>
		</form>
	</div>
</div>

<?php

}


Template::footer();

