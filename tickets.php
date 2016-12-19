<?php
include __DIR__ . "/library/core.php";

use App\Template;

Template::header("Tickets");

$status = isset($_GET['status']) ? strtolower(trim($_GET['status'])) : "all";
?>

<h1>Blackhole</h1>

<a href="/tickets.php?status=open">View Opened Tickets</a> | 
<a href="/tickets.php?status=all">View All Tickets</a> | 
<a href="/tickets.php?status=closed">View Closed Tickets</a> 

<br />

[ <a href="/create.php">Create Ticket</a> ]

<table style="width:100%;" border="1">
    <thead>
        <tr>
            <th>Queue #</th>
            <th>Ticket ID</th>
            <th>Author</th>
            <th>Subject</th>
            <th>Date Created</th>
            <th>Date Updated</th>
            <th>Category</th>
        </tr>
    </thead>
    <tbody>
<?php
$getTickets = $dbh->prepare("
    SELECT t.*, q.rank
    FROM tickets t
    LEFT JOIN (
        SELECT *
        FROM (
                SELECT *, @rank := @rank + 1 as rank
                FROM tickets, (SELECT @rank := 0) r
                WHERE status = 0
                ORDER BY date_created
        ) tt
    ) q
    ON q.ticket_id = t.ticket_id
    " . ($status !== "all" ? "WHERE t.status = :status" : "") . "
    ORDER BY ABS(t.date_updated) DESC
");

if ($status !== "all")
    $getTickets->bindValue(":status", ($status === "closed" ? 1 : 0));

$getTickets->execute();

while ($ticket = $getTickets->fetch()) {
    echo "<tr>
        <td>" . $ticket['rank'] . "</td>
        <td>" . $ticket['ticket_id'] . "</td>
        <td>" . htmlentities($ticket['author_name']) . "</td>
        <td><a href='/ticket.php?id=" . $ticket['ticket_id'] . "'>" . htmlentities($ticket['subject']) . "</a> (" . getStatus($ticket['status']) . ")</td>
        <td>" . date("r", $ticket['date_created']) . "</td>
        <td>" . date("r", $ticket['date_updated']) . "</td>
        <td>TBD</td>
    </tr>";
}

?>
    </tbody>
</table>


<?php
Template::footer();
