<?php
require_once('../../includes/bootstrap.php');
$page_title = 'Detaljer – Fartøy';
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
          <dt>Type</dt><dd><!-- echo $row['type'] --></dd>
          <dt>Byggeår</dt><dd><!-- echo $row['byggeår'] --></dd>
          <dt>Rederi</dt><dd><!-- echo $row['rederi'] --></dd>
        </dl>

        <a href="../fartoyer.php" class="btn">Tilbake</a>
      </div>
    </div>
  </div>
</main>
<?php include('../../includes/footer.php'); ?>
