<?php
session_start();

if (!isset($_SESSION['admin'])) {
  header("Location: login.php");
  exit();
}

/* ================= LOAD CONFIG ================= */
require_once "../config.php";

/* ================= DATABASE CONNECTION ================= */
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
  die("Database connection failed");
}

/* ================= GET FORM DATA ================= */
$work_date  = $_POST['work_date'];
$work_time  = $_POST['work_time'];
$location   = $_POST['location'];
$work_type  = $_POST['work_type'];
$desc       = $_POST['description'];
$worker_id  = $_POST['worker_id'];

/* ================= SAVE WORK ASSIGNMENT ================= */
$stmt = $conn->prepare("
  INSERT INTO work_assignments 
  (work_date, work_time, location, work_type, description, worker_id)
  VALUES (?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
  "sssssi",
  $work_date,
  $work_time,
  $location,
  $work_type,
  $desc,
  $worker_id
);

$stmt->execute();
$stmt->close();

/* ================= FETCH WORKER DETAILS ================= */
$stmt = $conn->prepare("
  SELECT name, phone 
  FROM workers 
  WHERE worker_id = ?
");
$stmt->bind_param("i", $worker_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
  header("Location: assign_work.php?error=worker");
  exit();
}

$worker = $result->fetch_assoc();
$worker_name  = $worker['name'];
$worker_phone = $worker['phone'];

/* ================= CREATE MESSAGE ================= */
$message  = "📋 *New Work Assigned*\n\n";
$message .= "👤 Worker: $worker_name\n";
$message .= "📅 Date: $work_date\n";
$message .= "⏰ Time: $work_time\n";
$message .= "📍 Location: $location\n";
$message .= "🧹 Work: $work_type\n";

if (!empty($desc)) {
  $message .= "📝 Note: $desc\n";
}

$message .= "\nPlease report on time.\n— EasyBin Admin";

/* ================= GREEN API SEND ================= */
$url = "https://api.green-api.com/waInstance" . GREEN_API_INSTANCE_ID . "/sendMessage/" . GREEN_API_TOKEN;

$data = [
  "chatId"  => $worker_phone . "@c.us",
  "message" => $message
];

$options = [
  "http" => [
    "header"  => "Content-Type: application/json\r\n",
    "method"  => "POST",
    "content" => json_encode($data),
    "timeout" => 10
  ]
];

$context = stream_context_create($options);
@file_get_contents($url, false, $context);

/* ================= REDIRECT BACK ================= */
header("Location: assign_work.php?success=1");
exit();