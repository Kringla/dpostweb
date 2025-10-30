<?php
// includes/auth.php
if (session_status() === PHP_SESSION_NONE) session_start();

function is_logged_in(): bool {
    return isset($_SESSION['user_id']);
}

function current_role(): string {
    return $_SESSION['user_role'] ?? '';
}

function is_admin(): bool {
    if (!is_logged_in()) return false;
    $role = current_role();
    return in_array($role, ['admin','super'], true);
}

function is_super(): bool {
    if (!is_logged_in()) return false;
    return current_role() === 'super';
}

function require_login(): void {
    if (!is_logged_in()) {
        $next = urlencode($_SERVER['REQUEST_URI'] ?? '/');
        $base = defined('BASE_URL') ? rtrim(BASE_URL, '/') : '';
        header('Location: ' . $base . '/login.php?next=' . $next);
        exit;
    }
}

function require_admin(): void {
    if (!is_admin()) {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $base = defined('BASE_URL') ? rtrim(BASE_URL, '/') : '';
        $next = $_SERVER['REQUEST_URI'] ?? '/admin/';

        // Normaliser next til en path uten domenedel
        $nextPath = parse_url($next, PHP_URL_PATH) ?? '/';
        $nextQuery = parse_url($next, PHP_URL_QUERY) ?? '';
        if ($base !== '' && !str_starts_with($nextPath, $base . '/')) {
            $nextPath = $base . '/' . ltrim($nextPath, '/');
        }
        $nextFinal = $nextPath . ($nextQuery ? ('?' . $nextQuery) : '');

        header('Location: ' . $base . '/login.php?next=' . urlencode($nextFinal));
        exit;
    }
}

function logout(): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}
