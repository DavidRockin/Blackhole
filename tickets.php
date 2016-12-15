<?php
include __DIR__ . "/library/core.php";

$status = isset($_GET['status']) ? strtolower(trim($_GET['status'])) : "all";
?>

<h1>Blackhole</h1>

<a href="/tickets.php?status=open">View Opened Tickets</a> | 
<a href="/tickets.php?status=all">View All Tickets</a> | 
<a href="/tickets.php?status=closed">View Closed Tickets</a>

<table style="width:100%;" border="1">
    <thead>
        <tr>
            <th>Ticket ID</th>
            <th>Author</th>
            <th>Subject</th>
            <th>Date Updated</th>
            <th>Category</th>
        </tr>
    </thead>
    <tbody>
<?php
$getTickets = $dbh->prepare("
    SELECT t.*
    FROM tickets t
    " . ($status !== "all" ? "WHERE t.status = :status" : "") . "
    ORDER BY ABS(date_updated) DESC
");

if ($status !== "all")
    $getTickets->bindValue(":status", ($status === "closed" ? 1 : 0));

$getTickets->execute();

while ($ticket = $getTickets->fetch()) {
    echo "<tr>
        <td>" . $ticket['ticket_id'] . "</td>
        <td>" . htmlentities($ticket['author_name']) . "</td>
        <td><a href='/ticket.php?id=" . $ticket['ticket_id'] . "'>" . htmlentities($ticket['subject']) . "</a> (" . getStatus($ticket['status']) . ")</td>
        <td>" . date("r", $ticket['date_updated']) . "</td>
        <td>TBD</td>
    </tr>";
}

?>
    </tbody>
</table>