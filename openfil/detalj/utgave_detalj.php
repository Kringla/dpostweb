<?php
require_once('../../includes/bootstrap.php');
$page_title = 'Detaljer – Utgave';
include('../../includes/header.php');
?>
<main class="page-main">
  <div class="container">
    <div class="card">
      <div class="card-content">
        <h1><?php echo h($page_title); ?></h1>

        <!-- TODO: Hent ID via $_GET['id'] og slå opp i databasen -->
        <dl class="detail-list">
          <dt>År</dt><dd><!-- echo $row['år'] --></dd>
          <dt>Nummer</dt><dd><!-- echo $row['nummer'] --></dd>
          <dt>Antall artikler</dt><dd><!-- echo $row['antall artikler'] --></dd>
          <dt>Antall bilder</dt><dd><!-- echo $row['antall bilder'] --></dd>
        </dl>

        <a href="../utgaver.php" class="btn">Tilbake</a>
      </div>
    </div>
  </div>
</main>
<?php include('../../includes/footer.php'); ?>
