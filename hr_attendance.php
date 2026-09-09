<?php

include "auth.php";
include "config.php";

if ($_SESSION['role'] !== 'hr') {
    header("Location: login.php");
    exit();
}

$result = $conn->query("
    SELECT *
    FROM attendance_logs
    ORDER BY attendance_date DESC, time_in DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Attendance Records</title>
</head>
<body>

<h1>Attendance Records</h1>

<a href="hr_dashboard.php">← Back to HR Dashboard</a>

<br><br>

<table border="1" cellpadding="10" cellspacing="0">
    <tr>
        <th>Employee ID</th>
        <th>Full Name</th>
        <th>Department</th>
        <th>Date</th>
        <th>Time In</th>
        <th>Time Out</th>
        <th>Status</th>
        <th>Remarks</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= htmlspecialchars($row['employee_id']) ?></td>
        <td><?= htmlspecialchars($row['full_name']) ?></td>
        <td><?= htmlspecialchars($row['department']) ?></td>
        <td><?= htmlspecialchars($row['attendance_date']) ?></td>
        <td><?= htmlspecialchars($row['time_in']) ?></td>
        <td><?= htmlspecialchars($row['time_out'] ?? '-') ?></td>
        <td><?= htmlspecialchars($row['status']) ?></td>
        <td><?= htmlspecialchars($row['remarks'] ?? '-') ?></td>
    </tr>
    <?php endwhile; ?>

</table>

</body>
</html>