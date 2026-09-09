<?php

include "auth.php";
include "config.php";

if (!isset($_GET['id'])) {
    header("Location: employees.php");
    exit();
}

$id = intval($_GET['id']);

$stmt = $conn->prepare(
    "SELECT * FROM employees WHERE id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Employee not found.");
}

$employee = $result->fetch_assoc();


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $employee_id = trim($_POST['employee_id']);
    $full_name = trim($_POST['full_name']);
    $department = trim($_POST['department']);
    $position = trim($_POST['position']);

    if (
        $employee_id === '' ||
        $full_name === '' ||
        $department === '' ||
        $position === ''
    ) {
        $error = "All fields are required.";
    } else {

        // Check if Employee ID already belongs to another employee
        $check = $conn->prepare(
            "SELECT id FROM employees
             WHERE employee_id = ?
             AND id != ?"
        );

        $check->bind_param("si", $employee_id, $id);
        $check->execute();

        $checkResult = $check->get_result();

        if ($checkResult->num_rows > 0) {

            $error = "Employee ID already exists.";

        } else {

            $update = $conn->prepare(
                "UPDATE employees
                 SET employee_id = ?,
                     full_name = ?,
                     department = ?,
                     position = ?
                 WHERE id = ?"
            );

            $update->bind_param(
                "ssssi",
                $employee_id,
                $full_name,
                $department,
                $position,
                $id
            );

            if ($update->execute()) {
                header("Location: employees.php");
                exit();
            }

            $error = "Unable to update employee.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Faculty & Staff</title>

    <link rel="stylesheet" href="admin.css">

</head>

<body>

<header class="topbar">

    <div>
        <p class="eyebrow">School Log Book</p>
        <h1>Edit Faculty & Staff</h1>
    </div>

</header>


<div class="wrap">

    <main class="main">

        <section class="panel">

            <div class="panel-header">
                <h2>Edit Employee</h2>
            </div>

            <?php if (isset($error)): ?>

                <p>
                    <?= htmlspecialchars($error) ?>
                </p>

            <?php endif; ?>


            <form method="POST">

                <label>
                    Employee ID

                    <input
                        type="text"
                        name="employee_id"
                        value="<?= htmlspecialchars($employee['employee_id']) ?>"
                        required
                    >

                </label>


                <label>
                    Full Name

                    <input
                        type="text"
                        name="full_name"
                        value="<?= htmlspecialchars($employee['full_name']) ?>"
                        required
                    >

                </label>


                <label>
                    Department

                    <input
                        type="text"
                        name="department"
                        value="<?= htmlspecialchars($employee['department']) ?>"
                        required
                    >

                </label>


                <label>
                    Position

                    <input
                        type="text"
                        name="position"
                        value="<?= htmlspecialchars($employee['position']) ?>"
                        required
                    >

                </label>


                <button type="submit">
                    Save Changes
                </button>

                <a href="employees.php">
                    Cancel
                </a>

            </form>

        </section>

    </main>

</div>

</body>

</html>