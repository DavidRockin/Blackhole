<?php
include __DIR__ . "/library/core.php";

if (isset($_GET['id']))
    $id = intval($_GET['id']);

$getTicket = $dbh->prepare("
    SELECT *, q.rank, c.name as category_name
    FROM tickets t
    LEFT JOIN (
        SELECT *
        FROM (
                SELECT *, @rank := @rank + 1 as rank
                FROM tickets, (SELECT @rank := 0) r
                WHERE status = 0
                ORDER BY date_created
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

$ticket = $getTicket->fetch();


if (isset($_POST['reply'])) {
    // TODO: add validation
    
    // create message
    $createMessage = $dbh->prepare("
        INSERT INTO ticket_messages
        VALUES (null, :ticketId, :name, UNIX_TIMESTAMP(NOW()), :message)
    ");
    $createMessage->execute([
        ":ticketId" => $ticket['ticket_id'],
        ":name"     => trim($_POST['name']),
        ":message"  => trim($_POST['message']),
    ]);
    
}

?>

<h1>Blackhole <small><?=htmlentities($ticket['subject'])?></small></h1>

<a href="/tickets.php?status=open">Return</a>

<br />

<strong>Ticket in the queue: <?=$ticket['rank']?></strong>

<br />

<strong>Category: <?=$ticket['category_name']?></strong>

<table style="width:100%;" border="1">
<?php
$getMessages = $dbh->prepare("
    SELECT *
    FROM ticket_messages
    WHERE ticket_id = :ticketId
    ORDER BY ABS(date_created);
");
$getMessages->execute([
    ":ticketId" => $ticket['ticket_id'],
]);

while ($message = $getMessages->fetch()) {
    echo "<tr>
        <td>
            " . htmlentities($message['author_name']) . "<br />"
            . date("r", $message['date_created']) . "
        </td>
        <td>
            " . htmlentities($message['message']) . "
        </td>
    </tr>";
}
?>

</table>



<form action="" method="POST">
    
    <label>Your Name:
        <input type="text" name="name" />
    </label>
    
    <Br />
    <label>Message:
        <textarea name="message"></textarea>
    </label>
    
    <Br />
    <button type="submit" name="reply">Reply</button>
    
</form>
