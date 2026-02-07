<?php
include "db_connect.php";
$msg = "";

/* Police stations (station_id, station_name, pincode) */
$stations = $conn->query("SELECT station_id, station_name, pincode FROM police_station ORDER BY station_name");

if(isset($_POST['submit'])){

    // Crime details
    $crime_type = $_POST['crime_type'];
    $crime_description = $_POST['crime_description'];
    $crime_date = $_POST['crime_date'];
    $crime_time = $_POST['crime_time'];
    $crime_location = $_POST['crime_location'];
    $city = $_POST['city'];

    // Selected police station
    $station_id = $_POST['station_id'];
    $pincode = $_POST['pincode'];

    // Victim
    $victim_name = $_POST['victim_name'];
    $victim_age = $_POST['victim_age'];
    $victim_address = $_POST['victim_address'];
    $victim_phone = $_POST['victim_phone'];

    // Suspect
    $suspect_name = $_POST['suspect_name'];
    $suspect_details = $_POST['suspect_details'];

    // Evidence
    $attachment = "";
    if(!empty($_FILES['attachment']['name'])){
        if(!is_dir("uploads")){
            mkdir("uploads",0777,true);
        }
        $attachment = "uploads/".time()."_".basename($_FILES['attachment']['name']);
        move_uploaded_file($_FILES['attachment']['tmp_name'], $attachment);
    }

    // Insert
    $sql = "INSERT INTO public_reports
    (station_id, pincode, crime_type, crime_description, crime_date, crime_time,
     crime_location, city, victim_name, victim_age, victim_address, victim_phone,
     suspect_name, suspect_details, attachment)
    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "issssssssisssss",
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
        $msg = "<div class='success'>✅ Report submitted successfully</div>";
    }else{
        $msg = "<div class='error'>❌ Error occurred</div>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Public Crime Report</title>
<link rel="stylesheet" href="public_report.css">

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
</head>

<body>

<div class="header">
    <div class="logo">KAVALNET</div>
    <a href="index.php" class="home-btn">Home</a>
</div>

<div class="container">
<h2>Public Crime Report</h2>

<form method="post" enctype="multipart/form-data">

<!-- Crime Details -->
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

<label>Crime Description</label>
<textarea name="crime_description" required></textarea>

<label>Date</label>
<input type="date" name="crime_date" required>

<label>Time</label>
<input type="time" name="crime_time" required>

<label>City</label>
<input type="text" name="city" required>

<label>Police Station (by Pincode)</label>
<select name="station_id" id="station" required onchange="setPincode(this)">
    <option value="">Select</option>
    <?php while($s=$stations->fetch_assoc()){ ?>
        <option value="<?= $s['station_id'] ?>" data-pin="<?= $s['pincode'] ?>">
            <?= $s['station_name']." - ".$s['pincode'] ?>
        </option>
    <?php } ?>
</select>

<input type="hidden" name="pincode" id="pincode">

<label>Crime Location (Auto)</label>
<input type="text" name="crime_location" id="crime_location" readonly>

<div id="map"></div>

<!-- Victim -->
<h3>Victim Details</h3>

<label>Name</label>
<input type="text" name="victim_name" required>

<label>Age</label>
<input type="number" name="victim_age">

<label>Address</label>
<textarea name="victim_address"></textarea>

<label>Phone</label>
<input type="text" name="victim_phone">

<!-- Suspect -->
<h3>Suspect Details</h3>

<label>Suspect Name</label>
<input type="text" name="suspect_name">

<label>Description</label>
<textarea name="suspect_details"></textarea>

<!-- Evidence -->
<h3>Evidence</h3>
<input type="file" name="attachment">

<button type="submit" name="submit">Submit Report</button>

<?= $msg ?>

</form>
</div>

<script>
function setPincode(sel){
    document.getElementById("pincode").value =
    sel.options[sel.selectedIndex].getAttribute("data-pin");
}

/* MAP – click anywhere selection */
let map, marker;

// Default Chennai center
let defaultLat = 13.0827;
let defaultLng = 80.2707;

// Initialize map
map = L.map('map').setView([defaultLat, defaultLng], 12);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap'
}).addTo(map);

// Click event
map.on('click', function(e) {

    let lat = e.latlng.lat;
    let lng = e.latlng.lng;

    // Marker move / create
    if(marker){
        marker.setLatLng(e.latlng);
    } else {
        marker = L.marker(e.latlng).addTo(map);
    }

    // Set location to input
    document.getElementById("crime_location").value =
        lat.toFixed(6) + ", " + lng.toFixed(6);
});

// Optional: show current location marker (NOT mandatory)
if(navigator.geolocation){
    navigator.geolocation.getCurrentPosition(function(pos){
        let clat = pos.coords.latitude;
        let clng = pos.coords.longitude;

        L.circleMarker([clat, clng], {
            radius: 6,
            color: 'blue'
        }).addTo(map).bindPopup("Your current location");
    });
}
</script>

</body>
</html>
