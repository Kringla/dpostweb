<?php
require_once('../../includes/bootstrap.php');

if (!isset($_GET['id']) || !is_valid_id($_GET['id'])) {
    http_response_code(400);
    $page_title = 'Ugyldig forespørsel';
    include('../../includes/header.php');
    ?>
    <main class="page-main">
      <div class="container">
        <div class="card">
          <div class="card-content">
            <h1 class="page-title"><?= h($page_title) ?></h1>
            <p>Forespørselen mangler et gyldig forfatter-id.</p>
            <a class="btn" href="<?= h(url('openfil/forfattere.php')) ?>">Tilbake</a>
          </div>
        </div>
      </div>
    </main>
    <?php
    include('../../includes/footer.php');
    return;
}

$forfatterId = (int) $_GET['id'];
$db = db();

$detailSql = <<<'SQL'
  SELECT
    f.ForfID,
    COALESCE(NULLIF(TRIM(f.ForfNavn), ''), NULLIF(TRIM(CONCAT_WS(' ', f.FNavn, f.ENavn)), '')) AS name,
    NULLIF(TRIM(f.FNavn), '') AS first_name,
    NULLIF(TRIM(f.ENavn), '') AS last_name,
    NULLIF(TRIM(f.PostAdresse), '') AS address,
    NULLIF(TRIM(f.Postnr), '') AS postal_code,
    NULLIF(TRIM(f.Poststed), '') AS city,
    NULLIF(TRIM(f.Land), '') AS country,
    NULLIF(TRIM(f.EpostAdresse), '') AS email,
    COALESCE(f.AntArt, 0) AS article_count,
    COALESCE(f.AntBilde, 0) AS image_count,
    COALESCE(f.ForfBio, '') AS biography,
    f.Forfatter,
    f.Fotograf
  FROM tblForfatter f
  WHERE f.ForfID = ?
  LIMIT 1
SQL;

$stmt = $db->prepare($detailSql);
$stmt->bind_param('i', $forfatterId);
$stmt->execute();
$detail = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$detail) {
    http_response_code(404);
    $page_title = 'Forfatter ikke funnet';
    include('../../includes/header.php');
    ?>
    <main class="page-main">
      <div class="container">
        <div class="card">
          <div class="card-content">
            <h1 class="page-title"><?= h($page_title) ?></h1>
            <p>Vi fant ikke forfatter #<?= h($forfatterId) ?>.</p>
            <a class="btn" href="<?= h(url('openfil/forfattere.php')) ?>">Tilbake</a>
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
    MIN(bl.Year) AS first_year,
    MAX(bl.Year) AS last_year
  FROM tblxArtForf af
  LEFT JOIN tblArtikkel a ON a.ArtID = af.ArtID
  LEFT JOIN tblBlad bl ON bl.BladID = a.BladID
  WHERE af.ForfID = ?
SQL;

$periodStmt = $db->prepare($periodSql);
$periodStmt->bind_param('i', $forfatterId);
$periodStmt->execute();
$period = $periodStmt->get_result()->fetch_assoc() ?: ['first_year' => null, 'last_year' => null];
$periodStmt->close();

$name = $detail['name'] !== null && $detail['name'] !== '' ? $detail['name'] : 'Forfatter ' . $forfatterId;
$locationParts = array_filter([
    $detail['postal_code'] ?? null,
    $detail['city'] ?? null,
    $detail['country'] ?? null,
]);
$location = implode(' ', $locationParts);

$page_title = 'Forfatter: ' . $name;
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
          <dt>Antall artikler</dt><dd><?= h($detail['article_count']) ?></dd>
          <dt>Antall bilder</dt><dd><?= h($detail['image_count']) ?></dd>
          <dt>Første år</dt><dd><?= h(($period['first_year'] ?? '') ? (string)$period['first_year'] : '') ?></dd>
          <dt>Siste år</dt><dd><?= h(($period['last_year'] ?? '') ? (string)$period['last_year'] : '') ?></dd>
          <dt>Forfatter / Fotograf</dt>
          <dd>
            <?php
              $roles = [];
              if (!empty($detail['Forfatter'])) { $roles[] = 'Forfatter'; }
              if (!empty($detail['Fotograf'])) { $roles[] = 'Fotograf'; }
              echo $roles ? h(implode(', ', $roles)) : 'Ukjent';
            ?>
          </dd>
          <dt>Biografi</dt><dd><?= h($detail['biography']) ?></dd>
        </dl>

        <a href="<?= h(url('openfil/forfattere.php')) ?>" class="btn">Tilbake</a>
      </div>
    </div>
  </div>
</main>
<?php include('../../includes/footer.php'); ?>




