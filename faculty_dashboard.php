<?php

include "auth.php";
include "config.php";

if ($_SESSION['role'] !== 'faculty') {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$employee_id = $_SESSION['employee_id'] ?? '';

$records = [];

if ($employee_id !== '') {

    $stmt = $conn->prepare(
        "SELECT employee_id, full_name, department,
                attendance_date, time_in, time_out,
                status, remarks
         FROM attendance_logs
         WHERE employee_id = ?
         ORDER BY attendance_date DESC, time_in DESC"
    );

    $stmt->bind_param("s", $employee_id);
    $stmt->execute();

    $result = $stmt->get_result();

    $records = $result->fetch_all(MYSQLI_ASSOC);

    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Faculty Dashboard</title>

    <link rel="stylesheet" href="admin.css">
</head>

<body>

<header class="topbar">

    <div>
        <p class="eyebrow">Faculty & Staff Logbook</p>
        <h1>Faculty Dashboard</h1>
    </div>

    <div class="top-actions">
        <a href="logout.php">Logout</a>
    </div>

</header>

<div class="wrap">

    <main class="content">

        <h2>Welcome, <?= htmlspecialchars($username) ?>!</h2>

        <p>You are logged in as a Faculty/Staff user.</p>

        <div class="stats-grid">

            <div class="stat-card">
                <h3>Attendance</h3>
                <strong>✓</strong>
                <p>Record your attendance</p>
            </div>

            <div class="stat-card">
                <h3>My Records</h3>
                <strong>📋</strong>
                <p>View your attendance</p>
            </div>

        </div>

        <br>

        <form action="time_in.php" method="POST" style="display:inline;">
    <input type="hidden" name="employee_id" value="<?= htmlspecialchars($_SESSION['employee_id'] ?? '') ?>">
    <button type="submit">Time In</button>
</form>
    <form action="time_out.php" method="POST" style="display:inline;">
    <input type="hidden" name="employee_id" value="<?= htmlspecialchars($_SESSION['employee_id'] ?? '') ?>">
    <button type="submit">Time Out</button>
</form>

        <h2>My Attendance Records</h2>

<table border="1" cellpadding="10" cellspacing="0">
    <tr>
        <th>Date</th>
        <th>Time In</th>
        <th>Time Out</th>
        <th>Status</th>
        <th>Remarks</th>
    </tr>

    <?php if (empty($records)): ?>
        <tr>
            <td colspan="5">No attendance records found.</td>
        </tr>
    <?php else: ?>

        <?php foreach ($records as $record): ?>
            <tr>
                <td><?= htmlspecialchars($record['attendance_date']) ?></td>
                <td><?= htmlspecialchars($record['time_in'] ?? '-') ?></td>
                <td><?= htmlspecialchars($record['time_out'] ?? '-') ?></td>
                <td><?= htmlspecialchars($record['status']) ?></td>
                <td><?= htmlspecialchars($record['remarks'] ?? '-') ?></td>
            </tr>
        <?php endforeach; ?>

    <?php endif; ?>
</table>

    </main>

</div>

</body>
</html>