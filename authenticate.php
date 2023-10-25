<?php
session_start();
// Change this to your connection info.
$POSTGRES_CONNECTION_STRING = "host=ep-muddy-art-24176362.ap-southeast-1.postgres.vercel-storage.com port=5432 dbname=verceldb user=default password=FoQMG0CR6IWE";

// Attempt to connect to PostgreSQL using the connection string
$con = pg_connect($POSTGRES_CONNECTION_STRING);

if (!$con) {
    exit('Failed to connect to PostgreSQL: ' . pg_last_error());
}

// Now we check if the data from the login form was submitted, isset() will check if the data exists.
if (!isset($_POST['username'], $_POST['password'])) {
    // Could not get the data that should have been sent.
    exit('Please fill both the username and password fields!');
}

// Prepare our SQL, preparing the SQL statement will prevent SQL injection.
$query = "SELECT \"id\", \"password\" FROM \"accounts\" WHERE \"username\" = $1";

$result = pg_prepare($con, 'select_user', $query);

if ($result) {
    $result = pg_execute($con, 'select_user', array($_POST['username']));

    if ($result) {
        $row = pg_fetch_assoc($result);

        if ($row) {
            $id = $row['id'];
            $password = $row['password'];

            // Account exists, now we verify the password.
            // Note: remember to use password_hash in your registration file to store the hashed passwords.
            if (password_verify($_POST['password'], $password)) {
                // Verification success! User has logged-in!
                // Retrieve the login history JSON from the database for the logged-in user
                $loginHistory = ''; // Initialize the variable

                $query = "SELECT \"loginHistory\" FROM \"accounts\" WHERE \"id\" = $1";

                $result = pg_prepare($con, 'select_login_history', $query);

                if ($result) {
                    $result = pg_execute($con, 'select_login_history', array($id));

                    if ($result) {
                        $row = pg_fetch_assoc($result);

                        if ($row) {
                            $loginHistory = $row['loginHistory'];

                            // Retrieve existing login history JSON
                            $loginHistoryJson = json_decode($loginHistory, true);

                            // Add the current login time to the history
                            $loginHistoryJson[] = date('Y-m-d H:i:s');

                            // Convert back to JSON and update the column
                            $updatedLoginHistory = json_encode($loginHistoryJson);

                            // Update the loginHistory column in the accounts table
                            $query = "UPDATE \"accounts\" SET \"loginHistory\" = $1 WHERE \"id\" = $2";

                            $result = pg_prepare($con, 'update_login_history', $query);

                            if ($result) {
                                $result = pg_execute($con, 'update_login_history', array($updatedLoginHistory, $id));

                                if ($result) {
                                    sleep(3);
                                    // Continue with the login process
                                    // Create sessions, so we know the user is logged in; they basically act like cookies but remember the data on the server.
                                    session_regenerate_id();
                                    $_SESSION['loggedin'] = TRUE;
                                    $_SESSION['name'] = $_POST['username'];
                                    $_SESSION['id'] = $id;
                                    header('Location: dashboard.php');
                                } else {
                                    echo 'Login history update failed.';
                                }
                            } else {
                                echo 'Could not prepare statement for login history update.';
                            }
                        } else {
                            echo 'Login history not found.';
                        }
                    } else {
                        echo 'Could not execute query for login history: ' . pg_last_error();
                    }
                } else {
                    echo 'Could not prepare statement for login history: ' . pg_last_error();
                }
            } else {
                // Incorrect password
                echo 'Login failed. Please check your credentials.';
            }
        } else {
            // Incorrect username
            echo 'Login failed. Please check your credentials.';
        }
    } else {
        echo 'Could not execute query: ' . pg_last_error();
    }
} else {
    echo 'Could not prepare statement: ' . pg_last_error();
}

// Close the PostgreSQL connection when you're done
pg_close($con);
?>
