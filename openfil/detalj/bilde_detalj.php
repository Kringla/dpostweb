<?php
require_once('../../includes/bootstrap.php');
$page_title = 'Detaljer – Bilde';
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
          <dt>Fotograf</dt><dd><!-- echo $row['fotograf'] --></dd>
          <dt>År</dt><dd><!-- echo $row['år'] --></dd>
          <dt>DIMU-kode</dt><dd><!-- echo $row['dimu-kode'] --></dd>
        </dl>

        <a href="../bilder.php" class="btn">Tilbake</a>
      </div>
    </div>
  </div>
</main>
<?php include('../../includes/footer.php'); ?>
