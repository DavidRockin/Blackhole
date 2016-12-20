<?php

function getStatus($status) {
    switch ($status) {
        case 0: return "<span class='label label-success'>Open</span>";
        case 1: return "<span class='label label-danger'>Closed</span>";
    }
}

function getCategories() {
    global $dbh;
    $getCategories = $dbh->query("SELECT * FROM categories");
    return $getCategories->fetchAll();
}
