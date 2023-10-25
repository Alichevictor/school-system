<?php
// Define your PostgreSQL connection parameters
$host = 'localhost'; // Hostname or IP address of your PostgreSQL server
$port = 5432; // Default PostgreSQL port
$dbname = 'royal hub academy'; // Your PostgreSQL database name
$user = 'postgres'; // Your PostgreSQL username
$password = ''; // Your PostgreSQL password

// Construct the connection string with SSL mode set to "require"
$connectionString = "host=$host port=$port dbname=$dbname user=$user password=$password sslmode=require";

// Attempt to connect to PostgreSQL using the connection string
$con = pg_connect($connectionString) or die('Failed to connect to PostgreSQL: ' . pg_last_error());

$requiredFields = [
    'firstName',
    'lastName',
    'otherName',
    'email',
    'phoneNumber',
    'dateOfBirth',
    'residentialAddress',
    'statesecurityNumber',
    'nextofkinName',
    'username',
    'password'
];

foreach ($requiredFields as $field) {
    if (!isset($_POST[$field]) || empty($_POST[$field])) {
        die('Please complete the registration form');
    }
}

// Validate image fields (required)
if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    die('Please upload a valid image');
}

// Validate image type and size
$allowedImageTypes = ['image/jpeg', 'image/png', 'image/gif'];
$maxImageSize = 5 * 1024 * 1024; // 5 MB

if (
    !in_array($_FILES['image']['type'], $allowedImageTypes) ||
    $_FILES['image']['size'] > $maxImageSize
) {
    die('Please upload a valid image (JPEG, PNG, GIF) within 5 MB.');
}

// Move the uploaded image to a designated upload directory
$uploadDir = 'uploads/';
$uploadedFilePath = $uploadDir . $_FILES['image']['name'];

if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadedFilePath)) {
    die('Failed to move uploaded image to the directory.');
}

$imagePath = $uploadedFilePath;

if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    die('Email is not valid!');
}
if (preg_match('/^[a-zA-Z0-9]+$/', $_POST['username']) == 0) {
    die('Username is not valid!');
}
if (strlen($_POST['password']) > 20 || strlen($_POST['password']) < 5) {
    die('Password must be between 5 and 20 characters long!');
}

// Prepare a statement to check if the account with that username exists
if ($stmt = pg_prepare($con, 'check_username', 'SELECT id FROM accounts WHERE username = $1')) {
    pg_send_execute($stmt, array($_POST['username']));
    $result = pg_get_result($con);

    if (pg_num_rows($result) > 0) {
        die('Username exists, please choose another!');
    } else {
        // Username doesn't exist, insert a new account
        if ($stmt = pg_prepare(
            $con,
            'insert_user',
            'INSERT INTO accounts (
                firstName,
                lastName,
                otherName,
                email,
                phoneNumber,
                dateOfBirth,
                residentialAddress,
                statesecurityNumber,
                nextofkinName,
                username,
                password,
                imagePath,
                balance
            ) VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12, $13)'
        )) {
            // Hash the password and use password_hash when storing passwords.
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $balance = 0.00; // Initial balance

            $params = array(
                $_POST['firstName'],
                $_POST['lastName'],
                $_POST['otherName'],
                $_POST['email'],
                $_POST['phoneNumber'],
                $_POST['dateOfBirth'],
                $_POST['residentialAddress'],
                $_POST['statesecurityNumber'],
                $_POST['nextofkinName'],
                $_POST['username'],
                $password,
                $imagePath,
            );

            pg_send_execute($stmt, $params);
            $result = pg_get_result($con);

            if (pg_affected_rows($result) > 0) {
                echo 'You have successfully registered! You can now <a href="sign-in.html">login</a>.';
                header('Location: sign-in.html');
                exit;
            } else {
                die('Registration failed. Please try again.');
            }
        } else {
            die('Could not prepare the statement!');
        }
    }
    pg_free_result($result);
} else {
    die('Could not prepare the statement!');
}

pg_close($con);
?>
