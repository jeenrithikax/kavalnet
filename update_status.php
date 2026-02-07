<?php
session_start();
include "db_connect.php";

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

if (isset($_POST['report_id']) && isset($_POST['status'])) {
    $report_id = $_POST['report_id'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE public_reports SET status=? WHERE id=?");
    $stmt->bind_param("si", $status, $report_id);
    $stmt->execute();
}

header("Location: view_public.php");
exit();
?>
