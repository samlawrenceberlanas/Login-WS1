<?php

include "auth.php";
include "config.php";

$selected_date = $_GET['date'] ?? date('Y-m-d');


/* Get attendance records for selected date */

$stmt = $conn->prepare(
    "SELECT *
     FROM attendance_logs
     WHERE attendance_date = ?
     ORDER BY time_in ASC"
);

$stmt->bind_param("s", $selected_date);

$stmt->execute();

$result = $stmt->get_result();


/* Attendance statistics */

$total = $conn->query(
    "SELECT COUNT(*) AS total
     FROM attendance_logs
     WHERE attendance_date = '$selected_date'"
)->fetch_assoc()['total'];


$present = $conn->query(
    "SELECT COUNT(*) AS total
     FROM attendance_logs
     WHERE attendance_date = '$selected_date'
     AND status = 'Present'"
)->fetch_assoc()['total'];


$late = $conn->query(
    "SELECT COUNT(*) AS total
     FROM attendance_logs
     WHERE attendance_date = '$selected_date'
     AND status = 'Late'"
)->fetch_assoc()['total'];


$absent = $conn->query(
    "SELECT COUNT(*) AS total
     FROM attendance_logs
     WHERE attendance_date = '$selected_date'
     AND status = 'Absent'"
)->fetch_assoc()['total'];


$onLeave = $conn->query(
    "SELECT COUNT(*) AS total
     FROM attendance_logs
     WHERE attendance_date = '$selected_date'
     AND status = 'On Leave'"
)->fetch_assoc()['total'];

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Attendance Reports</title>

    <link rel="stylesheet" href="admin.css">

</head>

<body>

<header class="topbar">

    <div>

        <p class="eyebrow">
            School Log Book
        </p>

        <h1>
            Attendance Reports
        </h1>

    </div>

    <div class="top-actions">

        <a href="admin.php">
            Dashboard
        </a>

    </div>

</header>


<div class="wrap">

    <aside class="sidebar">

        <nav>

            <ul>

                <li>
                    <a href="admin.php">
                        Dashboard
                    </a>
                </li>

                <li>
                    <a href="admin.php">
                        Log Book
                    </a>
                </li>

                <li>
                    <a href="employees.php">
                        Faculty & Staff
                    </a>
                </li>

                <li class="active">
                    Reports
                </li>

                <li>
                    <a href="logout.php">
                        Logout
                    </a>
                </li>

            </ul>

        </nav>

    </aside>


    <main class="main">

        <section class="panel">

            <div class="panel-header">

                <h2>
                    Daily Report
                </h2>

            </div>


           <form method="GET">

    <label>

        Select Date

        <input
            type="date"
            name="date"
            value="<?= htmlspecialchars($selected_date) ?>"
            required
        >

    </label>

    <button type="submit">
        View Report
    </button>

    <button type="button" onclick="window.print()">
        Print Report
    </button>

</form>
        </section>


        <section class="stats">

            <div class="card">

                <div class="num">
                    <?= $total ?>
                </div>

                <div class="label">
                    Total
                </div>

            </div>


            <div class="card">

                <div class="num">
                    <?= $present ?>
                </div>

                <div class="label">
                    Present
                </div>

            </div>


            <div class="card">

                <div class="num">
                    <?= $late ?>
                </div>

                <div class="label">
                    Late
                </div>

                <div class="card">

                <div class="num">
                    <?= $absent ?>
                </div>

                <div class="label">
                    Absent
                </div>

            </div>


            <div class="card">

                <div class="num">
                    <?= $onLeave ?>
                </div>

                <div class="label">
                    On Leave
                </div>

            </div>

            </div>

        </section>


        <section class="panel">

            <div class="panel-header">

                <h2>
                    Attendance Records
                </h2>

            </div>


            <table>

                <thead>

                    <tr>

                        <th>
                            Employee ID
                        </th>

                        <th>
                            Name
                        </th>

                        <th>
                            Department
                        </th>

                        <th>
                            Time In
                        </th>

                        <th>
                            Time Out
                        </th>

                        <th>
                            Status
                        </th>

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
                           <?= htmlspecialchars($row['time_in'] ?? '—') ?>

                        <td>
                            <?= htmlspecialchars($row['time_out'] ?? '—') ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($row['status']) ?>
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