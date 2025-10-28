<?php
require_once('../../includes/bootstrap.php');
$page_title = 'Detaljer – Artikkel';
include('../../includes/header.php');
?>
<main class="page-main">
  <div class="container">
    <div class="card">
      <div class="card-content">
        <h1><?php echo h($page_title); ?></h1>

        <!-- TODO: Hent ID via $_GET['id'] og slå opp i databasen -->
        <dl class="detail-list">
          <dt>Tittel</dt><dd><!-- echo $row['tittel'] --></dd>
          <dt>Forfatter</dt><dd><!-- echo $row['forfatter'] --></dd>
          <dt>År</dt><dd><!-- echo $row['år'] --></dd>
          <dt>Type</dt><dd><!-- echo $row['type'] --></dd>
        </dl>

        <a href="../artikler.php" class="btn">Tilbake</a>
      </div>
    </div>
  </div>
</main>
<?php include('../../includes/footer.php'); ?>
