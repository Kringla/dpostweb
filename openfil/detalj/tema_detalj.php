<?php
require_once('../../includes/bootstrap.php');

if (!isset($_GET['id']) || !is_valid_id($_GET['id'])) {
    http_response_code(400);
    $page_title = 'Ugyldig foresporsel';
    include('../../includes/header.php');
    ?>
    <main class="page-main">
      <div class="container">
        <div class="card">
          <div class="card-content">
            <h1 class="page-title"><?= h($page_title) ?></h1>
            <p>Forespørselen mangler et gyldig tema-id.</p>
            <a class="btn" href="<?= h(url('openfil/tema_liste.php')) ?>">Tilbake</a>
          </div>
        </div>
      </div>
    </main>
    <?php
    include('../../includes/footer.php');
    return;
}

$temaId = (int) $_GET['id'];
$db = db();

$sql = <<<'SQL'
  SELECT
    t.TID,
    COALESCE(NULLIF(TRIM(t.Tema), ''), CONCAT('Tema ', t.TID)) AS name,
    COALESCE(t.AntArt, 0) AS article_count,
    COALESCE(t.AntBilde, 0) AS image_count,
    COALESCE(NULLIF(TRIM(t.TemaInnhold), ''), '') AS description
  FROM tblTema t
  WHERE t.TID = ?
    AND t.TID > 1
  LIMIT 1
SQL;

$stmt = $db->prepare($sql);
$stmt->bind_param('i', $temaId);
$stmt->execute();
$tema = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$tema) {
    http_response_code(404);
    $page_title = 'Tema ikke funnet';
    include('../../includes/header.php');
    ?>
    <main class="page-main">
      <div class="container">
        <div class="card">
          <div class="card-content">
            <h1 class="page-title"><?= h($page_title) ?></h1>
            <p>Vi fant ikke tema #<?= h($temaId) ?>.</p>
            <a class="btn" href="<?= h(url('openfil/tema_liste.php')) ?>">Tilbake</a>
          </div>
        </div>
      </div>
    </main>
    <?php
    include('../../includes/footer.php');
    return;
}

$temaNavn = $tema['name'] !== '' ? $tema['name'] : ('Tema ' . $temaId);
$page_title = 'Tema: ' . $temaNavn;
$articleCount = (int) $tema['article_count'];
$imageCount = (int) $tema['image_count'];
$description = $tema['description'] ?? '';

include('../../includes/header.php');
?>
<main class="page-main">
  <div class="container">
    <div class="card">
      <div class="card-content">
        <h1 class="page-title"><?= h($page_title) ?></h1>

        <dl class="detail-list">
          <dt>Tema</dt><dd><?= h($temaNavn) ?></dd>
          <?php if ($description !== ''): ?>
            <dt>Beskrivelse</dt><dd><?= nl2br(h($description)) ?></dd>
          <?php endif; ?>
          <dt>Artikler</dt><dd><?= h($articleCount) ?></dd>
          <dt>Bilder</dt><dd><?= h($imageCount) ?></dd>
        </dl>

        <a href="<?= h(url('openfil/tema_liste.php')) ?>" class="btn">Tilbake</a>
      </div>
    </div>
  </div>
</main>
<?php include('../../includes/footer.php'); ?>
