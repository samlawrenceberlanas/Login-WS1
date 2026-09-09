<?php

include "auth.php";
include "config.php";

if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'hr') {
    header("Location: login.php");
    exit();
}

$message = "";

/* Add employee */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $employee_id = trim($_POST['employee_id'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $position = trim($_POST['position'] ?? '');

    if ($employee_id && $full_name && $department && $position) {

    // Check if Employee ID already exists
    $check = $conn->prepare(
        "SELECT id FROM employees WHERE employee_id = ?"
    );

    $check->bind_param("s", $employee_id);
    $check->execute();

    $checkResult = $check->get_result();

    if ($checkResult->num_rows > 0) {

        $message = "Employee ID already exists!";

    } else {

        $stmt = $conn->prepare(
            "INSERT INTO employees
            (employee_id, full_name, department, position)
            VALUES (?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "ssss",
            $employee_id,
            $full_name,
            $department,
            $position
        );

        if ($stmt->execute()) {
            $message = "Employee added successfully!";
        } else {
            $message = "Error: " . $stmt->error;
        }
    }

} else {

    $message = "Please fill in all fields.";
}

}

/* Get employees */
$result = $conn->query(
    "SELECT * FROM employees ORDER BY full_name ASC"
);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Faculty & Staff</title>

    <link rel="stylesheet" href="admin.css">
</head>

<body>

<header class="topbar">

    <div>
        <p class="eyebrow">School Log Book</p>
        <h1>Faculty & Staff</h1>
    </div>

    <div class="top-actions">
        <a href="admin.php">Dashboard</a>
    </div>

</header>

<div class="wrap">

    <aside class="sidebar">

        <nav>
            <ul>
                <li>
                    <a href="admin.php">Dashboard</a>
                </li>

                <li class="active">
                    Faculty & Staff
                </li>
            </ul>
        </nav>

    </aside>

    <main class="main">

        <?php if ($message): ?>

            <div class="success-message">
                <?= htmlspecialchars($message) ?>
            </div>

        <?php endif; ?>


        <!-- Add Employee -->

        <section class="panel">

            <div class="panel-header">
                <h2>Add Faculty / Staff</h2>
            </div>

            <form method="POST">

                <label>
                    Employee ID

                    <input
                        type="text"
                        name="employee_id"
                        placeholder="Example: FAC-002"
                        required
                    >
                </label>

                <label>
                    Full Name

                    <input
                        type="text"
                        name="full_name"
                        placeholder="Full name"
                        required
                    >
                </label>

                <label>
                    Department

                    <input
                        type="text"
                        name="department"
                        placeholder="Department"
                        required
                    >
                </label>

                <label>
                    Position

                    <input
                        type="text"
                        name="position"
                        placeholder="Instructor / Staff"
                        required
                    >
                </label>

                <button type="submit">
                    Add Faculty / Staff
                </button>

            </form>

        </section>


        <!-- Employee List -->

        <section class="panel">

            <div class="panel-header">
                <h2>Faculty & Staff List</h2>
            </div>

            <table>

                <thead>

                   <tr>
    <th>Employee ID</th>
    <th>Name</th>
    <th>Department</th>
    <th>Position</th>
    <th>Action</th>
</tr>

                </thead>

                <tbody>

                    <?php while ($row = $result->fetch_assoc()): ?>

                        <tr>

                            <td>
                                <?= htmlspecialchars($row['employee_id']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($row['full_name']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($row['department']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($row['position']) ?>
                            </td>
                            <td>

                             <a href="edit_employee.php?id=<?= $row['id'] ?>">
                            Edit
                             </a>

                             |

                             <a 
                                href="delete_employee.php?id=<?= $row['id'] ?>"
                                onclick="return confirm('Are you sure you want to delete this employee?');"
                            >
                                Delete
                            </a>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                </tbody>

            </table>

        </section>

    </main>

</div>

</body>
</html>