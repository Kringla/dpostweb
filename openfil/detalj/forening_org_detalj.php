<?php
require_once('../../includes/bootstrap.php');
$page_title = 'Detaljer – Forening/org';
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
          <dt>Lokasjon</dt><dd><!-- echo $row['lokasjon'] --></dd>
          <dt>Antall omtaler</dt><dd><!-- echo $row['antall omtaler'] --></dd>
        </dl>

        <a href="../forening_org.php" class="btn">Tilbake</a>
      </div>
    </div>
  </div>
</main>
<?php include('../../includes/footer.php'); ?>
