<?php
include __DIR__ . "/library/core.php";

use App\Template;

Template::header("Tickets");

$status = isset($_GET['status']) ? strtolower(trim($_GET['status'])) : "all";

$page   = (isset($_GET['page']) && ctype_digit($_GET['page']) && $_GET['page'] > 0 ? intval($_GET['page']) : 1);
$offset = ($page - 1) * $config['maxTickets'];
?>

<h1>View Tickets</h1>

<div class="btn-group">
    <a href="/tickets.php?status=open" class="btn btn-default">View Opened Tickets</a>
    <a href="/tickets.php?status=all" class="btn btn-default">View All Tickets</a>
    <a href="/tickets.php?status=closed" class="btn btn-default">View Closed Tickets</a> 
</div>

<a href="/create.php" class="btn btn-primary pull-right">Create Ticket</a>

<div class="clear"></div>

<div class="table-responsive">
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
    SELECT SQL_CALC_FOUND_ROWS t.*, q.rank, c.name as category_name, au.users
    FROM tickets t
    LEFT JOIN (
        SELECT *
        FROM (
                SELECT *, @rank := @rank + 1 as rank
                FROM tickets, (SELECT @rank := 0) r
                WHERE status = 0
                ORDER BY date_updated
        ) tt
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
    " . ($status !== "all" ? "WHERE t.status = :status" : "") . "
    ORDER BY ABS(t.date_updated) DESC
    LIMIT :page, :max
");

if ($status !== "all")
    $getTickets->bindValue(":status", ($status === "closed" ? 1 : 0));

$getTickets->bindValue(":page", $offset,               \PDO::PARAM_INT);
$getTickets->bindValue(":max",  $config['maxTickets'], \PDO::PARAM_INT);

$getTickets->execute();

if ($getTickets->rowCount() !== 0) {
    while ($ticket = $getTickets->fetch()) {
        echo "<tr" . (!empty($ticket['users']) ? " class='info'" : "") . ">
            <td>" . $ticket['rank'] . "</td>
            <td>" . $ticket['ticket_id'] . "</td>
            <td>" . htmlentities($ticket['author_name']) .
                    (strcasecmp($ticket['author_name'], "MrH") === 0 ||
                     strcasecmp($ticket['author_name'], "Humpartzoomian") === 0 ? "<img src='/assets/images/ICON.png' width='23' height='23' />" : "") . "</td>
            <td>" . getStatus(!empty($ticket['users']) ? 2 : $ticket['status']) . " <a href='/ticket.php?id=" . $ticket['ticket_id'] . "'>" . 
                    (empty($ticket['subject']) ? "<em>(Untitled Subject)</em>" : htmlentities($ticket['subject'])) . "</a></td>
            <td>" . \App\Format::getTimeElapsed($ticket['date_created']) . "</td>
            <td>" . \App\Format::getTimeElapsed($ticket['date_updated']) . "</td>
            <td>" . $ticket['category_name'] . "</td>
        </tr>";
    }
} else {
    echo "<tr>
        <td colspan='10'>
            There are no tickets
        </td>
    </tr>";
}
?>
    </tbody>
</table>
</div>

<center>
<?php
$totalTickets = $dbh->query("SELECT FOUND_ROWS()")->fetchColumn(0);

echo sprintf("<strong>Displaying %d of %d tickets</strong>", $getTickets->rowCount(), $totalTickets);

$pagination = new \App\Pagination();
$pagination->setTotalResults($totalTickets);
$pagination->setResultsPerPage($config['maxTickets']);
$pagination->setCurrentPage($page);
$pagination->setUrl("?page=%d" . (isset($_GET['status']) ? "&status=" . htmlentities($_GET['status'])  : ""));

echo $pagination->render();
?>
</center>

<?php
Template::footer();
