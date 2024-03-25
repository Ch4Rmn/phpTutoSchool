<?php
require_once('config/db.php');
require('config/configuration.php');

if (isset($_POST['Submit'])) {

    $id = $mysqli->real_escape_string($_POST['id']);
    $name = $mysqli->real_escape_string($_POST['name']);
    $age = $mysqli->real_escape_string($_POST['age']);
    $townships = $mysqli->real_escape_string($_POST['townships']);
    $gender = $mysqli->real_escape_string($_POST['gender']);
    $hobbies = isset($_POST['hobby']) ? $_POST['hobby'] : [];

    $update_sql = "UPDATE `myDatabase` SET name='$name', age='$age', townships='$townships', gender='$gender' WHERE id=$id";
    $update_query = $mysqli->query($update_sql);

    if ($update_query) {
        // Delete existing hobbies for the user
        $delete_hobbies_sql = "DELETE FROM `user_hobbies` WHERE user_id=$id";
        $mysqli->query($delete_hobbies_sql);

        // Insert new hobbies for the user
        foreach ($hobbies as $hobby_id) {
            $insert_hobby_sql = "INSERT INTO `user_hobbies` (user_id, hobby_id) VALUES ('$id', '$hobby_id')";
            $mysqli->query($insert_hobby_sql);
        }

        // Redirect to index.php after successful update
        header("Location: index.php");
        exit();
    } else {
        echo "Error updating record: " . $mysqli->error;
    }
}
