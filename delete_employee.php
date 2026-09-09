<?php

include "auth.php";
include "config.php";

if (!isset($_GET['id'])) {
    header("Location: employees.php");
    exit();
}

$id = intval($_GET['id']);

$stmt = $conn->prepare(
    "DELETE FROM employees WHERE id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: employees.php");
exit();

?>