<?php
session_start();

/* ===== LOAD CONFIG ===== */
require_once "../config.php";

/* ===== DATABASE CONNECTION ===== */
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
  die("Database connection failed");
}

$error = "";

/* ===== LOGIN LOGIC ===== */
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $username = $_POST['username'];
  $password = $_POST['password'];

  $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    if (password_verify($password, $user['password'])) {
      $_SESSION['admin'] = $user['username'];
      header("Location: dashboard.php");
      exit();
    }
  }

  $error = "Invalid username or password";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login | EasyBin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- MAIN GLOBAL CSS -->
  <link rel="stylesheet" href="../css/main.css">
</head>

<body class="login-body">

  <div class="login-container">

    <!-- LEFT PANEL -->
    <div class="login-left">
      <h2>Welcome Back!</h2>
      <p>Login to manage dustbin status and workers</p>
    </div>

    <!-- RIGHT PANEL -->
    <div class="login-right">
      <h2>Admin Login</h2>

      <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" autocomplete="off">
        <input
          type="text"
          name="username"
          placeholder="Username"
          required
        >

        <input
          type="password"
          name="password"
          placeholder="Password"
          required
        >

        <button type="submit">SIGN IN</button>
      </form>

      <div class="login-help">
        Having trouble logging in?<br>
        Please contact the system administrator.
      </div>
    </div>

  </div>

</body>
</html>