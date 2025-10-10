<?php
// includes/menu.php
// Minimal, trygg meny iht. CR Faner:
//  - "Hjem" -> /index.php (skjules når man er på index.php)
//  - "Administrasjon" -> /login.php
//  - Ingen "Logg inn" og ingen "Logg ut"
//  - Bruker BASE_URL og h() (forutsatt definert i bootstrap/header)

if (!defined('BASE_URL')) {
    // Hvis filen skulle bli brukt uten bootstrap/header, fall tilbake til rot
    define('BASE_URL', '');
}

// Finn gjeldende scriptnavn uten sti
$current = basename($_SERVER['SCRIPT_NAME'] ?? '');

// Enkel helper: hvis h() ikke finnes (ekstremt tilfelle), lag en ufarlig fallback
if (!function_exists('h')) {
    function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
}
// Ikke render meny på landingssiden
if ($current === 'index.php') { return; }
?>
<nav class="nav tabs" aria-label="Hovedmeny">
    <ul class="tabs-list">
        
        <?php
          // Visningsregler:
          // - Innlogget: kun "Hjem"
          // - På index.php (ikke innlogget): kun "Administrasjon"
          // - Ellers: begge
          $loggedIn = isset($_SESSION) && !empty($_SESSION['user_id']);
          $isIndex  = ($current === 'index.php');
          
          $homeHref  = BASE_URL . '/index.php';
          $adminHref = BASE_URL . '/login.php?next=/admin/'; // gå til admin-hjem etter login
          
          $showHome  = false;
          $showAdmin = false;
          
          if ($loggedIn) {
              $showHome = true;
          } elseif ($isIndex) {
              $showAdmin = true;
          } else {
              $showHome = true;
              $showAdmin = true;
          }
        
          // Force-hide Admin tab on admin/* pages after login to avoid duplicate self-link and UX confusion
          $req = $_SERVER['REQUEST_URI'] ?? '';
          $inAdmin = (strpos($req, '/admin/') !== false);
          if ($inAdmin) {
              $showAdmin = false; // show only Hjem in admin section
              $showHome  = true;
          }
?>
        
        <?php if ($showHome): ?>
            <li class="tab-item">
                <a class="tab-link<?= $current === 'index.php' ? ' is-active' : '' ?>"
                   href="<?= $homeHref ?>"
                   <?= $current === 'index.php' ? 'aria-current=\"page\"' : '' ?>
                ><?= h('Hjem') ?></a>
            </li>
        <?php endif; ?>
        
        <?php if ($showAdmin): ?>
            <li class="tab-item">
                <a class="tab-link<?= $current === 'login.php' ? ' is-active' : '' ?>"
                   href="<?= $adminHref ?>"
                   <?= $current === 'login.php' ? 'aria-current=\"page\"' : '' ?>
                ><?= h('Administrasjon') ?></a>
            </li>
        <?php endif; ?>
    </ul>
</nav>
