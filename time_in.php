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

/* Check if employee already has an active Time In today */
$check = $conn->prepare(
    "SELECT id FROM attendance_logs
     WHERE employee_id = ?
     AND attendance_date = CURDATE()
     AND time_out IS NULL
     LIMIT 1"
);

$check->bind_param("s", $employee_id);
$check->execute();

$result = $check->get_result();

if ($result->num_rows > 0) {
    die("This employee is already timed in today.");
}

/* Get faculty/staff information */
$employee = $conn->prepare(
    "SELECT full_name, department
     FROM employees
     WHERE employee_id = ?
     LIMIT 1"
);

$employee->bind_param("s", $employee_id);
$employee->execute();

$employeeResult = $employee->get_result();

if ($employeeResult->num_rows === 0) {
    die("Employee ID not found.");
}

$data = $employeeResult->fetch_assoc();

$full_name = $data['full_name'];
$department = $data['department'];
$time_in = date("H:i:s");

/* Determine attendance status */
$currentTime = date("H:i");

$status = ($currentTime <= "08:00")
    ? "Present"
    : "Late";

/* Save attendance */
$stmt = $conn->prepare(
    "INSERT INTO attendance_logs
    (employee_id, full_name, department, attendance_date, time_in, status, remarks)
    VALUES (?, ?, ?, CURDATE(), ?, ?, ?)"
);

$remarks = ($status === "Present")
    ? "Timed in on time"
    : "Timed in late";

$stmt->bind_param(
    "ssssss",
    $employee_id,
    $full_name,
    $department,
    $time_in,
    $status,
    $remarks
);

if ($stmt->execute()) {

    if ($_SESSION['role'] === 'faculty') {
        header("Location: faculty_dashboard.php?success=timein");
    } else {
        header("Location: admin.php?success=timein");
    }

    exit();
}

echo "Error: " . $stmt->error;

?>