<?php

include "auth.php";
include "config.php";

$today = date("Y-m-d");

// Total attendance records
$total = $conn->query(
    "SELECT COUNT(*) AS total
     FROM attendance_logs
     WHERE attendance_date = '$today'"
)->fetch_assoc()['total'];

// Present
$present = $conn->query(
    "SELECT COUNT(*) AS total
     FROM attendance_logs
     WHERE attendance_date = '$today'
     AND status = 'Present'"
)->fetch_assoc()['total'];

// Late
$late = $conn->query(
    "SELECT COUNT(*) AS total
     FROM attendance_logs
     WHERE attendance_date = '$today'
     AND status = 'Late'"
)->fetch_assoc()['total'];

// Absent
$absent = $conn->query(
    "SELECT COUNT(*) AS total
     FROM attendance_logs
     WHERE attendance_date = '$today'
     AND status = 'Absent'"
)->fetch_assoc()['total'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>HR Dashboard</title>

    <link rel="stylesheet" href="admin.css">
</head>

<body>

<header class="topbar">

    <div>
        <p class="eyebrow">Office System</p>
        <h1>HR Dashboard</h1>
    </div>

    <div class="top-actions">
        <a href="reports.php">Reports</a>
        <a href="logout.php">Logout</a>
    </div>

</header>

<div class="wrap">

    <main class="content">

        <h2>Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</h2>

        <p>Today's Attendance</p>

        <div class="stats-grid">

            <div class="stat-card">
                <h3>Total</h3>
                <strong><?= $total ?></strong>
            </div>

            <div class="stat-card">
                <h3>Present</h3>
                <strong><?= $present ?></strong>
            </div>

            <div class="stat-card">
                <h3>Late</h3>
                <strong><?= $late ?></strong>
            </div>

            <div class="stat-card">
                <h3>Absent</h3>
                <strong><?= $absent ?></strong>
            </div>

        </div>

        <br>

            <a href="hr_attendance.php">
    View Attendance Log
        </a>

    &nbsp;&nbsp;

    <a href="hr_reports.php">
    View Reports
</a>

    </main>

</div>

</body>
</html>