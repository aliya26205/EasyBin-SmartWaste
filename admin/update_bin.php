<?php
/* ================= LOAD CONFIG ================= */
require_once "../config.php";

/* ================= DATABASE CONNECTION ================= */
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("DB Connection Failed");
}

/* ================= GET DATA FROM THINGSPEAK ================= */
$thingspeak_url =
    "https://api.thingspeak.com/channels/" .
    THINGSPEAK_CHANNEL_ID .
    "/fields/1/last.json?api_key=" .
    THINGSPEAK_API_KEY;

$response = @file_get_contents($thingspeak_url);
if ($response === FALSE) {
    exit(); // silently fail (no dashboard break)
}

$data = json_decode($response, true);
if (!isset($data['field1'])) {
    exit();
}

$distance = floatval($data['field1']);

/* ================= DECIDE BIN STATUS ================= */
if ($distance <= 15) {
    $status = "FULL";
} elseif ($distance <= 20) {
    $status = "HALF";
} else {
    $status = "EMPTY";
}

/* ================= FETCH PREVIOUS STATUS ================= */
$stmt = $conn->prepare("
    SELECT prev_status, location 
    FROM bins 
    WHERE bin_id = 'BIN001'
");
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

$prev_status = $row['prev_status'];
$location    = $row['location'];

/* ================= UPDATE BIN DATA ================= */
$stmt = $conn->prepare("
    UPDATE bins 
    SET distance = ?, status = ?, last_updated = NOW()
    WHERE bin_id = 'BIN001'
");
$stmt->bind_param("ds", $distance, $status);
$stmt->execute();

/* ================= SEND WHATSAPP ALERT (ONCE) ================= */
if ($prev_status !== "FULL" && $status === "FULL") {

    $message = "🚨 EasyBin Alert\n\n" .
               "Bin ID: BIN001\n" .
               "Location: $location\n" .
               "Status: FULL\n\n" .
               "⚠️ Action Required: Immediate Cleaning.";

    $whatsapp_url =
        "https://api.green-api.com/waInstance" .
        GREEN_API_INSTANCE_ID .
        "/sendMessage/" .
        GREEN_API_TOKEN;

    $payload = [
        "chatId"  => GREEN_API_ALERT_PHONE . "@c.us",
        "message" => $message
    ];

    $options = [
        "http" => [
            "header"  => "Content-Type: application/json",
            "method"  => "POST",
            "content" => json_encode($payload),
            "timeout" => 10
        ]
    ];

    $context = stream_context_create($options);
    @file_get_contents($whatsapp_url, false, $context);
}

/* ================= UPDATE PREVIOUS STATUS ================= */
$stmt = $conn->prepare("
    UPDATE bins 
    SET prev_status = ?
    WHERE bin_id = 'BIN001'
");
$stmt->bind_param("s", $status);
$stmt->execute();