<?php
session_start();
include "db_connect.php";

// Admin check
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

$message = "";

// Form submit
if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];
    $district = $_POST['district'];
    $joining_date = $_POST['joining_date'];
    $proof = "";
    $profile_photo = "";

    // File upload - proof
    if (isset($_FILES['proof']) && $_FILES['proof']['error'] == 0) {
        $proof = 'uploads/' . time() . '_' . $_FILES['proof']['name'];
        move_uploaded_file($_FILES['proof']['tmp_name'], $proof);
    }

    // File upload - profile_photo
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] == 0) {
        $profile_photo = 'uploads/' . time() . '_' . $_FILES['profile_photo']['name'];
        move_uploaded_file($_FILES['profile_photo']['tmp_name'], $profile_photo);
    }

    // Insert FOP user
    $stmt = $conn->prepare("INSERT INTO fop_register(username, password, full_name, email, phone, dob, gender, address, district, joining_date, proof, profile_photo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssssss", $username, $password, $full_name, $email, $phone, $dob, $gender, $address, $district, $joining_date, $proof, $profile_photo);

    if ($stmt->execute()) {
        $message = "FOP Registered Successfully!";
    } else {
        $message = "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>FOP Register - KaavalNet</title>
    <link rel="stylesheet" href="fop_register.css">
</head>
<body>

<!-- HEADER -->
<div class="header-box">
    <a href="admin_dashboard.php" class="home-btn">Home</a>
    <div class="logo-text">KAVALNET</div>
</div>

<!-- FORM CONTAINER -->
<div class="container">
    <h2>FOP Register</h2>

    <?php if ($message != "") { ?>
        <div class="message"><?php echo $message; ?></div>
    <?php } ?>

    <form method="post" enctype="multipart/form-data">
        <label>Username</label>
        <input type="text" name="username" placeholder="Enter FOP Username" required>

        <label>Password</label>
        <input type="password" name="password" placeholder="Enter Password" required>

        <label>Full Name</label>
        <input type="text" name="full_name" placeholder="Enter Full Name" required>

        <label>Email</label>
        <input type="email" name="email" placeholder="Enter Email" required>

        <label>Phone</label>
        <input type="text" name="phone" placeholder="Enter Phone Number" required>

        <label>Date of Birth</label>
        <input type="date" name="dob" required>

        <label>Gender</label>
        <select name="gender" required>
            <option value="">Select Gender</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
            <option value="Other">Other</option>
        </select>

        <label>Address</label>
        <input type="text" name="address" placeholder="Enter Address" required>

        <label>District</label>
        <input type="text" name="district" placeholder="Enter District" required>

        <label>Joining Date</label>
        <input type="date" name="joining_date" required>

        <label>Proof Document</label>
        <input type="file" name="proof" accept=".jpg,.png,.pdf">

        <label>Upload Profile Photo</label>
        <input type="file" name="profile_photo" accept=".jpg,.png">

        <button type="submit" name="register">Register FOP</button>
    </form>
</div>

</body>
</html>
