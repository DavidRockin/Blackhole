<?php

// predefine some consts for the application
define("LIBDIR", dirname(__FILE__) . "/");

// load the boostrap
include LIBDIR . "bootstrap.php";
include LIBDIR . "functions.php";

// load config
$config = include LIBDIR . "config/app.php";

// initialize database connection
$dbh    = new \App\Database($config);

session_start();

// initialize the user object
$user   = new \App\User($dbh, \App\Auth::getUserId());
