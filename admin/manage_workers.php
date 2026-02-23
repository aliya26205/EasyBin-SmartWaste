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
    die("DB Connection Failed");
}

/* ===== ADD WORKER ===== */
if (isset($_POST['add_worker'])) {
    $name  = $_POST['name'];
    $phone = $_POST['phone'];
    $area  = $_POST['area'];

    $stmt = $conn->prepare(
        "INSERT INTO workers (name, phone, area) VALUES (?, ?, ?)"
    );
    $stmt->bind_param("sss", $name, $phone, $area);
    $stmt->execute();
    $stmt->close();
}

/* ===== DELETE WORKER ===== */
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];

    $stmt = $conn->prepare("DELETE FROM workers WHERE worker_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

/* ===== FETCH WORKERS ===== */
$workers = $conn->query("SELECT * FROM workers ORDER BY worker_id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Workers | EasyBin</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="/css/main.css?v=33">
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
    <a href="manage_bins.php"><i class="fas fa-trash"></i> Manage Bins</a>
    <a class="active"><i class="fas fa-users"></i> Manage Workers</a>
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
    <h1>Manage Workers</h1>
    <p>Add, view and manage sanitation staff</p>
    <span class="live-dot">● Worker management module</span>
  </div>
</section>

<section class="box clay">
  <h2>👷 Add New Worker</h2>

  <form method="post" class="assign-form">
    <label>Worker Name</label>
    <input type="text" name="name" required>

    <label>Phone Number</label>
    <input type="text" name="phone" placeholder="91XXXXXXXXXX" required>

    <label>Assigned Area</label>
    <input type="text" name="area" required>

    <button type="submit" name="add_worker" class="assign-btn">
      <i class="fas fa-user-plus"></i> Add Worker
    </button>
  </form>
</section>

<section class="box clay" style="margin-top:30px;">
  <h2>📋 Workers List</h2>

  <div class="table-wrap">
    <table>
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Phone</th>
        <th>Area</th>
        <th>Action</th>
      </tr>

      <?php if ($workers && $workers->num_rows > 0): ?>
        <?php while ($w = $workers->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($w['worker_id']) ?></td>
          <td><?= htmlspecialchars($w['name']) ?></td>
          <td><?= htmlspecialchars($w['phone']) ?></td>
          <td><?= htmlspecialchars($w['area']) ?></td>
          <td>
            <a href="?delete=<?= $w['worker_id'] ?>"
               onclick="return confirm('Delete this worker?')"
               class="delete-btn">❌</a>
          </td>
        </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr>
          <td colspan="5" style="text-align:center;">No workers added yet</td>
        </tr>
      <?php endif; ?>
    </table>
  </div>
</section>

<footer class="footer">© 2026 EasyBin | Admin Panel</footer>

</main>

<script src="/js/dashboard.js"></script>
</body>
</html>