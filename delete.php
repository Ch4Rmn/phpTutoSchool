<?php
require_once('config/db.php');
require('config/configuration.php');

$id = ($_GET['id']);
$delete_sql = "DELETE FROM `myDatabase` WHERE id = $id";
$delete_query = $mysqli->query($delete_sql);
$url = 'index.php/';
if ($delete_query) {
    header("Refresh:0;url=$url");
    exit();
}
