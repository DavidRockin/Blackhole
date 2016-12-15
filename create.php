<?php
include __DIR__ . "/library/core.php";

if (isset($_POST['create'])) {
    // TODO: add validation
    
    // insert ticket info
    $createTicket = $dbh->prepare("
        INSERT INTO tickets
        VALUES (NULL, :subject, :name, UNIX_TIMESTAMP(NOW()), UNIX_TIMESTAMP(NOW()), 0)
    ");
    $createTicket->execute([
        ":subject" => trim($_POST['subject']),
        ":name" => trim($_POST['name']),
    ]);
    
    // ticket id
    $ticketId = $dbh->lastInsertId();
    
    // create message
    $createMessage = $dbh->prepare("
        INSERT INTO ticket_messages
        VALUES (null, :ticketId, :name, UNIX_TIMESTAMP(NOW()), :message)
    ");
    $createMessage->execute([
        ":ticketId" => $ticketId,
        ":name"     => trim($_POST['name']),
        ":message"  => trim($_POST['message']),
    ]);
    
    
    // redirect to ticket page
    header("Location: /ticket.php?id=" . $ticketId);
    exit;
    
}
?>

<h1>Blackhole <small>Create Ticket</small></h1>

<a href="/tickets.php?status=open">View Opened Tickets</a> | 
<a href="/tickets.php?status=all">View All Tickets</a> | 
<a href="/tickets.php?status=closed">View Closed Tickets</a>

<form action="" method="POST">
    
    <label>Your Name:
        <input type="text" name="name" />
    </label>
    
    <Br />
    
    <label>Subject:
        <input type="text" name="subject" />
    </label>
    
    <Br />
    <label>Category:
        TBD
    </label>
    
    <Br />
    <label>Message:
        <textarea name="message"></textarea>
    </label>
    
    <Br />
    <button type="submit" name="create">Create</button>
    
</form>