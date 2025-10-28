<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// index.php - Landingsside for DpostWeb
$page_title = 'DpostWeb';
$page_class = 'home';
$bodyClass  = 'home';

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$BASE = defined('BASE_URL') ? rtrim(BASE_URL, '/') : '';

include __DIR__ . '/includes/header.php';
// Ikke vis fanemeny på landingssiden foreløpig
?>

<main class="home-main" aria-labelledby="home-title">
  <div class="container">
    <h1 id="home-title"><?php echo e('Velkommen til DpostWeb'); ?></h1>
    <p class="lead"><?php echo e('Brukervennlig oversikt over Dampskibspostens innhold'); ?></p>

    <div class="home-grid" role="navigation" aria-label="Hurtigvalg">
      <a class="tile blue" href="<?php echo $BASE; ?>/login.php">Innlogging</a>
      <a class="tile admin" href="<?php echo $BASE; ?>/protfil/login.php">Administrasjon</a>
    </div>
  </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
