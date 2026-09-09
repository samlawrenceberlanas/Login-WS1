<?php

include "auth.php";
include "config.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: admin.php");
    exit();
}

$employee_id = trim($_POST['employee_id'] ?? '');
$status = $_POST['status'] ?? '';
$attendance_date = $_POST['attendance_date'] ?? '';
$remarks = trim($_POST['remarks'] ?? '');

if ($employee_id === '' || $attendance_date === '') {
    header("Location: admin.php");
    exit();
}

if (!in_array($status, ['Absent', 'On Leave'], true)) {
    header("Location: admin.php");
    exit();
}

/* Find employee */
$stmt = $conn->prepare(
    "SELECT full_name, department
     FROM employees
     WHERE employee_id = ?
     LIMIT 1"
);

$stmt->bind_param("s", $employee_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("Employee ID not found.");
}

$employee = $result->fetch_assoc();

$full_name = $employee['full_name'];
$department = $employee['department'];
/* Check for existing attendance */
$check = $conn->prepare(
    "SELECT id
     FROM attendance_logs
     WHERE employee_id = ?
     AND attendance_date = ?
     LIMIT 1"
);

$check->bind_param(
    "ss",
    $employee_id,
    $attendance_date
);

$check->execute();

$existing = $check->get_result();

if ($existing->num_rows > 0) {
    $check->close();

    header("Location: admin.php?error=already_recorded");
    exit();
}

$check->close();
/* Save attendance */
$stmt = $conn->prepare(
    "INSERT INTO attendance_logs
    (employee_id, full_name, department, attendance_date, status, remarks)
    VALUES (?, ?, ?, ?, ?, ?)"
);

$stmt->bind_param(
    "ssssss",
    $employee_id,
    $full_name,
    $department,
    $attendance_date,
    $status,
    $remarks
);

$stmt->execute();

$stmt->close();

header("Location: admin.php?message=attendance_saved");
exit();
?>