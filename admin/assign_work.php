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

/* ===== FETCH WORKERS ===== */
$workers = $conn->query("SELECT worker_id, name, phone FROM workers");

/* ===== FETCH ASSIGNMENT HISTORY ===== */
$history = $conn->query("
  SELECT wa.*, w.name AS worker_name
  FROM work_assignments wa
  JOIN workers w ON wa.worker_id = w.worker_id
  ORDER BY wa.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Assign Work | EasyBin</title>

<link rel="stylesheet" href="/css/main.css?v=32">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

<!-- ===== TOPBAR ===== -->
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

<!-- ===== SIDEBAR ===== -->
<aside class="sidebar" id="sidebar">
  <nav class="nav-links">
    <a href="dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a>
    <a href="manage_bins.php"><i class="fas fa-trash"></i> Manage Bins</a>
    <a href="manage_workers.php"><i class="fas fa-users"></i> Manage Workers</a>
    <a class="active"><i class="fas fa-tasks"></i> Assign Work</a>
  </nav>

  <div class="sidebar-bottom">
    <a href="admin_profile.php"><i class="fas fa-user-cog"></i> Admin Profile</a>
    <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </div>
</aside>

<!-- ===== MAIN ===== -->
<main class="main">

<section class="hero clay">
  <div>
    <h1>Assign Work</h1>
    <p>Schedule and assign tasks to workers</p>
    <span class="live-dot">● Admin task assignment module</span>
  </div>
</section>

<section class="box clay">
<h2 style="margin-bottom:15px;">📝 Assign Work to Worker</h2>

<form action="assign_work_process.php" method="POST" class="assign-form">

  <div class="form-grid">
    <div>
      <label>Date</label>
      <input type="date" name="work_date" required>
    </div>

    <div>
      <label>Time</label>
      <input type="time" name="work_time" required>
    </div>
  </div>

  <label>Location</label>
  <select name="location" required>
    <option value="">-- Select Location --</option>
    <option>Admin Block</option>
    <option>Academic Block 1</option>
    <option>Academic Block 2</option>
    <option>Academic Block 3</option>
    <option>Ground</option>
    <option>Main Gate</option>
  </select>

  <label>Work Type</label>
  <select name="work_type" required>
    <option value="">-- Select Work --</option>
    <option>Cleaning</option>
    <option>Mopping</option>
    <option>Waste Collection</option>
    <option>Dustbin Check</option>
    <option>Other</option>
  </select>

  <label>Description (optional)</label>
  <textarea name="description" placeholder="Any specific instructions..."></textarea>

  <label>Select Worker</label>
  <select name="worker_id" required>
    <option value="">-- Select Worker --</option>
    <?php while($w = $workers->fetch_assoc()): ?>
      <option value="<?= $w['worker_id'] ?>">
        <?= htmlspecialchars($w['name']) ?> (<?= htmlspecialchars($w['phone']) ?>)
      </option>
    <?php endwhile; ?>
  </select>

  <button type="submit" class="assign-btn">
    <i class="fas fa-paper-plane"></i> Assign Work
  </button>

</form>
</section>

<section class="box clay" style="margin-top:30px;">
<h2>📋 Work Assignment History</h2>

<div class="table-wrap">
<table>
<thead>
<tr>
  <th>Date</th>
  <th>Time</th>
  <th>Location</th>
  <th>Work</th>
  <th>Worker</th>
  <th>Action</th>
</tr>
</thead>
<tbody>

<?php if ($history && $history->num_rows > 0): ?>
  <?php while($row = $history->fetch_assoc()): ?>
  <tr>
    <td><?= htmlspecialchars($row['work_date']) ?></td>
    <td><?= htmlspecialchars($row['work_time']) ?></td>
    <td><?= htmlspecialchars($row['location']) ?></td>
    <td><?= htmlspecialchars($row['work_type']) ?></td>
    <td><?= htmlspecialchars($row['worker_name']) ?></td>
    <td>
      <a href="delete_work.php?id=<?= $row['id'] ?>"
         class="delete-btn"
         onclick="return confirm('Delete this work assignment?')">❌</a>
    </td>
  </tr>
  <?php endwhile; ?>
<?php else: ?>
  <tr>
    <td colspan="6" style="text-align:center;">No work assigned yet</td>
  </tr>
<?php endif; ?>

</tbody>
</table>
</div>
</section>

<footer class="footer">© 2026 EasyBin | Admin Panel</footer>

</main>

<script src="/js/dashboard.js"></script>
</body>
</html>