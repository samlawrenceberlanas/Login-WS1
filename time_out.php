<?php

include "auth.php";
include "config.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: admin.php");
    exit();
}

$employee_id = trim($_POST['employee_id'] ?? '');

if ($employee_id === '') {
    die("Employee ID is required.");
}

$time_out = date("H:i:s");

$stmt = $conn->prepare(
    "UPDATE attendance_logs
     SET time_out = ?
     WHERE employee_id = ?
     AND attendance_date = CURDATE()
     AND time_out IS NULL
     ORDER BY id DESC
     LIMIT 1"
);

$stmt->bind_param(
    "ss",
    $time_out,
    $employee_id
);

if ($stmt->execute()) {

    if ($stmt->affected_rows === 0) {
        die("No active Time In record found for this employee.");
    }

   if ($_SESSION['role'] === 'faculty') {
    header("Location: faculty_dashboard.php?success=timeout");
} else {
    header("Location: admin.php?success=timeout");
}

exit();
}

echo "Error: " . $stmt->error;

?>