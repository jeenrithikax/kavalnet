<?php
session_start();
include "db_connect.php";

/* Police/Admin login check */
if (!isset($_SESSION['station_id']) || empty($_SESSION['station_id'])) {
    header("Location: police_login.php"); // correct login page
    exit();
}

$station_id = intval($_SESSION['station_id']); // extra safety

/* ONLY this police station reports */
$sql = "SELECT * FROM public_reports 
        WHERE station_id = ? 
        ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $station_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Public Reports - KaavalNet</title>
    <link rel="stylesheet" href="view_public.css">
</head>
<body>

<!-- HEADER -->
<div class="header-box">
    <div class="logo-text">KAAVALNET</div>
    <a href="admin_dashboard.php" class="home-btn">Dashboard</a>
</div>

<div class="container">
    <h2>Public Crime Reports</h2>

    <?php if ($result->num_rows == 0) { ?>
        <p class="no-data">No reports available for your station.</p>
    <?php } else { ?>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Crime</th>
                <th>Location</th>
                <th>Victim</th>
                <th>Suspect</th>
                <th>Evidence</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>

        <tbody>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= $row['id']; ?></td>

                <td>
                    <b><?= htmlspecialchars($row['crime_type']); ?></b><br>
                    <?= htmlspecialchars($row['crime_description']); ?>
                </td>

                <td>
                    <?= htmlspecialchars($row['crime_location']); ?><br>
                    <?= htmlspecialchars($row['city']); ?> - <?= $row['pincode']; ?>
                </td>

                <td>
                    <?= htmlspecialchars($row['victim_name']); ?><br>
                    <?= htmlspecialchars($row['victim_phone']); ?>
                </td>

                <td>
                    <b><?= htmlspecialchars($row['suspect_name']); ?></b><br>
                    <?= htmlspecialchars($row['suspect_details']); ?>
                </td>

                <td>
                    <?php if (!empty($row['attachment'])) { ?>
                        <a href="<?= $row['attachment']; ?>" target="_blank">View</a>
                    <?php } else { ?>
                        No File
                    <?php } ?>
                </td>

                <td>
                    <form method="post" action="update_status.php">
                        <input type="hidden" name="report_id" value="<?= $row['id']; ?>">
                        <select name="status" onchange="this.form.submit()">
                            <?php
                            $status = ["Pending","Accepted","Rejected","In Progress","Resolved"];
                            foreach ($status as $s) {
                                $sel = ($row['status'] == $s) ? "selected" : "";
                                echo "<option value='$s' $sel>$s</option>";
                            }
                            ?>
                        </select>
                    </form>
                </td>

                <td><?= $row['created_at']; ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

    <?php } ?>
</div>

</body>
</html>
