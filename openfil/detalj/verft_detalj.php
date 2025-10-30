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
            <p>Forespørselen mangler et gyldig verft-id.</p>
            <a class="btn" href="<?= h(url('openfil/verft.php')) ?>">Tilbake</a>
          </div>
        </div>
      </div>
    </main>
    <?php
    include('../../includes/footer.php');
    return;
}

$verftId = (int) $_GET['id'];
$db = db();

$detailSql = "
  SELECT
    v.VerftID AS verft_id,
    COALESCE(NULLIF(TRIM(v.VerftNavn), ''), CONCAT('Verft ', v.VerftID)) AS name,
    COALESCE(NULLIF(TRIM(n.Nasjon), ''), '') AS nation,
    COUNT(DISTINCT CASE WHEN fn.FartID IS NOT NULL THEN fn.FartID END) AS vessel_count
  FROM tblVerft v
  LEFT JOIN tblzNasjon n ON n.NaID = v.NaID
  LEFT JOIN tblFartSpes fs ON fs.VerftID = v.VerftID
  LEFT JOIN tblFartNavn fn ON fn.ObjID = fs.ObjID AND (fn.AntDP IS NULL OR fn.AntDP > 0)
  WHERE v.VerftID = ?
  GROUP BY v.VerftID, v.VerftNavn, n.Nasjon
  LIMIT 1
";

$detailStmt = $db->prepare($detailSql);
$detailStmt->bind_param('i', $verftId);
$detailStmt->execute();
$detailResult = $detailStmt->get_result();
$detail = $detailResult->fetch_assoc();
$detailStmt->close();

if (!$detail) {
    http_response_code(404);
    $page_title = 'Verft ikke funnet';
    include('../../includes/header.php');
    ?>
    <main class="page-main">
      <div class="container">
        <div class="card">
          <div class="card-content">
            <h1 class="page-title"><?= h($page_title) ?></h1>
            <p>Vi fant ikke verft #<?= h($verftId) ?>.</p>
            <a class="btn" href="<?= h(url('openfil/verft.php')) ?>">Tilbake</a>
          </div>
        </div>
      </div>
    </main>
    <?php
    include('../../includes/footer.php');
    return;
}

$name = trim((string)($detail['name'] ?? ''));
if ($name === '') {
    $name = 'Verft ' . $verftId;
}
$nation = trim((string)($detail['nation'] ?? ''));
$vesselCount = (int)($detail['vessel_count'] ?? 0);

$page_title = 'Verft: ' . $name;
include('../../includes/header.php');
?>
<main class="page-main">
  <div class="container">
    <div class="card">
      <div class="card-content">
        <h1 class="page-title"><?= h($page_title) ?></h1>

        <dl class="detail-list">
          <dt>Verft</dt><dd><?= h($name) ?></dd>
          <dt>Land</dt><dd><?= $nation !== '' ? h($nation) : 'Ukjent' ?></dd>
          <dt>Antall fartoy</dt><dd><?= h((string)$vesselCount) ?></dd>
        </dl>

        <a href="<?= h(url('openfil/verft.php')) ?>" class="btn">Tilbake</a>
      </div>
    </div>
  </div>
</main>
<?php include('../../includes/footer.php'); ?>
