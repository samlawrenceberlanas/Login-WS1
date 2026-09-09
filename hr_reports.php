<?php

include "auth.php";
include "config.php";

if ($_SESSION['role'] !== 'hr') {
    header("Location: login.php");
    exit();
}

$today = date("Y-m-d");

// Total attendance today
$stmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM attendance_logs
    WHERE attendance_date = ?
");
$stmt->bind_param("s", $today);
$stmt->execute();
$total = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

// Present
$stmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM attendance_logs
    WHERE attendance_date = ? AND status = 'Present'
");
$stmt->bind_param("s", $today);
$stmt->execute();
$present = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

// Late
$stmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM attendance_logs
    WHERE attendance_date = ? AND status = 'Late'
");
$stmt->bind_param("s", $today);
$stmt->execute();
$late = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

?>

<!DOCTYPE html>
<html>
<head>
    <title>HR Reports</title>
</head>
<body>

<h1>HR Attendance Reports</h1>

<a href="hr_dashboard.php">← Back to HR Dashboard</a>

<br><br>

<h2>Today's Summary</h2>

<table border="1" cellpadding="15" cellspacing="0">
    <tr>
        <th>Total</th>
        <th>Present</th>
        <th>Late</th>
    </tr>

    <tr>
        <td><?= $total ?></td>
        <td><?= $present ?></td>
        <td><?= $late ?></td>
    </tr>
</table>

</body>
</html>