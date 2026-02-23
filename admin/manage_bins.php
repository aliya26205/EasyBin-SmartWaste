<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

/* ===== LOAD CONFIG ===== */
require_once "../config.php";

/* ===== DATABASE CONNECTION ===== */
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Database connection failed");
}

/* ===== ADD BIN ===== */
if (isset($_POST['add_bin'])) {
    $bin_id   = $_POST['bin_id'];
    $location = $_POST['location'];

    // Prevent duplicate BIN
    $stmt = $conn->prepare("SELECT bin_id FROM bins WHERE bin_id = ?");
    $stmt->bind_param("s", $bin_id);
    $stmt->execute();
    $check = $stmt->get_result();

    if ($check->num_rows === 0) {
        $stmt = $conn->prepare("
            INSERT INTO bins (bin_id, location, distance, status)
            VALUES (?, ?, 0, 'EMPTY')
        ");
        $stmt->bind_param("ss", $bin_id, $location);
        $stmt->execute();
    }
}

/* ===== DELETE BIN (NOT BIN001) ===== */
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    if ($id !== "BIN001") {
        $stmt = $conn->prepare("DELETE FROM bins WHERE bin_id = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
    }
}

/* ===== FETCH BINS ===== */
$bins = $conn->query("SELECT * FROM bins ORDER BY bin_id ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Bins | EasyBin</title>

<link rel="stylesheet" href="/css/main.css?v=40">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

<header class="topbar">
  <div class="hamburger" id="hamburger">
    <i class="fas fa-bars"></i>
  </div>
  <div class="logo-text">EasyBin</div>
  <div class="datetime">
    <div id="date"></div>
    <div id="time"></div>
  </div>
</header>

<aside class="sidebar" id="sidebar">
  <nav class="nav-links">
    <a href="dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a>
    <a class="active"><i class="fas fa-trash"></i> Manage Bins</a>
    <a href="manage_workers.php"><i class="fas fa-users"></i> Manage Workers</a>
    <a href="assign_work.php"><i class="fas fa-tasks"></i> Assign Work</a>
  </nav>

  <div class="sidebar-bottom">
    <a href="admin_profile.php"><i class="fas fa-user-cog"></i> Admin Profile</a>
    <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </div>
</aside>

<main class="main">

<section class="hero clay">
  <div>
    <h1>Manage Bins</h1>
    <p>Add, view and control dustbins connected to the system</p>
    <span class="live-dot">● BIN001 is reserved for IoT sensor</span>
  </div>
</section>

<section class="box clay">
  <h2>➕ Add New Bin</h2>

  <form method="post" class="assign-form">
    <div class="form-grid">
      <div>
        <label>Bin ID</label>
        <input type="text" name="bin_id" placeholder="e.g. BIN003" required>
      </div>

      <div>
        <label>Location</label>
        <input type="text" name="location" placeholder="Location name" required>
      </div>
    </div>

    <button type="submit" name="add_bin" class="assign-btn">
      <i class="fas fa-plus"></i> Add Bin
    </button>
  </form>
</section>

<section class="box clay" style="margin-top:30px;">
  <h2>🗑 Bin List</h2>

  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Bin ID</th>
          <th>Location</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>

      <?php while ($b = $bins->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($b['bin_id']) ?></td>
          <td><?= htmlspecialchars($b['location']) ?></td>
          <td>
            <span class="badge <?= strtolower($b['status']) ?>">
              <?= htmlspecialchars($b['status']) ?>
            </span>
          </td>
          <td>
            <?php if ($b['bin_id'] === "BIN001"): ?>
              <span class="lock">🔒 IoT Connected</span>
            <?php else: ?>
              <a
                href="?delete=<?= $b['bin_id'] ?>"
                class="delete-btn"
                onclick="return confirm('Delete this bin?')"
              >
                ❌ Delete
              </a>
            <?php endif; ?>
          </td>
        </tr>
      <?php endwhile; ?>

      </tbody>
    </table>
  </div>
</section>

<footer class="footer">© 2026 EasyBin | Admin Panel</footer>

</main>

<script src="/js/dashboard.js"></script>
</body>
</html>