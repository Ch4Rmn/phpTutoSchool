<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit</title>
</head>

<body>
    <?php
    require_once('config/db.php');
    require_once('config/configuration.php');

    $error = false;
    $errorMessage = '';
    $hobby = [];

    $townshipsSql = 'SELECT * FROM `townships`';
    $townships_res = $mysqli->query($townshipsSql);

    $hobbiesSql = 'SELECT * FROM `hobbies`';
    $hobbies_res = $mysqli->query($hobbiesSql);

    $id = (int) $_GET['id'];
    $id = $mysqli->real_escape_string($id);
    $user_sql = "SELECT * FROM myDatabase WHERE id='$id'";
    $user_query = $mysqli->query($user_sql);
    $user_num_row = $user_query->num_rows;

    if ($user_num_row > 0) {
        $user_row = $user_query->fetch_assoc();
        $name = htmlspecialchars($user_row['name']);
        $age = htmlspecialchars($user_row['age']);
        $gender = htmlspecialchars($user_row['gender']);
        $townships = htmlspecialchars($user_row['townships']);

        // Fetch user's hobbies
        $user_hobbies_sql = "SELECT hobby_id FROM `user_hobbies` WHERE user_id='$id'";
        $user_hobbies_query = $mysqli->query($user_hobbies_sql);

        while ($hobby_row = $user_hobbies_query->fetch_assoc()) {
            $hobby[] = $hobby_row['hobby_id'];
        }
    } else {
        $error = true;
        $errorMessage = 'User not found.';
    }
    ?>

    <?php if ($error == true) : ?>
        <p style='color: red;'><?php echo $errorMessage; ?></p>
    <?php endif; ?>

    <div>
        <h1>Edit</h1>
        <form action="<?php echo $baseUrl ?>update.php" method="POST">

            <input type="hidden" name="id" value="<?php echo $id; ?>">


            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <label for="">Name</label>
            <input type="text" name="name" id="" value="<?php echo $name ?>"> <br />

            <label for="">Age</label>
            <input type="text" name="age" id="" value="<?php echo $age ?>"> <br />

            <select name="townships" class="form-control">
                <option value="">Choose Your Township</option>
                <?php while ($town = $townships_res->fetch_assoc()) : ?>
                    <option value="<?php echo $town['id']; ?>" <?php if ($townships == $town['id']) echo "selected"; ?>><?php echo $town['name'] ?></option>
                <?php endwhile; ?>
            </select>
            <hr>

            <label for="gender">Gender</label><br>
            <input type="radio" id="male" name="gender" value="1" <?php if ($gender == '1') echo 'checked'; ?>>
            <label for="male">Male</label><br>

            <input type="radio" id="female" name="gender" value="2" <?php if ($gender == '2') echo 'checked'; ?>>
            <label for="female">Female</label><br>
            <hr>

            <label for="">Hobbies</label><br>
            <?php while ($hobby_row = $hobbies_res->fetch_assoc()) : ?>
                <input type="checkbox" name="hobby[]" id="hobby<?php echo $hobby_row['id']; ?>" value="<?php echo $hobby_row['id']; ?>" <?php if (in_array($hobby_row['id'], $hobby)) echo "checked"; ?>>
                <label for="hobby<?php echo $hobby_row['id']; ?>"><?php echo $hobby_row['name'] ?></label><br>
            <?php endwhile; ?>

            <!-- <input type="hidden" name="form-sub" value="1"> -->
            <input type="submit" name="Submit" value="Update">
        </form>
        <hr>
    </div>
</body>

</html>