<?php
include __DIR__ . "/library/core.php";

if (isset($_GET['id']))
    $id = intval($_GET['id']);

$getTicket = $dbh->prepare("
    SELECT *
    FROM tickets
    WHERE ticket_id = :ticketId
");
$getTicket->execute([
    ":ticketId" => $id,
]);

if ($getTicket->rowCount() === 0) {
    echo "<h1>No ticket found</h1>";
    exit;
}

$ticket = $getTicket->fetch();

?>

<h1>Blackhole <small><?=htmlentities($ticket['subject'])?></small></h1>

<a href="/tickets.php?status=open">Return</a>

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

