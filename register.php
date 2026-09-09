<?php

session_start();
include "config.php";

$errors = [];
$values = ['username'=>'','email'=>'','phone'=>''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    $values['username'] = $username;
    $values['email'] = $email;
    $values['phone'] = $phone;

    if ($username === '') $errors[] = 'Username is required.';
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
    if ($password === '') $errors[] = 'Password is required.';
    if ($password !== $confirm) $errors[] = 'Passwords do not match.';

    if (empty($errors)) {
        // Check duplicate username/email
        $stmt = $conn->prepare('SELECT id FROM users WHERE email = ? OR username = ? LIMIT 1');
        $stmt->bind_param('ss', $email, $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = 'Username or email already exists.';
        }
        $stmt->close();
    }

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);

$role = "hr";

$stmt = $conn->prepare(
    'INSERT INTO users (username, email, phone, password_hash, role)
     VALUES (?, ?, ?, ?, ?)'
);

$stmt->bind_param(
    "sssss",
    $username,
    $email,
    $phone,
    $hash,
    $role
);

if ($stmt->execute()) {
    header('Location: login.php?registered=1');
    exit;
} else {
    $errors[] = 'Registration failed — please try again.';
}

$stmt->close();
    }
}

function esc($v){ return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="registration.css">
    <title>Register</title>
    <style>
        .errors{background:#ffecec;border:1px solid #f5c2c2;padding:10px;margin-bottom:12px;border-radius:6px}
    </style>
</head>
<body>
    <section class="registration-card">
        <h1>Register</h1>

        <?php if (!empty($errors)): ?>
            <div class="errors">
                <ul>
                <?php foreach($errors as $e): ?><li><?=esc($e)?></li><?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?=esc($values['username'])?>" required><br><br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?=esc($values['email'])?>" required><br><br>

            <label for="phone">Phone Number:</label>
            <input type="tel" id="phone" name="phone" value="<?=esc($values['phone'])?>"><br><br>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required><br><br>

            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required><br><br>

            <button type="submit">Register</button>
            <p>Already have an account?<a href="login.php"> Login here.</a></p>
        </form>
    </section>
</body>
</html>
