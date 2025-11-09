<?php
    $host = "localhost";
    $user = "";    // your MySQL username
    $pass = "";        // your MySQL password
    $db = "greenville_db";     // database name

    $connection = new mysqli($host, $user, $pass, $db);
    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }
?>