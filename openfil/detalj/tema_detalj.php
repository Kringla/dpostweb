<?php
require_once('../../includes/bootstrap.php');
$page_title = 'Detaljer – Tema';
include('../../includes/header.php');
?>
<main class="page-main">
  <div class="container">
    <div class="card">
      <div class="card-content">
        <h1><?php echo h($page_title); ?></h1>

        <!-- TODO: Hent ID via $_GET['id'] og slå opp i databasen -->
        <dl class="detail-list">
          <dt>Tema</dt><dd><!-- echo $row['tema'] --></dd>
          <dt>Artikler</dt><dd><!-- echo $row['artikler'] --></dd>
          <dt>Bilder</dt><dd><!-- echo $row['bilder'] --></dd>
        </dl>

        <a href="../tema_liste.php" class="btn">Tilbake</a>
      </div>
    </div>
  </div>
</main>
<?php include('../../includes/footer.php'); ?>
