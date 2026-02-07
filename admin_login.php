<?php
session_start();
include "db_connect.php";

$error = "";

if (isset($_POST['login'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];

    // Plain password check
    $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {

        $admin = $result->fetch_assoc();

        // ✅ SESSION SET PROPERLY
        $_SESSION['admin'] = $admin['admin_id'];
        $_SESSION['admin_username'] = $admin['username'];
        $_SESSION['station_id'] = $admin['station_id'];

        header("Location: admin_dashboard.php");
        exit();

    } else {
        $error = "❌ Invalid Username or Password";
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Admin Login - KaavalNet</title>
    <link rel="stylesheet" href="admin_login.css">
</head>

<body>

<!-- HEADER -->
<div class="header-box">
    <div class="header-left">
        <div class="logo-text">KAVALNET</div>
    </div>

    <div class="header-center">
        <img src="tn_police_logo.png" class="logo">
        <div class="sub-title">TAMILNADU CRIME REPORTING PORTAL</div>
    </div>

    <div class="header-right">
        <a href="index.php" class="home-btn">Home</a>
    </div>
</div>

<!-- LOGIN BOX -->
<div class="login-container">
    <h2>Police Admin Login</h2>

    <?php if ($error != "") { ?>
        <div class="error"><?php echo $error; ?></div>
    <?php } ?>

    <form method="post">
        <input type="text" name="username" placeholder="Enter Username" required>
        <input type="password" name="password" placeholder="Enter Password" required>
        <button type="submit" name="login">Login</button>
    </form>
</div>

<div class="welcome-text">
    Welcome to KaavalNet
</div>

</body>
</html>
