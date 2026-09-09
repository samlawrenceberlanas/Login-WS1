<?php

include "auth.php";
include "config.php";

if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$search = $_GET['search'] ?? '';
$date = $_GET['date'] ?? '';
$department = $_GET['department'] ?? '';

$search = $_GET['search'] ?? '';
$date = $_GET['date'] ?? '';
$department = $_GET['department'] ?? '';

$message = $_GET['message'] ?? '';
$error = $_GET['error'] ?? '';

$sql = "SELECT * FROM attendance_logs WHERE 1=1";

$params = [];
$types = "";

/* Search */
if ($search !== '') {

    $sql .= " AND (
        full_name LIKE ?
        OR employee_id LIKE ?
        OR department LIKE ?
        OR status LIKE ?
        OR attendance_date LIKE ?
    )";

    $searchTerm = "%$search%";

    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;

    $types .= "sssss";
}

/* Date filter */
if ($date !== '') {

    $sql .= " AND attendance_date = ?";

    $params[] = $date;
    $types .= "s";
}

/* Department filter */
if ($department !== '') {

    $sql .= " AND department = ?";

    $params[] = $department;
    $types .= "s";
}

$sql .= " ORDER BY attendance_date DESC, time_in DESC";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();

$result = $stmt->get_result();

$totalLogs = $conn->query(
    "SELECT COUNT(*) AS total FROM attendance_logs"
)->fetch_assoc()['total'];

$present = $conn->query(
    "SELECT COUNT(*) AS total FROM attendance_logs WHERE status = 'Present'"
)->fetch_assoc()['total'];

$late = $conn->query(
    "SELECT COUNT(*) AS total FROM attendance_logs WHERE status = 'Late'"
)->fetch_assoc()['total'];

$today = date('Y-m-d');

$todayCount = $conn->query(
    "SELECT COUNT(*) AS total
     FROM attendance_logs
     WHERE attendance_date = '$today'"
)->fetch_assoc()['total'];

$insideCount = $conn->query(
    "SELECT COUNT(*) AS total
     FROM attendance_logs
     WHERE attendance_date = '$today'
     AND time_in IS NOT NULL
     AND time_out IS NULL"
)->fetch_assoc()['total'];
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <header class="topbar">
        <div>
            <p class="eyebrow">Office System</p>
            <h1>Admin Dashboard</h1>
        </div>
        <div class="top-actions">
           <form method="GET" action="admin.php" class="search-form">

    <input
        type="text"
        name="search"
        id="search"
        placeholder="Search faculty or staff..."
        value="<?= htmlspecialchars($search) ?>"
    >

    <input
        type="date"
        name="date"
        value="<?= htmlspecialchars($_GET['date'] ?? '') ?>"
    >

    <select name="department">

        <option value="">All Departments</option>

        <option value="IT Department"
            <?= (($_GET['department'] ?? '') === 'IT Department') ? 'selected' : '' ?>>
            IT Department
        </option>

        <option value="English Department"
            <?= (($_GET['department'] ?? '') === 'English Department') ? 'selected' : '' ?>>
            English Department
        </option>

        <option value="Science Department"
            <?= (($_GET['department'] ?? '') === 'Science Department') ? 'selected' : '' ?>>
            Science Department
        </option>

        <option value="Math Department"
            <?= (($_GET['department'] ?? '') === 'Math Department') ? 'selected' : '' ?>>
            Math Department
        </option>

    </select>

    <button type="submit">
        Filter
    </button>

</form>
            <button id="add-log-btn">+ Add Entry</button>
        </div>
    </header>

    <div class="wrap">
       <aside class="sidebar">
    <nav>
        <ul>

            <li class="active">
                <a href="admin.php">Dashboard</a>
            </li>

            <li>
                <a href="admin.php">Log Book</a>
            </li>

            <li>
                <a href="employees.php">Faculty & Staff</a>
            </li>

            <li>
                <a href="reports.php">Reports</a>
            </li>

            <li>
                <a href="logout.php">Logout</a>
            </li>

        </ul>
    </nav>
</aside>

        <main class="main">
            <?php if ($message === 'attendance_saved'): ?>

    <div class="alert success">
        Attendance recorded successfully!
    </div>

<?php endif; ?>


<?php if ($error === 'already_recorded'): ?>

    <div class="alert error">
        This employee already has an attendance record for this date.
    </div>

<?php endif; ?>
            <section class="stats">
                <div class="card">
                    <div class="num"><?= $totalLogs ?></div>
                    <div class="label">Total Logs</div>
                </div>
                <div class="card">
                    <div class="num"><?= $present ?></div>
                    <div class="label">Present</div>
                </div>
                <div class="card">
                    <div class="num"><?= $late ?></div>
                    <div class="label">Late</div>
                </div>
                <div class="card">
                    <div class="num"><?= $todayCount ?></div>
                    <div class="label">Today</div>
                </div>
                <div class="card">

                <div class="num">
                    <?= $insideCount ?>
                </div>

                <div class="label">
                    Currently Inside
                </div>

            </div>
            </section>

            <section class="panel">

    <div class="panel-header">
        <h2>Faculty & Staff Attendance</h2>

<p>
    Enter the Employee ID to record attendance.
</p>

        <div class="time-actions">

            <!-- TIME IN -->
            <form action="time_in.php" method="POST">
                <input
                    type="text"
                    name="employee_id"
                    placeholder="Employee ID"
                    required
                >

                <button type="submit">
                    Time In
                </button>
            </form>

            <!-- TIME OUT -->
            <form action="time_out.php" method="POST">
                <input
                    type="text"
                    name="employee_id"
                    placeholder="Employee ID"
                    required
                >

                <button type="submit">
                    Time Out
                </button>
            </form>

        </div>
    </div>

    <section class="panel">

    <div class="panel-header">
        <h2>Record Absence / Leave</h2>
    </div>

    <form action="save_status.php" method="POST">

        <label>
            Employee ID

            <input
                type="text"
                name="employee_id"
                placeholder="Example: FAC-001"
                required
            >
        </label>

        <label>
            Status

            <select name="status" required>

                <option value="Absent">
                    Absent
                </option>

                <option value="On Leave">
                    On Leave
                </option>

            </select>

        </label>

        <label>
            Date

            <input
                type="date"
                name="attendance_date"
                value="<?= date('Y-m-d') ?>"
                required
            >
        </label>

        <label>
            Remarks

            <textarea
                name="remarks"
                placeholder="Optional remarks"
            ></textarea>
        </label>

        <button type="submit" class="primary-btn">
            Save Attendance Status
        </button>

    </form>

</section>
                <table id="log-table">
                    <thead>
                        <tr>
                            <th>Employee ID</th>
                            <th>Faculty / Staff</th>
                            <th>Department</th>
                            <th>Date</th>
                            <th>Time In</th>
                            <th>Time Out</th>
                            <th>Status</th>
                            <th>Remarks</th>
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
        <?= htmlspecialchars($row['attendance_date']) ?>
    </td>

    <td>
        <?= htmlspecialchars($row['time_in'] ?? '—') ?>
    </td>

    <td>
        <?= htmlspecialchars($row['time_out'] ?? '—') ?>
    </td>

    <td>
        <span class="status">
            <?= htmlspecialchars($row['status']) ?>
        </span>
    </td>

    <td>
        <?= htmlspecialchars($row['remarks'] ?? '—') ?>
    </td>

    <td>
        <a
            href="delete_attendance.php?id=<?= $row['id'] ?>"
            onclick="return confirm('Are you sure you want to delete this attendance record?')"
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

    <div class="modal" id="log-modal" aria-hidden="true">
        <div class="modal-content">
            <h3>Add Attendance Log</h3>
            <form action="save_attendance.php" method="POST" id="log-form">
                <label>
    Employee ID
    <input type="text" name="employee_id" required>
</label>

<label>
    Faculty/Staff Name
    <input type="text" name="full_name" required>
</label>

<label>
    Department
    <input type="text" name="department" required>
</label>

<label>
    Date
    <input type="date" name="attendance_date" required>
</label>

<label>
    Time In
    <input type="time" name="time_in" required>
</label>

<label>
    Time Out
    <input type="time" name="time_out">
</label>

<label>
    Status
    <select name="status" required>
        <option value="Present">Present</option>
        <option value="Late">Late</option>
        <option value="Absent">Absent</option>
        <option value="On Leave">On Leave</option>
    </select>
</label>

<label>
    Remarks
    <textarea name="remarks"></textarea>
</label>

<div class="modal-actions">
    <button type="submit" class="primary-btn">
        Record Attendance
    </button>

    <button type="button" id="modal-cancel" class="secondary-btn">
        Cancel
    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="admin.js"></script>
</body>
</html>
