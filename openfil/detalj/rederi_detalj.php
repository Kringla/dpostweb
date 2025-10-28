<?php
require_once('../../includes/bootstrap.php');
$page_title = 'Detaljer – Rederi';
include('../../includes/header.php');
?>
<main class="page-main">
  <div class="container">
    <div class="card">
      <div class="card-content">
        <h1><?php echo h($page_title); ?></h1>

        <!-- TODO: Hent ID via $_GET['id'] og slå opp i databasen -->
        <dl class="detail-list">
          <dt>Rederi</dt><dd><!-- echo $row['rederi'] --></dd>
          <dt>Hjemsted</dt><dd><!-- echo $row['hjemsted'] --></dd>
          <dt>Antall fartøy</dt><dd><!-- echo $row['antall fartøy'] --></dd>
        </dl>

        <a href="../rederier.php" class="btn">Tilbake</a>
      </div>
    </div>
  </div>
</main>
<?php include('../../includes/footer.php'); ?>
