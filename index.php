<?php
include __DIR__ . "/library/core.php";

use App\Template;

Template::header("Blackhole");
?>

<div class="col-lg-12">

    <h1>Blackhole</h1>
    
    <a href="/tickets.php?status=open">View Opened Tickets</a> | 
    <a href="/tickets.php?status=all">View All Tickets</a> | 
    <a href="/tickets.php?status=closed">View Closed Tickets</a>

</div>

<?php
Template::footer();
