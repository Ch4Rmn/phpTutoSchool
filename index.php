<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testing PHP</title>
    <style>
        table {
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        td,
        th {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        tr:nth-child(even) {
            background-color: #dddddd;
        }

        #th {
            background-color: #dddddd;
        }
    </style>
</head>

<body>
    <?php
    require_once('config/db.php');
    require_once('config/configuration.php');

    $name = $age = $townships = $gender = '';
    $hobby = [];
    $error = false;
    $errorMessage = '';
    $success = false;
    $successMessage = '';
    $delete_link = 'delete.php';
    $edit_link = 'edit.php';

    $townshipsSql = 'SELECT * FROM `townships`';
    $townships_res = $mysqli->query($townshipsSql);

    $hobbiesSql = 'SELECT * FROM `hobbies`';
    $hobbies_res = $mysqli->query($hobbiesSql);

    if ($error == false) {
        $userDatas = "SELECT myDatabase.*, townships.name AS township_name, GROUP_CONCAT(DISTINCT hobbies.name) AS hobbies
            FROM `myDatabase` 
            LEFT JOIN `townships` ON myDatabase.townships = townships.id
            LEFT JOIN `user_hobbies` ON myDatabase.id = user_hobbies.user_id
            LEFT JOIN `hobbies` ON user_hobbies.hobby_id = hobbies.id
            GROUP BY myDatabase.id";

        $userData_res = $mysqli->query($userDatas);
    }

    if (isset($_POST['form-sub']) && $_POST['form-sub'] == 1) {
        $hobby = (isset($_POST['hobby'])) ? $_POST['hobby'] : [];
        $name = $mysqli->real_escape_string($_POST['name']);
        $age = $mysqli->real_escape_string($_POST['age']);
        $townships = $mysqli->real_escape_string($_POST['townships']);
        $gender = (isset($_POST['gender'])) ? $mysqli->real_escape_string($_POST['gender']) : '';

        if ($name == '') {
            $error = true;
            $errorMessage .= 'need to fill name<br>';
        }

        if ($townships == '') {
            $error = true;
            $errorMessage .= 'need to fill townships<br>';
        }

        if ($gender == '') {
            $error = true;
            $errorMessage .= 'need to fill gender<br>';
        }

        if (!is_numeric($age) || $age == '') {
            $error = true;
            $errorMessage .= 'need to fill age<br>';
        }

        if ($error == false) {
            $getUser = "SELECT id FROM `myDatabase` WHERE name='$name' AND age='$age'";
            $query = $mysqli->query($getUser);
            if ($query->num_rows > 0) {
                $error = true;
                $errorMessage .= 'User already exists with this name and age.';
            } else {
                if (isset($_FILES['file'])) {
                    $imageFolder = 'image/';

                    if (!is_dir($imageFolder) || !file_exists($imageFolder)) {
                        mkdir($imageFolder, 0777, true);
                    }

                    if (
                        $_FILES["file"]["size"] > 50000 || $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                        && $imageFileType != "gif"
                    ) {
                        echo "Sorry,can't create";


                        $image_name = $imageFolder . basename($_FILES['file']['name']);
                        if (move_uploaded_file($_FILES['file']['tmp_name'], $image_name)) {
                            echo "Image uploaded successfully.";
                        }
                    }
                }


                $sql = "INSERT INTO `myDatabase` (name, age, townships, gender,image) VALUES ('$name', '$age', $townships, '$gender','$image_name')";
                $mysqli->query($sql);
                $user_id = $mysqli->insert_id;

                foreach ($hobby as $hobby_id) {
                    $userHobbies = "INSERT INTO `user_hobbies` (user_id, hobby_id) VALUES ('$user_id', '$hobby_id')";
                    $mysqli->query($userHobbies);
                }
            }
            $name = $age = $townships = $gender = '';
            $success = true;
            $successMessage = "<p style='color: green;'>Insert complete!</p><br>";
        }
    }

    ?>

    <?php if ($error == true); { ?>
        <p style='color: red;'><?php echo $errorMessage; ?></p>
    <?php } ?>

    <?php if ($success == true); { ?>
        <p style='color: green;'><?php echo $successMessage; ?></p>
    <?php } ?>

    <div>
        <form action="index.php" method="post" enctype="multipart/form-data">
            <label for="">Name</label>
            <input type="text" name="name" id="" value="<?php echo $name ?>"> <br />

            <label for="">Age</label>
            <input type="text" name="age" id="" value="<?php echo $age ?>"> <br />

            <select name="townships" class="form-control">
                <option value="">Choose Your Township</option>
                <?php while ($town = $townships_res->fetch_assoc()) {
                ?>
                    <option value="<?php echo $town['id']; ?>"><?php echo $town['name'] ?></option>
                <?php } ?>
            </select>
            <hr>

            <label for="gender">Gender</label><br>
            <input type="radio" id="male" name="gender" value="1" <?php if ($gender == '1') echo 'checked'; ?>>
            <label for="male">Male</label><br>

            <input type="radio" id="female" name="gender" value="2" <?php if ($gender == '2') echo 'checked'; ?>>
            <label for="female">Female</label><br>
            <hr>

            <?php while ($hobby_row = $hobbies_res->fetch_assoc()) { ?>
                <input type="checkbox" name="hobby[]" id="hobby" value="<?php echo $hobby_row['id']; ?>" <?php if (in_array($hobby_row['id'], $hobby)) echo "checked"; ?>>
                <label for="hobby"><?php echo $hobby_row['name'] ?></label><br>
            <?php } ?>

            Image
            <input type="file" name="file" value="">

            <input type="hidden" name="form-sub" value="1">
            <input type="submit" name="Submit" value="Submit">
        </form>
        <hr>

        <div>
            <h2>Table</h2>
            <table>
                <div>
                    <tr id="th">
                        <th>id</th>
                        <th>name</th>
                        <th>gender</th>
                        <th>age</th>
                        <th>townships</th>
                        <th>Hobbies</th>
                        <th>Action</th>
                    </tr>
                </div>



                <tbody>
                    <?php
                    while ($user = $userData_res->fetch_assoc()) {
                        $userID =  $user['id'];
                    ?>
                        <tr>
                            <td><?php echo (int) $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                            <td><?php if ($user['gender'] == 1) {
                                    echo "Male";
                                } else {
                                    echo "Female";
                                }
                                ?></td>
                            <td><?php echo htmlspecialchars($user['age']); ?></td>
                            <td><?php echo htmlspecialchars($user['township_name']); ?></td>
                            <td><?php echo htmlspecialchars($user['hobbies']); ?></td>


                            <td>
                                <a style="margin-right: 20px;" href='<?php echo $baseUrl . $delete_link . "?id=" . $userID; ?>'>Delete</a>

                                <a style="margin-right: 20px;" href='<?php echo $baseUrl . $edit_link . "?id=" . $userID; ?>'>Edit</a>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>

</html>