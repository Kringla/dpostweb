<?php
// Global HTML-escape helper (alias h()/e())
if (!function_exists('h')) {
    function h($value): string {
        return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('e')) {
    function e($value): string {
        return h($value);
    }
}

function is_valid_id($id) {
    return ctype_digit(strval($id)) && intval($id) > 0;
}
function isAdmin() {
    if (function_exists('is_admin')) {
        return is_admin();
    }
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * URL-helper som respekterer BASE_URL.
 * Eksempel: url('user/fart_soknavn.php')
 */
function url(string $path = ''): string {
    $base = defined('BASE_URL') ? rtrim(BASE_URL, '/') : '';
    return $base . '/' . ltrim($path, '/');
}

if (!function_exists('asset')) {
    /**
     * asset('/assets/img/hero1.jpg')  ->  '/assets/img/hero1.jpg?v=1699999999'
     * Legger på mtime for cache-busting og respekterer BASE_URL.
     */
    function asset(string $path): string {
        $base = defined('BASE_URL') ? rtrim(BASE_URL, '/') : '';
        $rel  = '/' . ltrim($path, '/');
        $fs   = rtrim(__DIR__ . '/..', '/') . $rel; // prosjektrot = includes/..
        $v    = @filemtime($fs);
        return $base . $rel . ($v ? ('?v=' . $v) : '');
    }
}

if (!function_exists('stmt_fetch_all_assoc')) {
    /**
     * Fetch all rows from a prepared statement as associative arrays.
     * Works even when mysqlnd (and mysqli_stmt::get_result) is unavailable.
     */
    function stmt_fetch_all_assoc(mysqli_stmt $stmt): array {
        if (method_exists($stmt, 'get_result')) {
            $result = $stmt->get_result();
            if ($result instanceof mysqli_result) {
                $rows = $result->fetch_all(MYSQLI_ASSOC);
                $result->free();
                return $rows;
            }
            return [];
        }

        $rows = [];
        if (!$stmt->store_result()) {
            return $rows;
        }

        $meta = $stmt->result_metadata();
        if (!$meta) {
            $stmt->free_result();
            return $rows;
        }

        $fields = $meta->fetch_fields();
        $row = [];
        $bindParams = [];
        foreach ($fields as $field) {
            $row[$field->name] = null;
            $bindParams[] = &$row[$field->name];
        }

        if ($bindParams) {
            call_user_func_array([$stmt, 'bind_result'], $bindParams);
            while ($stmt->fetch()) {
                $rowCopy = [];
                foreach ($row as $key => $value) {
                    $rowCopy[$key] = $value;
                }
                $rows[] = $rowCopy;
            }
        }

        $stmt->free_result();
        return $rows;
    }
}

if (!function_exists('stmt_fetch_assoc')) {
    /**
     * Fetch the first row from a prepared statement as an associative array.
     */
    function stmt_fetch_assoc(mysqli_stmt $stmt): ?array {
        $rows = stmt_fetch_all_assoc($stmt);
        return $rows[0] ?? null;
    }
}
