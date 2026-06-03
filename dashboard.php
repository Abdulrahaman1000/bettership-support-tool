<?php
session_start();
require __DIR__ . '/config.php';

require_admin(); // redirect to login if not authenticated

// Handle "Mark as Resolved" submissions.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['resolve_id'])) {
    $tickets = load_tickets();

    foreach ($tickets as &$ticket) {
        if ($ticket['ticket_id'] === $_POST['resolve_id']) {
            $ticket['status'] = 'Resolved';
            break;
        }
    }
    unset($ticket); // break the reference from the loop

    save_tickets($tickets);

    // Redirect-after-POST so a refresh doesn't resubmit the action.
    header('Location: dashboard.php');
    exit;
}

$tickets = load_tickets();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 950px; margin: 40px auto; padding: 0 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; vertical-align: top; }
        th { background: #f4f4f4; }
        .open { color: #b06f00; font-weight: bold; }
        .resolved { color: #070; font-weight: bold; }
        .topbar { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px; }
        button { cursor: pointer; padding: 6px 12px; }
    </style>
</head>
<body>
    <div class="topbar">
        <h1>Support Tickets</h1>
        <span><?= e($_SESSION['admin_email']) ?> &middot; <a href="logout.php">Logout</a></span>
    </div>

    <?php if (empty($tickets)): ?>
        <p>No tickets submitted yet.</p>
    <?php else: ?>
        <table>
            <tr>
                <th>Ticket ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Title</th>
                <th>Description</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php foreach ($tickets as $ticket): ?>
                <tr>
                    <td><?= e($ticket['ticket_id']) ?></td>
                    <td><?= e($ticket['name']) ?></td>
                    <td><?= e($ticket['email']) ?></td>
                    <td><?= e($ticket['title']) ?></td>
                    <td><?= e($ticket['description']) ?></td>
                    <td class="<?= $ticket['status'] === 'Resolved' ? 'resolved' : 'open' ?>">
                        <?= e($ticket['status']) ?>
                    </td>
                    <td>
                        <?php if ($ticket['status'] !== 'Resolved'): ?>
                            <form method="POST" style="margin:0">
                                <input type="hidden" name="resolve_id" value="<?= e($ticket['ticket_id']) ?>">
                                <button type="submit">Mark Resolved</button>
                            </form>
                        <?php else: ?>
                            &mdash;
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</body>
</html>
