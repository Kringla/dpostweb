<?php
require_once('../../includes/bootstrap.php');
$page_title = 'Detaljer – Verft';
include('../../includes/header.php');
?>
<main class="page-main">
  <div class="container">
    <div class="card">
      <div class="card-content">
        <h1><?php echo h($page_title); ?></h1>

        <!-- TODO: Hent ID via $_GET['id'] og slå opp i databasen -->
        <dl class="detail-list">
          <dt>Verft</dt><dd><!-- echo $row['verft'] --></dd>
          <dt>Land</dt><dd><!-- echo $row['land'] --></dd>
          <dt>Antall fartøy</dt><dd><!-- echo $row['antall fartøy'] --></dd>
        </dl>

        <a href="../verft.php" class="btn">Tilbake</a>
      </div>
    </div>
  </div>
</main>
<?php include('../../includes/footer.php'); ?>
