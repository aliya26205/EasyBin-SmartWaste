<?php
session_start();

if (isset($_SESSION['admin_logged_in'])) {
    header("Location: admin/dashboard.php");
    exit();
} else {
    header("Location: admin/login.php");
    exit();
}
