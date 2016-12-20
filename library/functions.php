<?php

function getStatus($status, $customCss = null) {
    switch ($status) {
        case 0: return "<span class='label label-success" . $customCss . "'>Open</span>";
        case 1: return "<span class='label label-danger" . $customCss . "'>Closed</span>";
    }
}

function getCategories() {
    global $dbh;
    $getCategories = $dbh->query("SELECT * FROM categories");
    return $getCategories->fetchAll();
}
