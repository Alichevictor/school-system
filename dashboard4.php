<?php
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
	header('Location: sign-in.html');
	exit;
}
$POSTGRES_CONNECTION_STRING = "postgres://default:FoQMG0CR6IWE@ep-muddy-art-24176362.ap-southeast-1.postgres.vercel-storage.com:5432/verceldb";

// Attempt to connect to PostgreSQL using the connection string
$con = pg_connect($POSTGRES_CONNECTION_STRING);

if (!$con) {
    exit('Failed to connect to PostgreSQL: ' . pg_last_error());
}

if (isset($_SESSION['id'])) {
  $userId = $_SESSION['id'];

  $stmt = $con->prepare('SELECT imagePath, firstName, lastName, otherName, email, phoneNumber, dateOfBirth, residentialAddress, statesecurityNumber, nextofkinName, username, password, balance FROM accounts WHERE id = ?');

  if ($stmt) {
      $stmt->bind_param('i', $userId);
      $stmt->execute();
      $stmt->bind_result( $firstName, $lastName, $otherName, $email, $phoneNumber, $dateOfBirth, $residentialAddress,  $nextofkinName, $username, $password, );
      $stmt->fetch();
      $stmt->close();
  } else {
      // Handle the case when the prepared statement couldn't be created
      echo 'Could not prepare statement!';
  }
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
  <title>Transfer</title>
  <style>
     body{
      font-family: "Inter";
      font-size: 14px;
    }
    #formdiv {
        text-align: center;
        padding: 20px;
    }

    input {
        width: 400px;
        height: 20px;
        border: 1px solid #780b54de;
        border-radius: 10px;
        padding: 10px;
        margin: 10px 0;
        font-size: 14px;
    }

    button {
        background-color:#780b54de;
        color: white;
        border: none;
        cursor: pointer;
        height: 30px;
        border-radius: 8px;
    }
    button:hover{
        background-color: #d23b9fde;
    }
    #summaryOverlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        #summaryContent {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            height: 500px;
            width: 300px;
          }
          .close-button {
            margin-top: 10px;
            text-align: center;
            position: absolute;
            top: 85%;
        }

        .close-button > button{
            border-radius: 8px;
        }
    #successMessage{
      position: absolute;
      top: 30%;
      left: 40%;
      z-index: 2000;
      height: 200px;
      width: 200px;
      text-align: center;
      padding-top: 30px;
      background-color: white;
    }

    #successMessage > button{
      border-radius: 8px;
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

<div id="dashbod1" >
<div style="height: 70px;border-bottom: 3px solid #780b54de;;">
<img src="images/logo.jpg" alt="logo" height="50px" style="padding: 10px;border-radius: 50px;">
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
  <button id="btn1"><i class="fa fa-money"></i> <a href="dashboard3.php" style="color: white;text-decoration: none;"> TRANSFER</a></button>
 </div>
  <div style="text-align: right;">
    <p style="color: rgb(13, 88, 63);">66.12%</p>
    <p style="color: red;">24.12%</p>
  <br>
  <button id="btn2"> <i class="fa fa-credit-card"></i> <a href="deposit.php" style="color: white;text-decoration: none;"> Deposit</a></button>
  
  </div>
</div>
<p>MENU</p>
<ul>
  <li ><a href="dashboard.php"><i class="fa fa-tasks" ></i></i> Dashboard</a></li>
  <li><a href="dashboard2.php"><i class="fa fa-address-card-o"></i> Account summary</a></li>
  <li><a href="dashboard3.php"><i class="fa fa-share-square-o"></i> Transfer</a></li>
  <li><a href="dashboard4.php"><i class="fa fa-exchange"></i> Cross-border Transfer</a></li>
</ul>


</div>
</div>

<div id="dashbod2">
  <div style="height: 70px;background-color: #f5eef3de;;border-bottom: 1px solid #dbcdd6de;" >
  <div id="toggleButton" style="height: 50px;width: 150px;text-align: right;
    background-color: #780b54de;;float: right;margin: 10px;text-align: center;display: block;color:white;border-radius:8px"><p >Welcome <?=$_SESSION['name']?> <br> click me!</p></div>
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
  <li><a href="">Sign Out</a></li>
  </ul>
</div>  
 </div>


 
<!--  -->
<div id="underdash" >
<div id="one" class="standby">
    
    
<h2> Cross-border transfer.</h2>
<p style="color: #2e0420de;"> Cross-border transfer is fast and safe way to send money across different countries. The funds are deposited into the recipient's bank account associated with the IBAN or Swift Code you have added.
</p> 
<button id="btn1" style="width: 150px;"><i class="fa fa-exchange"></i><a href="dashboard3.php" style="color: white;text-decoration: none;"> Local Transfer</a></button>
<hr>
  
  
  <h3 style="color: #780b54de;">Recipient's and banking information</h3>
  <div id="formdiv">
  <form id="transferFormz" action="transfer.php" method="post">

            <input type="text" id="email" placeholder="Email "><br>
           <input type="number" name="bank name" id="" placeholder="Phone Number"><br>
            <input type="text" name="zip" id="" placeholder="Postal or zip code"><br>
            <input type="text" name="AccountName" placeholder="Recipiant full name"><br>
            <input type="text" name="swift" id="" placeholder="Swift Code"><br>
            <input type="number" name="acc_number" id=""  placeholder="Account number"><br>
            <input type="number" name="amount" id=""  placeholder="Amount"><br>
            <button type="button" id="continueButton" onclick="simulateLoading()">Continue</button><br>
            <span style="font-size: smaller;">Note: our transfer fee is included.</span>
    </form>
    </div>
    <div id="summaryOverlay">
      <div id="summaryContent"></div>
      <div class="close-button">
          <button onclick="closeSummary()" >Continue</button>
      </div>
    </div>
    <!-- Transfer Successful Message Div -->
    <div id="successMessage" style="display: none;">
     <p>Transfer Successful! </p> <br>
      <button onclick="clearForm()">Complete</button>
    </div>
    <script>
    function validateForm() {
    var fieldsToCheck = [
        { field: document.getElementById("email"), name: "Email" },
        { field: document.getElementsByName("bank name")[0], name: "Bank name" },
        { field: document.getElementsByName("zip")[0], name: "ZIP Code" },
        { field: document.getElementsByName("AccountName")[0], name: "Account holder" },
        { field: document.getElementsByName("swift")[0], name: "SWIFT Code" },
        { field: document.getElementsByName("acc_number")[0], name: "Account Number" },
        { field: document.getElementsByName("amount")[0], name: "Amount to Transfer" }
    ];

    var allFieldsFilled = true;

    for (var i = 0; i < fieldsToCheck.length; i++) {
        var fieldObj = fieldsToCheck[i];
        if (fieldObj.field.value === "") {
            fieldObj.field.style.borderColor = "red";
            allFieldsFilled = false;
        } else {
            fieldObj.field.style.borderColor = ""; // Reset the border color
        }
    }

    return allFieldsFilled;
}


       function simulateLoading() {
           if (validateForm()) {
               var continueButton = document.getElementById("continueButton");
               continueButton.textContent = "processing...";
               setTimeout(showSummary, 2000); // Call showSummary after 2 seconds
           }
       }
      
   
   
   
     function showSummary() {
           var email = document.getElementById("email").value;
           var bankName = document.getElementsByName("bank name")[0].value;
           var zip = document.getElementsByName("zip")[0].value;
           var accountName = document.getElementsByName("AccountName")[0].value;
           var swift = document.getElementsByName("swift")[0].value;
           var acc_number = document.getElementsByName("acc_number")[0].value;
           var amount = document.getElementsByName("amount")[0].value;
   
         var summary = `<h2 style="color:purple">Bridgewater bank</h2>
                       <b>Please confirm your transaction details before proceeding</b> <br> <br> <br>
                        Email:${email}<br><br><br>
                        Amount: ${amount} USD<br><br><br>
                        Bank Name: ${bankName}<br><br><br> 
                        Account Number: ${acc_number}<br><br><br>
                        Account Name: ${accountName}<br><br><br>
                       Swift code: ${swift}<br><br><br>
                        Zip code:${zip}
                        `;
   
          // Populate the summary div
          var summaryDiv = document.getElementById("summaryContent");
               summaryDiv.innerHTML = summary;
   
               // Show the summary overlay
               var overlay = document.getElementById("summaryOverlay");
               overlay.style.display = "flex";
   
               // Reset the button text
               var continueButton = document.getElementById("continueButton");
               continueButton.textContent = "Continue";
           }
   
   
           function closeSummary() {
       // Change the close button text to "Loading..."
       var closeButton = document.querySelector("#summaryOverlay .close-button button");
       closeButton.textContent = "processing...";
   
       setTimeout(function() {
           // After 4 seconds, hide the loading message and the summary content
           closeButton.style.display = "none";
           var summaryDiv = document.getElementById("summaryContent");
           summaryDiv.style.display = "none";
   
           // Display the "Transfer Successful" message
           var successDiv = document.getElementById("successMessage");
           successDiv.style.display = "block";
       }, 4000); // Display the success message after 4 seconds
   }
   
   
   function clearForm() {
    var transferForm = document.getElementById("transferFormz");
    transferForm.submit();
       // Clear the form fields
       var email = document.getElementById("email").value="";
           var bankName = document.getElementsByName("bank name")[0].value="";
           var zip = document.getElementsByName("zip")[0].value="";
           var accountName = document.getElementsByName("AccountName")[0].value="";
           var swift = document.getElementsByName("swift")[0].value="";
           var acc_number = document.getElementsByName("acc_number")[0].value="";
           var amount = document.getElementsByName("amount")[0].value="";
   
       // Hide the success message div
       var successDiv = document.getElementById("successMessage");
       successDiv.style.display = "none";
   
       // Hide the summary overlay
       var overlay = document.getElementById("summaryOverlay");
       overlay.style.display = "none";
   }
   
     
   </script>


<div style="border-bottom: 1px solid #ec7ac6de;;margin-top: 10px;"></div>
<div style="height: 70px;color: #1f0516de;">
    <p>© 2023 Bridgewater Financial Union - All rights reserved.</p>
    </div>
</div>
</div>
</div>



<script src="dashboard.js"></script>
      </body>
      </html>