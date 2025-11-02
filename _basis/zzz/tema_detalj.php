<?php
require_once('../../includes/bootstrap.php');

$activeNav = 'topics';
$baseBreadcrumbs = [
    ['label' => 'Hjem', 'url' => url('index.php')],
    ['label' => 'Tema', 'url' => url('openfil/tema_liste.php')],
];


if (!isset($_GET['id']) || !is_valid_id($_GET['id'])) {
    http_response_code(400);
    $page_title = 'Ugyldig foresporsel';
    $breadcrumbs = array_merge($baseBreadcrumbs, [
        ['label' => $page_title],
    ]);
    include('../../includes/header.php');
    ?>
    <main class="page-main">
      <div class="container">
        <div class="card">
          <div class="card-content">
            <h1 class="page-title"><?= h($page_title) ?></h1>
            <p>Forespørselen mangler et gyldig tema-id.</p>
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
    $breadcrumbs = array_merge($baseBreadcrumbs, [
        ['label' => $page_title],
    ]);
    include('../../includes/header.php');
    ?>
    <main class="page-main">
      <div class="container">
        <div class="card">
          <div class="card-content">
            <h1 class="page-title"><?= h($page_title) ?></h1>
            <p>Vi fant ikke tema #<?= h($temaId) ?>.</p>
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
$breadcrumbs = array_merge($baseBreadcrumbs, 
    ['label' => $temaNavn],
);

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

      </div>
    </div>
  </div>
</main>
<?php include('../../includes/footer.php'); ?>
