<?php
$servername = "localhost";
$username = "root";
$password = "linhtutkyaw";
$dbname = "myDB";

$mysqli = new mysqli($servername, $username, $password, $dbname);
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
    exit();
}

?>
