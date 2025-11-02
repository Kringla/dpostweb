<?php require_once __DIR__ . '/bootstrap.php'; ?>
<?php require_once __DIR__ . '/menu.php'; ?>
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$role_class = 'role-guest';
if (!empty($_SESSION['user_role'])) {
    $role_class = ($_SESSION['user_role'] === 'admin') ? 'role-admin' : 'role-user';
}

$page_class = isset($page_class) && is_string($page_class) ? $page_class : 'page';
$body_class = trim($role_class . ' ' . $page_class);
$page_title = isset($page_title) && is_string($page_title) ? $page_title : 'DpostWeb';

$active_nav = isset($activeNav) && is_string($activeNav) ? $activeNav : null;
$resolved_active_nav = $active_nav;
if (function_exists('determine_active_nav')) {
    $resolved_active_nav = determine_active_nav($active_nav);
}

$BASE = defined('BASE_URL') ? rtrim(BASE_URL, '/') : '';

$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$scriptName = str_replace('\\', '/', $scriptName);
$scriptBasename = strtolower(basename($scriptName));
$is_home_page = ($scriptBasename === 'index.php');
$should_render_nav = function_exists('render_main_menu') && !$is_home_page;
$should_show_logo = !$is_home_page;
?>
<!doctype html>
<html lang="no">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= h($page_title) ?></title>
  <link rel="stylesheet" href="<?= h($BASE . '/css/app.css') ?>">
</head>
<body<?= $body_class !== '' ? ' class="' . h($body_class) . '"' : '' ?>>

<header class="site-header">
  <div class="container site-header__inner">
    <a class="brand" href="<?= h($BASE . '/') ?>">
      <?php if ($should_show_logo): ?>
        <img src="<?= h($BASE . '/assets/img/dposten-logo.jpg') ?>"
          srcset="<?= h($BASE . '/assets/img/dposten-logo.jpg') ?> 1x,
                   <?= h($BASE . '/assets/img/dposten-logo@x2.jpg') ?> 2x"
          alt="DpostWeb" class="logo">
      <?php else: ?>
        <span class="brand__text">DpostWeb</span>
      <?php endif; ?>
    </a>
  </div>
  <?php if ($should_render_nav): ?>
    <?php render_main_menu($resolved_active_nav); ?>
  <?php endif; ?>
</header>
