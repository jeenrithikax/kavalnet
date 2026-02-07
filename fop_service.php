<?php
session_start();
include "db_connect.php";

/* 🔐 Police station login check */
if(!isset($_SESSION['station_id'])){
    header("Location: admin_login.php");
    exit;
}

$station_id = $_SESSION['station_id'];

/* 🔴 MODULE 1 – FOP REPORTS (ONLY THIS STATION) */
$reports = $conn->prepare("
    SELECT 
        fr.report_id,
        fr.crime_type,
        fr.crime_description,
        fr.crime_date,
        fr.crime_time,
        fr.victim_name,
        fr.suspect_name,
        fr.suspect_details,
        fr.status,
        fr.attachment,
        fu.username
    FROM fop_report fr
    JOIN fop_register fu ON fr.fop_id = fu.fop_id
    WHERE fr.station_id = ?
    ORDER BY fr.report_id DESC
");
$reports->bind_param("i", $station_id);
$reports->execute();
$fop_reports = $reports->get_result();

/* 🔵 MODULE 2 – FOP LOGIN DETAILS (ONLY USERS WHO LOGGED IN) */
$fop_logins = $conn->prepare("
    SELECT username, last_login
    FROM fop_register
    WHERE last_login IS NOT NULL
");
$fop_logins->execute();
$fop_users = $fop_logins->get_result();
?>

<!DOCTYPE html>
<html>
<head>
<title>FOP Service - KaavalNet</title>
<link rel="stylesheet" href="fop_service.css">
</head>
<body>

<!-- HEADER -->
<div class="header">
    <div class="logo">KAVALNET FOP SERVICE</div>
    <div class="nav-buttons">
        <button onclick="location.href='profile.php'">Profile</button>
        <button onclick="location.href='dashboard.php'">Dashboard</button>
    </div>
</div>

<div class="container">

<!-- MODULE SELECTION BUTTONS -->
<div class="buttons">
    <button onclick="showReports()">FOP Reports</button>
    <button onclick="showLogins()">FOP Logins</button>
</div>

<!-- 🔴 FOP REPORTS -->
<div id="reports">
<h2>FOP Reports (Station ID: <?= $station_id ?>)</h2>

<table>
<tr>
<th>Report ID</th>
<th>FOP Username</th>
<th>Crime Type</th>
<th>Description</th>
<th>Date</th>
<th>Time</th>
<th>Victim</th>
<th>Suspect</th>
<th>Suspect Details</th>
<th>Status</th>
<th>Update Status</th>
<th>Evidence</th>
</tr>

<?php while($r = $fop_reports->fetch_assoc()){ ?>
<tr>
<td><?= $r['report_id'] ?></td>
<td><?= htmlspecialchars($r['username']) ?></td>
<td><?= htmlspecialchars($r['crime_type']) ?></td>
<td><?= htmlspecialchars($r['crime_description']) ?></td>
<td><?= $r['crime_date'] ?></td>
<td><?= $r['crime_time'] ?></td>
<td><?= htmlspecialchars($r['victim_name']) ?></td>
<td><?= htmlspecialchars($r['suspect_name']) ?></td>
<td><?= htmlspecialchars($r['suspect_details']) ?></td>
<td><b><?= $r['status'] ?></b></td>

<!-- Update Status Dropdown -->
<td>
<form method="post" action="update_fop_status.php">
<input type="hidden" name="report_id" value="<?= $r['report_id'] ?>">
<select name="status" onchange="this.form.submit()">
<option <?= $r['status']=="Pending"?"selected":"" ?>>Pending</option>
<option <?= $r['status']=="In Progress"?"selected":"" ?>>In Progress</option>
<option <?= $r['status']=="Resolved"?"selected":"" ?>>Resolved</option>
<option <?= $r['status']=="Rejected"?"selected":"" ?>>Rejected</option>
</select>
</form>
</td>

<!-- Evidence Upload -->
<td>
<?php if(!empty($r['evidence'])): ?>
<a href="evidence_files/<?= $r['evidence'] ?>" target="_blank">View</a>
<?php endif; ?>

<form method="post" action="add_evidence.php" enctype="multipart/form-data">
<input type="hidden" name="report_id" value="<?= $r['report_id'] ?>">
<input type="file" name="evidence_file" required>
<button type="submit">Add</button>
</form>
</td>

</tr>
<?php } ?>
</table>
</div>

<!-- 🔵 FOP LOGIN DETAILS -->
<div id="logins" style="display:none;">
<h2>FOP Login Details</h2>

<table>
<tr>
<th>Username</th>
<th>Last Login</th>
</tr>

<?php while($u = $fop_users->fetch_assoc()){ ?>
<tr>
<td><?= htmlspecialchars($u['username']) ?></td>
<td><?= $u['last_login'] ?></td>
</tr>
<?php } ?>
</table>
</div>

</div>

<script>
function showReports(){
    document.getElementById("reports").style.display="block";
    document.getElementById("logins").style.display="none";
}
function showLogins(){
    document.getElementById("reports").style.display="none";
    document.getElementById("logins").style.display="block";
}
window.onload = showReports;
</script>

</body>
</html>
