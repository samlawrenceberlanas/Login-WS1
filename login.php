<?php

session_start();
include "config.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {

        $error = "Please enter your username and password.";

    } else {

        $stmt = $conn->prepare(
    "SELECT id, username, password_hash, role, employee_id
FROM users
 WHERE username = ?
 LIMIT 1"

        );

        $stmt->bind_param("s", $username);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 1) {

            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password_hash'])) {
                

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['employee_id'] = $user['employee_id'];

                if ($user['role'] === 'admin') {

                    header("Location: admin.php");

                } elseif ($user['role'] === 'hr') {

                    header("Location: hr_dashboard.php");

                } else {

                    header("Location: faculty_dashboard.php");
                }

                exit();

            } else {

                $error = "Invalid username or password.";

            }

        } else {

            $error = "Invalid username or password.";

        }

        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="login.css">

    <title>Login</title>

</head>

<body>

<section class="login-card">

    <img src="mobiuslogo.png" alt="Logo">

    <h1>Faculty & Staff Logbook</h1>

    <?php if ($error): ?>

        <p style="color: red;">
            <?= htmlspecialchars($error) ?>
        </p>

    <?php endif; ?>

    <form action="login.php" method="POST">

        <label for="username">
            Username:
        </label>

        <input
            type="text"
            id="username"
            name="username"
            required
        >

        <br><br>

        <label for="password">
            Password:
        </label>

        <input
            type="password"
            id="password"
            name="password"
            required
        >

        <br><br>

        <button type="submit">
            Login
        </button>

        <p>
            Don't have an account?
            <a href="register.php">
                Register here.
            </a>
        </p>

    </form>

</section>

</body>

</html>