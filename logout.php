<?php
session_start();

// Clear all session data and destroy the session.
$_SESSION = [];
session_destroy();

// Send the user back to the login page.
header('Location: login.php');
exit;
