<?php

include "auth.php";
include "config.php";

$employee_id = $_POST['employee_id'];
$full_name = $_POST['full_name'];
$department = $_POST['department'];
$attendance_date = $_POST['attendance_date'];
$time_in = $_POST['time_in'];
$time_out = $_POST['time_out'];
$status = $_POST['status'];
$remarks = $_POST['remarks'];

$sql = "INSERT INTO attendance_logs
        (employee_id, full_name, department, attendance_date, time_in, time_out, status, remarks)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "ssssssss",
    $employee_id,
    $full_name,
    $department,
    $attendance_date,
    $time_in,
    $time_out,
    $status,
    $remarks
);

if ($stmt->execute()) {

    header("Location: admin.php?success=1");
    exit();

} else {

    echo "Error: " . $stmt->error;

}

?>