<?php
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
	header('Location: sign-in.html');
	exit;
}
$POSTGRES_CONNECTION_STRING = "host=your-hostname dbname=your-dbname user=your-username password=your-password port=5432";

// Attempt to connect to PostgreSQL using the connection string
$con = pg_connect($POSTGRES_CONNECTION_STRING);

if (!$con) {
    exit('Failed to connect to PostgreSQL: ' . pg_last_error());
}

if (isset($_SESSION['id'])) {
    $userId = $_SESSION['id'];

    $query = "SELECT imagePath, firstName, lastName, otherName, email, phoneNumber, dateOfBirth, residentialAddress, statesecurityNumber, nextofkinName, username, balance FROM accounts WHERE id = $userId";
    $result = pg_query($con, $query);

    if ($result) {
        $row = pg_fetch_assoc($result);
        $imagePath = $row['imagePath'];
        $firstName = $row['firstName'];
        $lastName = $row['lastName'];
        $otherName = $row['otherName'];
        $email = $row['email'];
        $phoneNumber = $row['phoneNumber'];
        $dateOfBirth = $row['dateOfBirth'];
        $residentialAddress = $row['residentialAddress'];
        $statesecurityNumber = $row['statesecurityNumber'];
        $nextofkinName = $row['nextofkinName'];
        $username = $row['username'];
        $balance = $row['balance'];
    } else {
        // Handle the case when the query fails
        echo 'Query failed: ' . pg_last_error();
    }

    $query = "SELECT * FROM transactions WHERE user_id = $userId AND (transaction_type = 'Deposit' OR transaction_type = 'Transfer') ORDER BY id DESC";
    $result = pg_query($con, $query);
    $transactions = pg_fetch_all($result);

} else {
    // Handle the case when the user is not logged in
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <!--TITLE-->
    <title>Dashboard</title>
    <style>
        body {
            font-family: "Inter";
            font-size: 14px;
        }

        #overview {
            display: flex;
            flex-direction: row;
            justify-content: start;
            align-items: center;
            flex-wrap: wrap;
        }

        #overview > div {
            height: 250px;
            width: 30%;
            margin: 10px;
            border-radius: 8px;
            margin-right: 90px;
        }
    </style>
    <!--ICON-->
    <link rel="shortcut icon" href="images/logo.jpg">
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="Responsive.css">
    <link href="https://fonts.googleapis.com/icon?family=Inter" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
</head>
<body>
<div id="dashbod1">
    <div style="height: 70px;border-bottom: 3px solid #780b54de;;">
        <img src="images/logo.jpg" alt="logo" height="50px"
             style="padding: 10px;border-radius: 50px;">
    </div>
    <div style="padding-left: 40px;">
        <P>AVAILABLE BALANCE</P>
        <div id="balance" style="height: 60px;width: 250px; background-color: aqua;padding-top:1px;padding-left:4px">
            <h1>$<?=$balance?></h1>
        </div>
        <div id="union">
            <div>
                <p>Income</p>
                <p>Debits</p>
                <br>
                <button id="btn1"><i class="fa fa-money"></i> <a href="dashboard3.php"
                                                                 style="color: white;text-decoration: none;"> TRANSFER</a>
                </button>
            </div>
            <div style="text-align: right;">
                <p style="color: rgb(13, 88, 63);">66.12%</p>
                <p style="color: red;">24.12%</p>
                <br>
                <button id="btn2"> <i class="fa fa-credit-card"></i> <a href="deposit.php"
                                                                         style="color: white;text-decoration: none;"> Deposit</a>
                </button>

            </div>
        </div>
        <p>MENU</p>
        <ul>
            <li><a href="dashboard.php"><i class="fa fa-tasks"></i></i> Dashboard</a></li>
            <li><a href="dashboard2.php"><i class="fa fa-address-card-o"></i> Account summary</a></li>
            <li><a href="dashboard3.php"><i class="fa fa-share-square-o"></i> Transfer</a></li>
            <li><a href="dashboard4.php"><i class="fa fa-exchange"></i> Cross-border Transfer</a></li>
        </ul>


    </div>
</div>

<div id="dashbod2">
    <div style="height: 70px;background-color: #f5eef3de;;border-bottom: 1px solid #dbcdd6de;">
        <div id="toggleButton" style="height: 50px;width: 150px;text-align: right;
    background-color: #780b54de;;float: right;margin: 10px;text-align: center;display: block;color:white;border-radius:8px">
            <p>Welcome <?=$_SESSION['name']?> <br> click me!</p>
        </div>
        <div id="upperbase" style="display: none;">
            <div style="height: 100px;background-color: rgb(194, 202, 202);margin-top:-6%;color:purple;padding:5px;">
                <h2>Balance:$<?=$balance?></h2>
                <h4><?=$firstName?> <?=$lastName?> </h4><br>

            </div>
            <ul id="upperli">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="profile.php">View Profile</a></li>
                <li><a href="setting.php">Account Settings</a></li>
                <li><a href="activity.php">Login Activity</a></li>
                <hr>
                <li><a href="logout.php">Log Out</a></li>
            </ul>
        </div>
    </div>


    <!--  -->
    <div id="underdash">
        <div id="one" class="standby">
            <div style="border:1px solid #e9bad9de;height: 90px;" id="union1">
                <div style="height: 80px;color: #3c0429de;padding-bottom: 20px;">
                    <p style="font-size: 19px;">Greetings! how are you doing today? </p>
                    <p id="p">Here is the summary of your account at a glance !</p>
                </div>
                <!--  -->
                <div style="height: 80px;">
                    <button id="btn11"><i class="fa fa-money"></i><a href="dashboard3.php"
                                                                    style="color: white;text-decoration: none;"> TRANSFER</a>
                    </button>
                    <button id="btn22"><i class="fa fa-toggle-down"></i><a href="deposit.php"
                                                                           style="color: white;text-decoration: none;"> Deposit</a>
                    </button>
                </div>
            </div>
            <h2 style="color: #780b54de;font-size: 18px;">Overview</h2>
            <div>
                <div style="background-color:#780b54de;;border-radius: 7px;color:white;padding:20px;" id="overview">
                    <div>
                        <p><img src="<?=$imagePath?>" alt="Profile Image" width="300px" height="230px"
                                style="border-radius:10px;border:2px solid #780b54de;"></p>
                    </div>
                    <div>
                        <h2>WELCOME!</h2>
                        <h1><?=$firstName?> <?=$lastName?></h1>
                        <h4 id="p">State security Number=<?=$statesecurityNumber?></h4>
                        <h1>Balance=$<?=$balance?></h1></div>
                </div>
            </div>

            <!--  -->
            <h2 style="color: #780b54de;font-size: 18px;">Recent Transaction Activities</h2>
            <div style="border: 1px solid #f19fd5de;height: 500px;border-radius: 7px;padding:10px;overflow: auto">
                <?php foreach ($transactions as $transaction): ?>

                    Transaction Type: <?php echo $transaction['transaction_type']; ?>|
                    Transaction ID: <?php echo $transaction['id']; ?>|
                    Account Number: <?php echo $transaction['account_number']; ?>|
                    Account Name: <?php echo $transaction['account_name']; ?>|
                    Amount: <?php echo $transaction['amount']; ?>|
                    Date: <?php echo $transaction['transaction_date']; ?>

                    <hr>
                <?php endforeach; ?>
            </div>
            <br>
            <div style="height: 150px;border: 1px solid #f19fd5de;border-radius: 7px;padding: 10px;">

                <div style="width: 70%;padding-left:20px;color: #780b54de;">
                    <h2 style="color: #470531de;"> We’re here to help you!</h2>
                    <p>Ask a question or file a support ticket, manage request, report an issues. Our team support
                        team will get back to you by email.
                    </p>
                    <button id="support"><b>Contact support</b></button>
                </div>
            </div>
            <div style="border-bottom: 1px solid #ec7ac6de;;margin-top: 10px;"></div>
            <div style="height: 100px;color: #1f0316de;">
                <p>© 2023 Bridgewater Financial Union - All rights reserved.</p>
            </div>

        </div>
    </div>
</div>

<script src="dashboard.js"></script>
</body>
</html>
