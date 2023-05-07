<?php
const HOSTNAME = "localhost";
const MYSQLI_USERNAME = "root";
const MYSQLI_PASSWORD = "";
const database_name = "rahmahd_db";

$database_connection = new mysqli(
    HOSTNAME,
    MYSQLI_USERNAME,
    MYSQLI_PASSWORD,
    database_name
);

if ($database_connection->connect_error)
    exit();
?>