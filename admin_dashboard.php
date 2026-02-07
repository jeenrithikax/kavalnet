<?php
session_start();
include "db_connect.php";

/* Admin login check */
if (!isset($_SESSION['admin_username']) || !isset($_SESSION['station_id'])) {
    header("Location: admin_login.php");
    exit();
}

$username   = $_SESSION['admin_username'];
$station_id = intval($_SESSION['station_id']);

/* TOTAL REPORTS - THIS STATION ONLY */
$stmt = $conn->prepare(
    "SELECT COUNT(*) AS total 
     FROM public_reports 
     WHERE station_id = ?"
);
$stmt->bind_param("i", $station_id);
$stmt->execute();
$total_public = $stmt->get_result()->fetch_assoc()['total'];

/* PENDING - THIS STATION ONLY */
$stmt = $conn->prepare(
    "SELECT COUNT(*) AS total 
     FROM public_reports 
     WHERE station_id = ? AND status = 'Pending'"
);
$stmt->bind_param("i", $station_id);
$stmt->execute();
$total_pending = $stmt->get_result()->fetch_assoc()['total'];

/* RESOLVED - THIS STATION ONLY */
$stmt = $conn->prepare(
    "SELECT COUNT(*) AS total 
     FROM public_reports 
     WHERE station_id = ? AND status = 'Resolved'"
);
$stmt->bind_param("i", $station_id);
$stmt->execute();
$total_resolved = $stmt->get_result()->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - KaavalNet</title>
    <link rel="stylesheet" href="admin_dashboard.css">
</head>
<body>

<!-- HEADER -->
<div class="header">
    <div class="logo">KAAVALNET</div>
    <div class="profile">
        <span><?= htmlspecialchars($username); ?></span>
        <a href="admin_logout.php">Logout</a>
    </div>
</div>

<!-- MODULES -->
<div class="modules">
    <a href="view_public.php" class="module-card">
        <h3>Public Reports</h3>
        <p>Your police station reports only</p>
    </a>

    <a href="fop_service.php" class="module-card">
        <h3>FOP Service</h3>
        <p>FOP reports for your station</p>
    </a>

    <a href="fop_register.php" class="module-card">
        <h3>FOP Register</h3>
        <p>Create FOP users</p>
    </a>
</div>

<!-- SUMMARY -->
<div class="summary">
    <div class="box">
        <h4>Total Reports</h4>
        <span><?= $total_public; ?></span>
    </div>

    <div class="box">
        <h4>Pending</h4>
        <span><?= $total_pending; ?></span>
    </div>

    <div class="box">
        <h4>Resolved</h4>
        <span><?= $total_resolved; ?></span>
    </div>
</div>

<footer>
    © 2026 KaavalNet | Tamil Nadu Police
</footer>

</body>
</html>
