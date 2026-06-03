<?php

/**
 * Shared configuration and helper functions.
 * Included by every page so logic is defined once, not duplicated.
 */

define('TICKETS_FILE', __DIR__ . '/tickets.json');

// --- Hardcoded admin credentials (demo) ---
// The task allows hardcoded credentials. The password is hashed with
// password_hash() and only ever checked with password_verify().
// In production this hash would live in an environment variable or database,
// computed once, so the plaintext never appears in source control.
define('ADMIN_EMAIL', 'admin@bettership.com');
define('ADMIN_PASSWORD_HASH', password_hash('Admin@123', PASSWORD_DEFAULT));

/**
 * Load all tickets from JSON. Returns an empty array if the file
 * does not exist yet or contains invalid data.
 */
function load_tickets(): array
{
    if (!file_exists(TICKETS_FILE)) {
        return [];
    }
    $data = json_decode(file_get_contents(TICKETS_FILE), true);
    return is_array($data) ? $data : [];
}

/**
 * Persist the full tickets array back to JSON.
 * LOCK_EX prevents file corruption if two writes overlap.
 */
function save_tickets(array $tickets): void
{
    file_put_contents(
        TICKETS_FILE,
        json_encode($tickets, JSON_PRETTY_PRINT),
        LOCK_EX
    );
}

/** Generate a unique, readable ticket ID, e.g. TKT-1A2B3C4D. */
function generate_ticket_id(): string
{
    return 'TKT-' . strtoupper(bin2hex(random_bytes(4)));
}

/** Guard a page: redirect to login unless an admin session exists. */
function require_admin(): void
{
    if (empty($_SESSION['admin_logged_in'])) {
        header('Location: login.php');
        exit;
    }
}

/** Escape output to prevent XSS. Use on every value rendered to HTML. */
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
