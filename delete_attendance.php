<?php

include "auth.php";
include "config.php";

$id = $_GET['id'];

$sql = "DELETE FROM attendance_logs WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

$stmt->execute();

header("Location: admin.php");
exit();

?>