<?php
require __DIR__ . '/config.php';

$errors  = [];
$success = null;
$name = $email = $title = $description = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name'] ?? '');
    $email       = trim($_POST['email'] ?? '');
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');

    // --- Validate required fields ---
    if ($name === '')        $errors[] = 'Full name is required.';
    if ($email === '')       $errors[] = 'Email is required.';
    if ($title === '')       $errors[] = 'Ticket title is required.';
    if ($description === '') $errors[] = 'Ticket description is required.';

    // --- Validate email format ---
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }

    // --- Save the ticket if everything passed ---
    if (empty($errors)) {
        $tickets  = load_tickets();
        $ticketId = generate_ticket_id();

        $tickets[] = [
            'ticket_id'   => $ticketId,
            'name'        => $name,
            'email'       => $email,
            'title'       => $title,
            'description' => $description,
            'status'      => 'Open',
        ];

        save_tickets($tickets);

        $success = "Ticket submitted successfully. Your ticket ID is {$ticketId}.";
        $name = $email = $title = $description = ''; // clear the form
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit a Support Ticket</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 40px auto; padding: 0 16px; }
        label { display: block; margin-top: 12px; font-weight: bold; }
        input, textarea { width: 100%; padding: 8px; box-sizing: border-box; }
        button { margin-top: 16px; padding: 10px 20px; cursor: pointer; }
        .error { color: #b00; margin: 4px 0; }
        .success { color: #070; margin: 4px 0; }
    </style>
</head>
<body>
    <h1>Submit a Support Ticket</h1>

    <?php if ($success): ?>
        <p class="success"><?= e($success) ?></p>
    <?php endif; ?>

    <?php foreach ($errors as $error): ?>
        <p class="error"><?= e($error) ?></p>
    <?php endforeach; ?>

    <form method="POST">
        <label>Full Name</label>
        <input type="text" name="name" value="<?= e($name) ?>">

        <label>Email</label>
        <input type="text" name="email" value="<?= e($email) ?>">

        <label>Ticket Title</label>
        <input type="text" name="title" value="<?= e($title) ?>">

        <label>Ticket Description</label>
        <textarea name="description" rows="5"><?= e($description) ?></textarea>

        <button type="submit">Submit Ticket</button>
    </form>

    <p><a href="login.php">Admin Login</a></p>
</body>
</html>
