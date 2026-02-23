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

/* ===== DELETE WORK ASSIGNMENT ===== */
if (isset($_GET['id'])) {
  $id = (int) $_GET['id'];

  $stmt = $conn->prepare("DELETE FROM work_assignments WHERE id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $stmt->close();
}

header("Location: assign_work.php");
exit();