<?php
session_start();

if (!isset($_SESSION['admin'])) {
  header("Location: login.php");
  exit();
}

/* ===== LOAD CONFIG (DB + KEYS) ===== */
require_once "../config.php";

/* ===== DATABASE CONNECTION ===== */
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
  die("Database connection failed");
}

$admin_username = $_SESSION['admin'];

/* ===== FETCH LOGGED ADMIN ===== */
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $admin_username);
$stmt->execute();
$me = $stmt->get_result()->fetch_assoc();
$admin_id = $me['user_id'];

/* ===== UPDATE PROFILE ===== */
if (isset($_POST['update_profile'])) {
  $new_username = $_POST['username'];
  $about = $_POST['about'];

  if (!empty($_FILES['profile_photo']['name'])) {
    $pname = time() . "_" . $_FILES['profile_photo']['name'];
    move_uploaded_file($_FILES['profile_photo']['tmp_name'], "uploads/profile/$pname");
    $conn->query("UPDATE users SET profile_photo='$pname' WHERE user_id=$admin_id");
  }

  if (!empty($_FILES['cover_photo']['name'])) {
    $cname = time() . "_" . $_FILES['cover_photo']['name'];
    move_uploaded_file($_FILES['cover_photo']['tmp_name'], "uploads/cover/$cname");
    $conn->query("UPDATE users SET cover_photo='$cname' WHERE user_id=$admin_id");
  }

  $conn->query("UPDATE users SET username='$new_username', about='$about' WHERE user_id=$admin_id");
  $_SESSION['admin'] = $new_username;

  header("Location: admin_profile.php");
  exit();
}

/* ===== CHANGE PASSWORD ===== */
if (isset($_POST['change_password'])) {
  $pass = password_hash($_POST['new_password'], PASSWORD_BCRYPT);
  $conn->query("UPDATE users SET password='$pass' WHERE user_id=$admin_id");
}

/* ===== ADD ADMIN ===== */
if (isset($_POST['add_admin'])) {
  $u = $_POST['new_username'];
  $p = password_hash($_POST['new_password'], PASSWORD_BCRYPT);
  $conn->query("INSERT INTO users (username,password,profile_photo,cover_photo)
                VALUES ('$u','$p','default_profile.png','default_cover.jpg')");
}

/* ===== DELETE ADMIN ===== */
if (isset($_GET['delete']) && $_GET['delete'] != $admin_id) {
  $did = $_GET['delete'];
  $conn->query("DELETE FROM users WHERE user_id=$did");
}

/* ===== FETCH ALL ADMINS ===== */
$admins = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Profile | EasyBin</title>

<link rel="stylesheet" href="/css/main.css?v=30">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
.profile-cover {
  height: 260px;
  background: url('uploads/cover/<?= $me['cover_photo'] ?: 'default_cover.jpg' ?>') center/cover;
  border-radius: 18px;
  position: relative;
}
.profile-avatar {
  width: 140px;
  height: 140px;
  border-radius: 50%;
  border: 6px solid #fff;
  background: url('uploads/profile/<?= $me['profile_photo'] ?: 'default_profile.png' ?>') center/cover;
  position: absolute;
  bottom: -70px;
  left: 40px;
}
.profile-section { margin-top: 90px; }
.profile-section input,
.profile-section textarea {
  width: 100%;
  padding: 14px;
  border-radius: 12px;
  border: 1px solid #ccc;
  margin-bottom: 22px;
}
.profile-section button {
  background: #145a32;
  color: #fff;
  padding: 12px 20px;
  border-radius: 14px;
  border: none;
}
.admin-table td, .admin-table th { padding: 14px; }
</style>
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
    <a href="dashboard.php">Dashboard</a>
    <a href="manage_bins.php">Manage Bins</a>
    <a href="manage_workers.php">Manage Workers</a>
    <a href="assign_work.php">Assign Work</a>
  </nav>
  <div class="sidebar-bottom">
    <a class="active">Admin Profile</a>
    <a href="logout.php">Logout</a>
  </div>
</aside>

<main class="main">

<section class="box clay">
  <div class="profile-cover"><div class="profile-avatar"></div></div>

  <div class="profile-section">
    <h2>👤 Admin Profile</h2>

    <form method="post" enctype="multipart/form-data">
      <input type="text" name="username" value="<?= $me['username'] ?>" required>
      <textarea name="about"><?= $me['about'] ?></textarea>
      <input type="file" name="profile_photo">
      <input type="file" name="cover_photo">
      <button name="update_profile">Save Profile</button>
    </form>

    <hr>

    <form method="post">
      <input type="password" name="new_password" placeholder="New password" required>
      <button name="change_password">Update Password</button>
    </form>

    <hr>

    <form method="post">
      <input type="text" name="new_username" placeholder="Username" required>
      <input type="password" name="new_password" placeholder="Password" required>
      <button name="add_admin">Add Admin</button>
    </form>

    <hr>

    <table class="admin-table">
      <tr><th>ID</th><th>Username</th><th>Action</th></tr>
      <?php while($a = $admins->fetch_assoc()): ?>
      <tr>
        <td><?= $a['user_id'] ?></td>
        <td><?= $a['username'] ?></td>
        <td>
          <?= ($a['user_id'] != $admin_id) ? "<a href='?delete={$a['user_id']}'>Delete</a>" : "You"; ?>
        </td>
      </tr>
      <?php endwhile; ?>
    </table>
  </div>
</section>

<footer class="footer">© 2026 EasyBin</footer>
</main>

<script src="/js/dashboard.js"></script>
</body>
</html>