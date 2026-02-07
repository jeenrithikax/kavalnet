<?php
include 'db_connect.php'; // database connection
?>

<!DOCTYPE html>
<html>
<head>
    <title>KaavalNet Home</title>
    <link rel="stylesheet" type="text/css" href="index.css">
</head>
<body>
 <!-- Top Bar -->
<div class="top-bar">
    <div class="left">KAAVALNET</div>
    <div class="right">
    <a href="admin_login.php">Admin Login</a>
    <a href="fop_login.php">FOP Login</a>
    </div>
</div>


    <!-- Main Content -->
    <div class="main-container">
        <!-- Box 1: TN Crime Reporting Portal -->
        <div class="box">
            <img src="tn_police_logo.png" alt="TN Police Logo">
            <h2>TamilNadu Crime Reporting Portal</h2>
            <button onclick="window.location.href='public_report.php'">Report a Crime</button>
        </div>

        <!-- Box 2: Welcome -->
        <div class="welcome-box">
            Welcome to KaavalNet
        </div>
    </div>
</body>
</html>
