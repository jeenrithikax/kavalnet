<?php
session_start();
include "db_connect.php";

/* 🔐 Login check */
if(!isset($_SESSION['fop_id'])){
    header("Location: fop_login.php");
    exit;
}

$fop_id   = $_SESSION['fop_id'];
$username = $_SESSION['fop_name'];
$msg = "";

/* 🚓 Police stations */
$stations = $conn->query(
    "SELECT station_id, station_name, pincode 
     FROM police_station ORDER BY station_name"
);

/* 📤 Form submit */
if(isset($_POST['submit'])){

    $crime_type        = $_POST['crime_type'];
    $crime_description = $_POST['crime_description'];
    $crime_date        = $_POST['crime_date'];
    $crime_time        = $_POST['crime_time'];
    $crime_location    = $_POST['crime_location'];
    $city              = $_POST['city'];

    $station_id = $_POST['station_id'];
    $pincode    = $_POST['pincode'];

    $victim_name    = $_POST['victim_name'];
    $victim_age     = $_POST['victim_age'];
    $victim_address = $_POST['victim_address'];
    $victim_phone   = $_POST['victim_phone'];

    $suspect_name    = $_POST['suspect_name'];
    $suspect_details = $_POST['suspect_details'];

    /* 📎 File upload */
    $attachment = "";
    if(!empty($_FILES['attachment']['name'])){
        if(!is_dir("uploads")){
            mkdir("uploads",0777,true);
        }
        $attachment = "uploads/".time()."_".$_FILES['attachment']['name'];
        move_uploaded_file($_FILES['attachment']['tmp_name'],$attachment);
    }

    /* 🧠 Correct SQL + datatype */
    $sql = "INSERT INTO fop_report
    (fop_id, station_id, pincode, crime_type, crime_description,
     crime_date, crime_time, crime_location, city,
     victim_name, victim_age, victim_address, victim_phone,
     suspect_name, suspect_details, attachment)
    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "iiissssssissssss",
        $fop_id,
        $station_id,
        $pincode,
        $crime_type,
        $crime_description,
        $crime_date,
        $crime_time,
        $crime_location,
        $city,
        $victim_name,
        $victim_age,
        $victim_address,
        $victim_phone,
        $suspect_name,
        $suspect_details,
        $attachment
    );

    if($stmt->execute()){
        $msg = "<p class='success'>✅ Report submitted successfully</p>";
    }else{
        $msg = "<p class='error'>❌ Something went wrong</p>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>FOP Crime Report - KaavalNet</title>

<link rel="stylesheet" href="fop_report.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
</head>

<body>

<!-- 🔷 HEADER -->
<div class="header">
    <div class="logo">KAVALNET</div>

    <div class="profile">
        <span><?= htmlspecialchars($username) ?></span>
        <a href="fop_logout.php">Logout</a>
    </div>
</div>

<div class="container">
<h2>FOP Crime Report</h2>

<form method="post" enctype="multipart/form-data">

<h3>Crime Details</h3>

<label>Crime Type</label>
<select name="crime_type" required>
    <option value="">Select</option>
    <option>Theft</option>
    <option>Assault</option>
    <option>Cyber Crime</option>
    <option>Domestic Violence</option>
    <option>Murder</option>
    <option>Other</option>
</select>

<label>Description</label>
<textarea name="crime_description" required></textarea>

<label>Date</label>
<input type="date" name="crime_date" required>

<label>Time</label>
<input type="time" name="crime_time" required>

<label>City</label>
<input type="text" name="city" required>

<label>Police Station</label>
<select name="station_id" onchange="setPincode(this)" required>
    <option value="">Select</option>
    <?php while($s=$stations->fetch_assoc()){ ?>
        <option value="<?= $s['station_id'] ?>" data-pin="<?= $s['pincode'] ?>">
            <?= $s['station_name']." - ".$s['pincode'] ?>
        </option>
    <?php } ?>
</select>

<input type="hidden" name="pincode" id="pincode">

<label>Crime Location (Click anywhere on map)</label>
<input type="text" name="crime_location" id="crime_location" readonly required>

<div id="map"></div>

<h3>Victim Details</h3>
<input type="text" name="victim_name" placeholder="Victim Name" required>
<input type="number" name="victim_age" placeholder="Age">
<textarea name="victim_address" placeholder="Address"></textarea>
<input type="text" name="victim_phone" placeholder="Phone">

<h3>Suspect Details</h3>
<input type="text" name="suspect_name" placeholder="Suspect Name">
<textarea name="suspect_details" placeholder="Suspect Details"></textarea>

<h3>Evidence</h3>
<input type="file" name="attachment">

<button type="submit" name="submit">Submit Report</button>

<?= $msg ?>

</form>
</div>

<script>
function setPincode(sel){
    document.getElementById("pincode").value =
        sel.options[sel.selectedIndex].dataset.pin;
}

/* 🗺️ Map */
let map = L.map('map').setView([13.0827,80.2707],12);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

let marker;
map.on('click',function(e){
    if(marker) marker.setLatLng(e.latlng);
    else marker = L.marker(e.latlng).addTo(map);

    document.getElementById("crime_location").value =
        e.latlng.lat.toFixed(6)+", "+e.latlng.lng.toFixed(6);
});
</script>

</body>
</html>
