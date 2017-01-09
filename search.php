<?php
include __DIR__ . "/library/core.php";

use App\Template;

Template::header("Search");

$query = trim($_GET['q']);
?>


<h1>Search for tickets</h1>

<div class="clear"></div>

<form action="" method="GET">
	<div class="form-group">
		<label for="query">Search Query:</label>
		<input type="text" name="q" class="form-control" id="query" value="<?=isset($_GET['q']) ? htmlentities($query) : ""?>" />
	</div>
	
	<div class="form-group">
		<label for="status">Status:</label>
		<select name="status" id="status" class="form-control">
			<option value="-1">All</option>
			<option value="0"<?=(isset($_GET['status']) && $_GET['status'] == 0 ? " selected" : "")?>>Open</option>
			<option value="1"<?=(isset($_GET['status']) && $_GET['status'] == 1 ? " selected" : "")?>>Closed</option>
		</select>
	</div>
	
	<div class="form-group">
		<label for="category">Category:</label>
		<select name="category" id="category" class="form-control">
			<option value=""></option>
<?php
$categories = getCategories();
foreach ($categories as $category)
	echo "<option value='" . $category['category_id'] . "'" . (isset($_GET['category']) && $_GET['category'] == $category['category_id'] ? " selected" : "") . ">" . $category['name'] . "</option>" . PHP_EOL;
?>
		</select>
	</div>
	
	<button type="submit" class="btn btn-primary">Search</button>
	
</form>

<?php
if (isset($_GET['q']) && !empty($_GET['q'])) {
	$criteria = [];
	
	if (isset($_GET['status']) && $_GET['status'] > -1)
		$criteria['t.status = ?'] = intval($_GET['status']);
	
	if (isset($_GET['category']) && !empty($_GET['category']))
		$criteria['t.category_id = ?'] = intval($_GET['category']);
?>

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
    SELECT t.*, q.rank, c.name as category_name
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
    WHERE (t.author_name LIKE ? OR t.subject LIKE ?)
    " . (!empty($criteria) ? " AND " . implode(array_keys($criteria), " AND ") : "") . "
    ORDER BY ABS(t.date_updated) DESC
");

$getTickets->bindValue(1, "%" . $query . "%");
$getTickets->bindValue(2, "%" . $query . "%");

$i = 3;
foreach ($criteria as $k => $v) {
	$getTickets->bindValue($i, $v);
	++$i;
}

$getTickets->execute();

while ($ticket = $getTickets->fetch()) {
    echo "<tr>
        <td>" . $ticket['rank'] . "</td>
        <td>" . $ticket['ticket_id'] . "</td>
        <td>" . htmlentities($ticket['author_name']) . "</td>
        <td>" . getStatus($ticket['status']) . " <a href='/ticket.php?id=" . $ticket['ticket_id'] . "'>" . 
    		(empty($ticket['subject']) ? "<em>(Untitled Subject)</em>" : htmlentities($ticket['subject'])). "</a></td>
        <td>" . \App\Format::getTimeElapsed($ticket['date_created']) . "</td>
        <td>" . \App\Format::getTimeElapsed($ticket['date_updated']) . "</td>
        <td>" . $ticket['category_name'] . "</td>
    </tr>";
}

?>
    </tbody>
</table>
</div>

<?php
}

Template::footer();
