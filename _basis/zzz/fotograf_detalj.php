<?php
require_once('../../includes/bootstrap.php');

$activeNav = 'photographers';
$baseBreadcrumbs = [
    ['label' => 'Hjem', 'url' => url('index.php')],
    ['label' => 'Fotografer', 'url' => url('openfil/fotografer.php')],
];


if (!isset($_GET['id']) || !is_valid_id($_GET['id'])) {
    http_response_code(400);
    $page_title = 'Ugyldig forespørsel';
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
            <p>Forespørselen mangler et gyldig fotograf-id.</p>
          </div>
        </div>
      </div>
    </main>
    <?php
    include('../../includes/footer.php');
    return;
}

$fotografId = (int) $_GET['id'];
$db = db();

$detailSql = <<<'SQL'
  SELECT
    f.ForfID,
    COALESCE(NULLIF(TRIM(f.ForfNavn), ''), NULLIF(TRIM(CONCAT_WS(' ', f.FNavn, f.ENavn)), '')) AS name,
    NULLIF(TRIM(f.PostAdresse), '') AS address,
    NULLIF(TRIM(f.Postnr), '') AS postal_code,
    NULLIF(TRIM(f.Poststed), '') AS city,
    NULLIF(TRIM(f.Land), '') AS country,
    NULLIF(TRIM(f.EpostAdresse), '') AS email,
    COALESCE(f.AntBilde, 0) AS image_count,
    COALESCE(f.ForfBio, '') AS biography
  FROM tblForfatter f
  WHERE f.ForfID = ?
    AND (f.Fotograf <> 0 OR COALESCE(f.AntBilde, 0) > 0)
  LIMIT 1
SQL;

$stmt = $db->prepare($detailSql);
$stmt->bind_param('i', $fotografId);
$stmt->execute();
$detail = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$detail) {
    http_response_code(404);
    $page_title = 'Fotograf ikke funnet';
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
            <p>Vi fant ikke fotograf #<?= h($fotografId) ?>.</p>
          </div>
        </div>
      </div>
    </main>
    <?php
    include('../../includes/footer.php');
    return;
}

$periodSql = <<<'SQL'
  SELECT
    MIN(COALESCE(NULLIF(b.TattYear, 0), NULLIF(b.TattYearSenest, 0), bl.Year)) AS first_year,
    MAX(COALESCE(NULLIF(b.TattYear, 0), NULLIF(b.TattYearSenest, 0), bl.Year)) AS last_year
  FROM tblBilde b
  LEFT JOIN tblxBildeBlad bb ON bb.PicID = b.PicID
  LEFT JOIN tblBlad bl ON bl.BladID = bb.BladID
  WHERE b.ForfID = ?
SQL;

$periodStmt = $db->prepare($periodSql);
$periodStmt->bind_param('i', $fotografId);
$periodStmt->execute();
$period = $periodStmt->get_result()->fetch_assoc() ?: ['first_year' => null, 'last_year' => null];
$periodStmt->close();

$locationParts = array_filter([
    $detail['postal_code'] ?? null,
    $detail['city'] ?? null,
    $detail['country'] ?? null,
]);
$location = implode(' ', $locationParts);
$name = $detail['name'] !== null && $detail['name'] !== '' ? $detail['name'] : 'Fotograf ' . $fotografId;

$page_title = 'Fotograf: ' . $name;
$breadcrumbs = array_merge($baseBreadcrumbs, 
    ['label' => $name],
);

include('../../includes/header.php');
?>
<main class="page-main">
  <div class="container">
    <div class="card">
      <div class="card-content">
        <h1 class="page-title"><?= h($page_title) ?></h1>

        <dl class="detail-list">
          <dt>Navn</dt><dd><?= h($name) ?></dd>
          <dt>Adresse</dt><dd><?= h($detail['address'] ?? '') ?></dd>
          <dt>Lokasjon</dt><dd><?= h($location) ?></dd>
          <dt>E-post</dt><dd><?= h($detail['email'] ?? '') ?></dd>
          <dt>Antall bilder</dt><dd><?= h($detail['image_count']) ?></dd>
          <dt>Første år</dt><dd><?= h(($period['first_year'] ?? '') ? (string)$period['first_year'] : '') ?></dd>
          <dt>Siste år</dt><dd><?= h(($period['last_year'] ?? '') ? (string)$period['last_year'] : '') ?></dd>
          <dt>Biografi</dt><dd><?= h($detail['biography']) ?></dd>
        </dl>

      </div>
    </div>
  </div>
</main>
<?php include('../../includes/footer.php'); ?>





