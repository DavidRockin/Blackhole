<?php
include __DIR__ . "/library/core.php";

// kill the session
session_destroy();

header("Location: /");
exit;
