<?php
require_once('../../includes/bootstrap.php');
$page_title = 'Detaljer – Forfatter';
include('../../includes/header.php');
?>
<main class="page-main">
  <div class="container">
    <div class="card">
      <div class="card-content">
        <h1><?php echo h($page_title); ?></h1>

        <!-- TODO: Hent ID via $_GET['id'] og slå opp i databasen -->
        <dl class="detail-list">
          <dt>Navn</dt><dd><!-- echo $row['navn'] --></dd>
          <dt>Artikler</dt><dd><!-- echo $row['artikler'] --></dd>
          <dt>Første år</dt><dd><!-- echo $row['første år'] --></dd>
        </dl>

        <a href="../forfattere.php" class="btn">Tilbake</a>
      </div>
    </div>
  </div>
</main>
<?php include('../../includes/footer.php'); ?>
