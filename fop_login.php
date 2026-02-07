<?php
session_start();
include "db_connect.php";

$msg = "";

if(isset($_POST['login'])){

    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM fop_register 
            WHERE username=? AND password=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss",$username,$password);
    $stmt->execute();
    $res = $stmt->get_result();

    if($res->num_rows == 1){
        $row = $res->fetch_assoc();

        $_SESSION['fop_id']   = $row['fop_id'];
        $_SESSION['fop_name'] = $row['username'];

        /* 🕒 UPDATE LOGIN TIME */
        $conn->query("UPDATE fop_register 
                      SET last_login = NOW() 
                      WHERE fop_id=".$row['fop_id']);

        header("Location: fop_dashboard.php");
        exit;
    }
    else{
        $msg = "Invalid Username or Password";
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>FOP Login - KaavalNet</title>
    <link rel="stylesheet" href="fop_login.css">
</head>
<body>

<!-- HEADER -->
<div class="header-box">
    <a href="index.php" class="home-btn">Home</a>
    <div class="header-center">
        <img src="tn_police_logo.png" class="logo">
        <h1>TAMILNADU CRIME REPORTING PORTAL</h1>
    </div>
</div>

<!-- LOGIN BOX -->
<div class="login-container">
    <h2>FOP Login</h2>

    <?php if ($msg != "") { ?>
        <p class="error-msg"><?= $msg; ?></p>
    <?php } ?>

    <form method="post">
        <input type="text" name="username" placeholder="Enter FOP Username" required>
        <input type="password" name="password" placeholder="Enter Password" required>
        <button type="submit" name="login">Login</button>
    </form>
</div>

<!-- WELCOME BOX -->
<div class="welcome-text">Welcome to KaavalNet</div>

</body>
</html>
