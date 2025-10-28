<?php
require_once('../../includes/bootstrap.php');
$page_title = 'Detaljer – Person';
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
          <dt>Rolle</dt><dd><!-- echo $row['rolle'] --></dd>
          <dt>Antall artikler</dt><dd><!-- echo $row['antall artikler'] --></dd>
        </dl>

        <a href="../personer.php" class="btn">Tilbake</a>
      </div>
    </div>
  </div>
</main>
<?php include('../../includes/footer.php'); ?>
