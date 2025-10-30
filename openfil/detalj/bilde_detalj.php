<?php
require_once('../../includes/bootstrap.php');

$activeNav = 'images';
$baseBreadcrumbs = [
    ['label' => 'Hjem', 'url' => url('index.php')],
    ['label' => 'Bilder', 'url' => url('openfil/bilder.php')],
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
            <p>Forespørselen mangler et gyldig bilde-id.</p>
            <a class="btn" href="<?= h(url('openfil/bilder.php')) ?>">Tilbake</a>
          </div>
        </div>
      </div>
    </main>
    <?php
    include('../../includes/footer.php');
    return;
}

$bildeId = (int) $_GET['id'];
$db = db();

$detailSql = <<<'SQL'
  SELECT
    b.PicID AS bilde_id,
    COALESCE(NULLIF(TRIM(b.PicMotiv), ''), CONCAT('Bilde ', b.PicID)) AS title,
    COALESCE(NULLIF(TRIM(f.ForfNavn), ''), NULLIF(TRIM(CONCAT_WS(' ', f.FNavn, f.ENavn)), '')) AS photographer,
    b.TattYear,
    b.TattYearSenest,
    b.CaYear,
    b.PicType,
    b.PicMerk,
    b.PicLenke,
    b.OpphRett,
    ms.StedTatt
  FROM tblBilde b
  LEFT JOIN tblForfatter f ON f.ForfID = b.ForfID
  LEFT JOIN tblMotivSted ms ON ms.MStedID = b.MStedID
  WHERE b.PicID = ?
  LIMIT 1
SQL;

$detailStmt = $db->prepare($detailSql);
$detailStmt->bind_param('i', $bildeId);
$detailStmt->execute();
$detail = $detailStmt->get_result()->fetch_assoc();
$detailStmt->close();

if (!$detail) {
    http_response_code(404);
    $page_title = 'Bilde ikke funnet';
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
            <p>Vi fant ikke bilde #<?= h($bildeId) ?>.</p>
            <a class="btn" href="<?= h(url('openfil/bilder.php')) ?>">Tilbake</a>
          </div>
        </div>
      </div>
    </main>
    <?php
    include('../../includes/footer.php');
    return;
}

$issuesSql = <<<'SQL'
  SELECT bl.Year AS issue_year, bl.BladNr AS issue_number
  FROM tblxBildeBlad bb
  INNER JOIN tblBlad bl ON bl.BladID = bb.BladID
  WHERE bb.PicID = ?
  ORDER BY bl.Year DESC, bl.BladNr DESC
SQL;

$issuesStmt = $db->prepare($issuesSql);
$issuesStmt->bind_param('i', $bildeId);
$issuesStmt->execute();
$issuesResult = $issuesStmt->get_result();
$publications = [];
while ($issue = $issuesResult->fetch_assoc()) {
    $year = $issue['issue_year'];
    $number = $issue['issue_number'];
    if ($year === null) {
        continue;
    }
    $label = trim($year . ' nr ' . ($number ?? ''));
    if ($label !== '') {
        $publications[] = $label;
    }
}
$issuesStmt->close();

$title = trim((string)($detail['title'] ?? ''));
if ($title === '') {
    $title = 'Bilde ' . $bildeId;
}

$photographer = trim((string)($detail['photographer'] ?? ''));
if ($photographer === '') {
    $photographer = 'Ukjent fotograf';
}

$yearText = '';
if (!empty($detail['TattYear'])) {
    $yearText = (string) $detail['TattYear'];
} elseif (!empty($detail['TattYearSenest'])) {
    $yearText = (string) $detail['TattYearSenest'];
}
if (!empty($detail['CaYear'])) {
    $yearText = 'ca. ' . ($yearText !== '' ? $yearText : (string) $detail['CaYear']);
}

$type = trim((string)($detail['PicType'] ?? ''));
$notes = trim((string)($detail['PicMerk'] ?? ''));
$link = trim((string)($detail['PicLenke'] ?? ''));
$rights = trim((string)($detail['OpphRett'] ?? ''));
$location = trim((string)($detail['StedTatt'] ?? ''));

$page_title = 'Bilde: ' . $title;
$detailLabel = $title !== '' ? $title : ('Bilde ' . $bildeId);
$breadcrumbs = array_merge($baseBreadcrumbs, [
    ['label' => $detailLabel],
]);
include('../../includes/header.php');
?>
<main class="page-main">
  <div class="container">
    <div class="card">
      <div class="card-content">
        <h1 class="page-title"><?= h($page_title) ?></h1>

        <dl class="detail-list">
          <dt>Tittel</dt><dd><?= h($title) ?></dd>
          <dt>Fotograf</dt><dd><?= h($photographer) ?></dd>
          <dt>År</dt><dd><?= h($yearText) ?></dd>
          <dt>Sted</dt><dd><?= h($location) ?></dd>
          <dt>Type</dt><dd><?= h($type) ?></dd>
          <dt>Merknader</dt><dd><?= h($notes) ?></dd>
          <dt>Opphavsrett</dt><dd><?= h($rights) ?></dd>
          <dt>Publisert i</dt>
          <dd><?= $publications ? h(implode(', ', $publications)) : 'Ingen registrerte utgaver.' ?></dd>
          <?php if ($link !== ''): ?>
          <dt>Lenke</dt><dd><a href="<?= h($link) ?>" target="_blank" rel="noopener noreferrer">Åpne kilde</a></dd>
          <?php endif; ?>
        </dl>

        <a href="<?= h(url('openfil/bilder.php')) ?>" class="btn">Tilbake</a>
      </div>
    </div>
  </div>
</main>
<?php include('../../includes/footer.php'); ?>


