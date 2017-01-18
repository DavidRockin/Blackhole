<?php
include __DIR__ . "/library/core.php";

use App\Template;

Template::header("Ticket");

$errors = [];

if (isset($_GET['id']))
    $id = intval($_GET['id']);

$getTicket = $dbh->prepare("
    SELECT t.*, q.rank, c.name as category_name, au.users, AVG(r.response_seconds) avg_response
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
    
    LEFT JOIN (
        SELECT GROUP_CONCAT(name) AS users, active_ticket
        FROM users
        WHERE date_seen > UNIX_TIMESTAMP(NOW()) - 60*15
        GROUP BY active_ticket
    ) au
    ON au.active_ticket = t.ticket_id
    
	LEFT JOIN (
		SELECT tm.ticket_id, (tm.date_created - t.date_created) AS response_seconds
		FROM
			tickets t
		JOIN
			ticket_messages tm
		ON
			t.ticket_id = tm.ticket_id
		WHERE
			t.ticket_id = :ticketId
		) AS r
	ON
	t.ticket_id = r.ticket_id
    
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

// if the user is logged in, set their active ticket
if (\App\Auth::isLoggedIn())
	$user->activeTicket = $ticket['ticket_id'];

if (isset($_GET['action'])) {
	if ($_GET['action'] === "close" && ($user->rank === "1" || (\App\Auth::isLoggedIn() && \App\Auth::getUserId() === $ticket['user_id']))) {
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
	else if ($_GET['action'] === "delete" && ($user->rank === "1" || (\App\Auth::isLoggedIn() && \App\Auth::getUserId() === $ticket['user_id']))) {
		$deleteTicket = $dbh->prepare("DELETE FROM tickets WHERE ticket_id = :ticketId");
		$deleteTicket->execute([":ticketId" => $ticket['ticket_id']]);
		header("Location: /tickets.php");
		exit;
	}
        else if ($_GET['action'] === "merge" && isset($_GET['mergeId']) && ($user->rank === "1" || (\App\Auth::isLoggedIn() && \App\Auth::getUserId() === $ticket['user_id']))) {
                // we should probably be checking the ticket if it exists
		$mergeMessages = $dbh->prepare("UPDATE ticket_messages SET ticket_id = :ticketId WHERE ticket_id = :oldId");
		$mergeMessages->execute([
			":ticketId" => intval($_GET['mergeId']),
			":oldId"    => $ticket['ticket_id'],
		]);

		$deleteTicket = $dbh->prepare("DELETE FROM tickets WHERE ticket_id = :ticketId");
                $deleteTicket->execute([":ticketId" => $ticket['ticket_id']]);

                header("Location: /ticket.php?id=" . intval($_GET['mergeId']));
                exit;
        }

}


if (isset($_POST['reply']) && $ticket['status'] == 0) {
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
    
    // TODO file upload validation here ...
    //var_dump($_FILES);
    
    if (empty($errors)) {
	    // create message
	    $createMessage = $dbh->prepare("
	        INSERT INTO ticket_messages
	        VALUES (null, :ticketId, :name, :userId, UNIX_TIMESTAMP(NOW()), :message)
	    ");
	    $createMessage->execute([
	        ":ticketId" => $ticket['ticket_id'],
	        ":name"     => $name,
	        ":message"  => $message,
	        ":userId"   => \App\Auth::getUserId(),
	    ]);
	    
	    $messageId = $dbh->lastInsertId();
	    
		// update the ticket
		$updateTicket = $dbh->prepare("
			UPDATE tickets SET date_updated = UNIX_TIMESTAMP(NOW())
			WHERE ticket_id = :ticketId
		");
		$updateTicket->execute([
			":ticketId" => $ticket['ticket_id'],
		]);
		
		header("Location: /ticket.php?id=" . $ticket['ticket_id'] . "#msg" . $messageId);
		exit;
    }
}

?>

<div class="row">
	<div class="col-md-8">
		<h1 style="margin:0px;padding:0px"><?=(empty($ticket['subject']) ? "<em>(Untitled Subject)</em>" : htmlentities($ticket['subject']))?></h1>
	</div>
	<div class="col-md-4">

<?php
if ($user->rank === "1" || (\App\Auth::isLoggedIn() && \App\Auth::getUserId() === $ticket['user_id'])) {
?> 
		<a href="?action=close&id=<?=$ticket['ticket_id']?>" class="btn btn-danger pull-right">Close Ticket</a>
                <a href="?action=delete&id=<?=$ticket['ticket_id']?>" class="btn btn-danger pull-right" onclick="return confirm('Are you sure you would like to delete the ticket?');">Delete Ticket</a>
<?php
}

if ($user->rank === "1") {
?> 
		<a href="?action=merge&id=<?=$ticket['ticket_id']?>" class="btn btn-warning pull-right merge">Merge Ticket</a>
<?php
}
?>
		<a href="/tickets.php?status=open" class="btn btn-primary pull-right">&laquo; Return</a>
	</div>
</div>

<div class="clear"></div>

<?php
if (!empty($ticket['users'])) {
	echo "<div class='alert alert-info'>
		This ticket is currently being reviewed.
	</div>
	<div class='clear'></div>";
	
}

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

<div class="panel panel-<?=($message['rank'] === "1" ? "danger" : "primary")?>" id="msg<?=$message['message_id']?>">
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
				<span class="label label-primary pull-right"><?=number_format($ticket['rank'])?></span>
			</div>
			
			<div class="col-md-3">
				<strong>Status:</strong>
				<?=getStatus(!empty($ticket['users']) ? 2 : $ticket['status'], " pull-right")?>
			</div>
			
			<div class="col-md-6">
				<strong>Average Response Time:</strong>
				<span class="label label-primary pull-right"><?=\App\Format::complexTimeElapsed($ticket['avg_response'])?></span>
			</div>
		</div>
	</div>
</div>

<?php
if ($ticket['status'] != 1) {
?>

<div class="clear"></div>

<?php
	if (!empty($errors)) {
		echo "<div class='alert alert-danger'>
			<strong>An error has occurred!</strong> " . $errors[0] .
		"</div>";
	}
?>

<div class="panel panel-info" id="reply">
	<div class="panel-heading">Reply to Ticket</div>
	<div class="panel-body">
		<form action="/ticket.php?id=<?=$ticket['ticket_id']?>#reply" method="POST" <?php /*class="dropzone" style="border:0px" id="dropzone" enctype="multipart/form-data"*/ ?>>
			<div class="form-group">
				<label for="name">Your Name:</label>
				<input type="text" name="name" class="form-control" id="name" value="<?=htmlentities(isset($_SESSION['name']) ? $_SESSION['name'] : (isset($_POST['name']) ? trim($_POST['name']) : ""))?>" />
			</div>
			
			<div class="form-group">
				<label for="message">Message:</label>
				<textarea name="message" class="form-control" id="message" style="height:200px"></textarea>
			</div>
			<!--
			<div class="dropzone-previews"></div>
			<div class="fallback">
				<input name="file" type="file" multiple />
			</div>
			-->
			<button type="submit" name="reply" class="btn btn-primary">Reply</button>
		</form>
	</div>
</div>

<?php

}


if (strcasecmp($ticket['author_name'], "MrH") === 0 || strcasecmp($ticket['author_name'], "Humpartzoomian") === 0) {

?>

<script type="text/javascript" src="/assets/js/meem.js"></script>


<?php
}

Template::footer();

