<?php require_once __DIR__ . '/bootstrap.php'; ?>
<?php require_once __DIR__ . '/menu.php'; ?>
<?php

if (session_status() === PHP_SESSION_NONE) { session_start(); }

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

$breadcrumb_items = [];
if (isset($breadcrumbs) && is_array($breadcrumbs)) {
    foreach ($breadcrumbs as $crumb) {
        if (!is_array($crumb) || !isset($crumb['label'])) {
            continue;
        }
        $label = trim((string)$crumb['label']);
        if ($label === '') {
            continue;
        }
        $urlValue = isset($crumb['url']) && is_string($crumb['url']) ? trim($crumb['url']) : null;
        if ($urlValue === '') {
            $urlValue = null;
        }
        $breadcrumb_items[] = [
            'label' => $label,
            'url' => $urlValue,
        ];
    }
}

$homeBreadcrumb = ['label' => 'Hjem', 'url' => url('index.php')];
if (empty($breadcrumb_items) && function_exists('get_main_navigation_items')) {
    $navItems = get_main_navigation_items();
    $breadcrumb_items[] = $homeBreadcrumb;

    if ($resolved_active_nav && isset($navItems[$resolved_active_nav]) && $resolved_active_nav !== 'home') {
        $navLabel = $navItems[$resolved_active_nav]['label'];
        $navUrl = $navItems[$resolved_active_nav]['href'];
        $breadcrumb_items[] = [
            'label' => $navLabel,
            'url' => $navUrl,
        ];

        if ($page_title !== '' && strcasecmp($page_title, $navLabel) !== 0) {
            $finalLabel = $page_title;
            $pattern = '/^' . preg_quote($navLabel, '/') . '\\s*:\\s*(.+)$/u';
            if (preg_match($pattern, $page_title, $matches) === 1) {
                $trimmed = trim($matches[1]);
                if ($trimmed !== '') {
                    $finalLabel = $trimmed;
                }
            }
            $breadcrumb_items[] = ['label' => $finalLabel];
        }
    } elseif ($page_title !== '' && strcasecmp($page_title, $homeBreadcrumb['label']) !== 0) {
        $breadcrumb_items[] = ['label' => $page_title];
    }
} elseif (empty($breadcrumb_items)) {
    if ($page_title !== '') {
        $breadcrumb_items[] = $homeBreadcrumb;
        $breadcrumb_items[] = ['label' => $page_title];
    } else {
        $breadcrumb_items[] = $homeBreadcrumb;
    }
}
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
      <img src="<?= h($BASE . '/assets/img/dposten-logo.jpg') ?>"
        srcset="<?= h($BASE . '/assets/img/dposten-logo.jpg') ?> 1x,
                 <?= h($BASE . '/assets/img/dposten-logo@x2.jpg') ?> 2x"
        alt="DpostWeb" class="logo">
    </a>
  </div>
  <?php if (function_exists('render_main_menu')): ?>
    <?php render_main_menu($resolved_active_nav); ?>
  <?php endif; ?>
  <?php if (!empty($breadcrumb_items)): ?>
    <nav class="breadcrumbs" aria-label="Brødsmulesti">
      <div class="container">
        <ol class="breadcrumbs-list">
          <?php
            $lastIndex = count($breadcrumb_items) - 1;
            foreach ($breadcrumb_items as $index => $crumb):
              $isCurrent = ($index === $lastIndex);
          ?>
            <li class="breadcrumbs-item">
              <?php if (!$isCurrent && !empty($crumb['url'])): ?>
                <a href="<?= h($crumb['url']) ?>"><?= h($crumb['label']) ?></a>
              <?php else: ?>
                <span<?= $isCurrent ? ' aria-current="page"' : '' ?>><?= h($crumb['label']) ?></span>
              <?php endif; ?>
            </li>
          <?php endforeach; ?>
        </ol>
      </div>
    </nav>
  <?php endif; ?>
</header>
