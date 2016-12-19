<?php
include __DIR__ . "/library/core.php";

use App\Template;

Template::header("Tickets");

$status = isset($_GET['status']) ? strtolower(trim($_GET['status'])) : "all";
?>

<h1>View Tickets</h1>

<div class="btn-group">
    <a href="/tickets.php?status=open" class="btn btn-default">View Opened Tickets</a>
    <a href="/tickets.php?status=all" class="btn btn-default">View All Tickets</a>
    <a href="/tickets.php?status=closed" class="btn btn-default">View Closed Tickets</a> 
</div>

<a href="/create.php" class="btn btn-primary pull-right">Create Ticket</a>

<div class="clear"></div>

<table class="table table-bordered table-hover">
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
        <td>" . getStatus($ticket['status']) . " <a href='/ticket.php?id=" . $ticket['ticket_id'] . "'>" . htmlentities($ticket['subject']) . "</a></td>
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
