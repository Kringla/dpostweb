<?php
require_once __DIR__ . '/includes/bootstrap.php';

if (!function_exists('h')) {
    function h($s) {
        return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
    }
}

$db = db();
$count = (int)$db->query('SELECT COUNT(*) FROM xuser')->fetch_row()[0];

$base = defined('BASE_URL') ? rtrim(BASE_URL, '/') : '';
$loginUrl = $base === '' ? '/login.php' : $base . '/login.php';

if ($count > 0) {
    header('Location: ' . $loginUrl);
    exit;
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim((string)$_POST['email']) : '';
    $passwordPlain = $_POST['password'] ?? '';

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Oppgi en gyldig e-postadresse.';
    } elseif ($passwordPlain === '') {
        $error = 'Oppgi et passord.';
    } else {
        $passwordHash = password_hash($passwordPlain, PASSWORD_DEFAULT);
        $role = 'admin';

        try {
            with_rw(function(\mysqli $rw) use ($email, $passwordHash, $role) {
                $stmt = $rw->prepare('INSERT INTO xuser (email, password, role) VALUES (?, ?, ?)');
                $stmt->bind_param('sss', $email, $passwordHash, $role);
                $stmt->execute();
                $stmt->close();
            });

            header('Location: ' . $loginUrl);
            exit;
        } catch (\mysqli_sql_exception $e) {
            if ((int)$e->getCode() === 1062) {
                $error = 'E-postadressen er allerede registrert.';
            } else {
                $error = 'Teknisk feil ved registrering av brukeren.';
            }
        }
    }
}
?>

<form method="post" action="">
    <?php if ($error !== ''): ?>
        <p><?= h($error) ?></p>
    <?php endif; ?>
    <input type="email" name="email" required placeholder="E-post" class="input" value="<?= h($email) ?>">
    <input type="password" name="password" required placeholder="Passord" class="input">
    <button type="submit" class="btn primary">Registrer administrator</button>
</form>
