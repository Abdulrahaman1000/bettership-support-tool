<?php
session_start();
require __DIR__ . '/config.php';

// Already logged in? Skip straight to the dashboard.
if (!empty($_SESSION['admin_logged_in'])) {
    header('Location: dashboard.php');
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Email must match AND the password must verify against the stored hash.
    if ($email === ADMIN_EMAIL && password_verify($password, ADMIN_PASSWORD_HASH)) {
        session_regenerate_id(true); // prevent session fixation
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_email']     = $email;
        header('Location: dashboard.php');
        exit;
    }

    $error = 'Invalid email or password.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 400px; margin: 40px auto; padding: 0 16px; }
        label { display: block; margin-top: 12px; font-weight: bold; }
        input { width: 100%; padding: 8px; box-sizing: border-box; }
        button { margin-top: 16px; padding: 10px 20px; cursor: pointer; }
        .error { color: #b00; margin: 4px 0; }
    </style>
</head>
<body>
    <h1>Admin Login</h1>

    <?php if ($error): ?>
        <p class="error"><?= e($error) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Email</label>
        <input type="text" name="email">

        <label>Password</label>
        <input type="password" name="password">

        <button type="submit">Log In</button>
    </form>

    <p><a href="index.php">&larr; Back to ticket form</a></p>
</body>
</html>
