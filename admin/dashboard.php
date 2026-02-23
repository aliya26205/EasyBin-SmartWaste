<?php
session_start();

if (!isset($_SESSION['admin'])) {
  header("Location: login.php");
  exit();
}

/* ===== LOAD CONFIG ===== */
require_once "../config.php";

/* ===== OPTIONAL BIN UPDATE LOGIC ===== */
include("update_bin.php");

/* ===== DATABASE CONNECTION ===== */
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
  die("Database connection failed");
}

/* ===== FETCH BINS ===== */
$result = $conn->query("SELECT * FROM bins");
$bins = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

/* ===== STATS ===== */
$total = count($bins);
$full  = count(array_filter($bins, fn($b) => $b["status"] === "FULL"));
$half  = count(array_filter($bins, fn($b) => $b["status"] === "HALF"));
$empty = count(array_filter($bins, fn($b) => $b["status"] === "EMPTY"));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="refresh" content="15">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>EasyBin | Admin Dashboard</title>

<link rel="stylesheet" href="/css/main.css?v=30">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
</head>

<body>

<header class="topbar">
  <div class="hamburger" id="hamburger"><i class="fas fa-bars"></i></div>
  <div class="logo-text">EasyBin</div>
  <div class="datetime">
    <div id="date"></div>
    <div id="time"></div>
  </div>
</header>

<aside class="sidebar" id="sidebar">
  <nav class="nav-links">
    <a class="active"><i class="fas fa-chart-line"></i> Dashboard</a>
    <a href="manage_bins.php"><i class="fas fa-trash"></i> Manage Bins</a>
    <a href="manage_workers.php"><i class="fas fa-users"></i> Manage Workers</a>
    <a href="assign_work.php"><i class="fas fa-tasks"></i> Assign Work</a>
  </nav>

  <div class="sidebar-bottom">
    <a href="admin_profile.php"><i class="fas fa-user-cog"></i> Admin Profile</a>
    <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </div>
</aside>

<main class="main">

<section class="hero">
  <div>
    <h1>EasyBin Dashboard</h1>
    <p>Smart Waste Management powered by IoT</p>
    <span class="live-dot">● Live sensor data active</span>
  </div>
</section>

<section class="stats">
  <div class="stat-card clay"><span>Total Bins</span><h2><?= $total ?></h2></div>
  <div class="stat-card clay danger"><span>Full</span><h2><?= $full ?></h2></div>
  <div class="stat-card clay warning"><span>Half</span><h2><?= $half ?></h2></div>
  <div class="stat-card clay success"><span>Empty</span><h2><?= $empty ?></h2></div>
</section>

<section class="box clay">
<h2>Live Dustbin Status</h2>
<div class="table-wrap">
<table>
<tr>
  <th>Bin ID</th>
  <th>Location</th>
  <th>Level</th>
  <th>Status</th>
</tr>
<?php foreach ($bins as $b): ?>
<tr>
  <td><?= htmlspecialchars($b['bin_id']) ?></td>
  <td><?= htmlspecialchars($b['location']) ?></td>
  <td><?= round($b['distance'], 1) ?> cm</td>
  <td>
    <span class="badge <?= strtolower($b['status']) ?>">
      <?= htmlspecialchars($b['status']) ?>
    </span>
  </td>
</tr>
<?php endforeach; ?>
</table>
</div>
</section>

<section class="modules">

  <div class="module clay waste-card">
    <h3>🌱 Waste Analytics</h3>
    <div class="donut-container">
      <div class="donut" id="donut">
        <span id="donutValue">0%</span>
      </div>
    </div>
    <p class="donut-label">Average Bin Fill Level</p>
    <p class="note">Live waste insights from smart bins</p>
  </div>

  <div class="module clay">
    <h3>📅 Calendar / Notes</h3>
    <div class="calendar">
      <div class="cal-grid">
        <?php for ($i = 1; $i <= 28; $i++): ?>
          <div><?= $i ?></div>
        <?php endfor; ?>
      </div>
    </div>
    <textarea placeholder="Admin notes..."></textarea>
  </div>

  <div class="module clay map-card" onclick="openGoogleMaps()">
    <h3>📍 Campus Map</h3>
    <div id="map"></div>
  </div>

</section>

<footer class="footer">© 2026 EasyBin | Admin Panel</footer>

</main>

<script src="/js/dashboard.js"></script>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
const map = L.map('map').setView([12.8913, 74.8702], 16);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
L.marker([12.8913, 74.8702]).addTo(map)
  .bindPopup("St Joseph Engineering College, Mangaluru");

function openGoogleMaps() {
  window.open(
    "https://maps.google.com/?q=St+Joseph+Engineering+College+Mangaluru",
    "_blank"
  );
}

// Demo analytics
const binCapacity = 100;
const currentWaste = 68;
const fillPercentage = Math.round((currentWaste / binCapacity) * 100);

document.getElementById("donutValue").innerText = fillPercentage + "%";
document.getElementById("donut").style.background =
  `conic-gradient(#2ecc71 ${fillPercentage}%, #eaeaea 0)`;
</script>

</body>
</html>