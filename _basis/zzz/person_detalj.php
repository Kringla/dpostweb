<?php
require_once('../../includes/bootstrap.php');

$activeNav = 'people';
$baseBreadcrumbs = [
    ['label' => 'Hjem', 'url' => url('index.php')],
    ['label' => 'Personer', 'url' => url('openfil/personer.php')],
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
            <p>Forespørselen mangler et gyldig person-id.</p>
          </div>
        </div>
      </div>
    </main>
    <?php
    include('../../includes/footer.php');
    return;
}

$personId = (int) $_GET['id'];
$db = db();

$detailSql = <<<'SQL'
  SELECT
    p.PersID,
    TRIM(CONCAT_WS(' ', NULLIF(p.FNavn, ''), NULLIF(p.ENavn, ''))) AS name,
    NULLIF(TRIM(p.PersTittel), '') AS title,
    NULLIF(TRIM(p.PersYrke), '') AS occupation,
    COALESCE(p.AntArt, 0) AS article_count
  FROM tblPerson p
  WHERE p.PersID = ?
  LIMIT 1
SQL;

$stmt = $db->prepare($detailSql);
$stmt->bind_param('i', $personId);
$stmt->execute();
$detail = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$detail) {
    http_response_code(404);
    $page_title = 'Person ikke funnet';
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
            <p>Vi fant ikke person #<?= h($personId) ?>.</p>
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
    $name = 'Person ' . $personId;
}

$roleParts = [];
if (!empty($detail['title'])) {
    $roleParts[] = $detail['title'];
}
if (!empty($detail['occupation'])) {
    $roleParts[] = $detail['occupation'];
}
$role = implode(', ', array_unique($roleParts));
$articleCount = (int) $detail['article_count'];

$page_title = 'Person: ' . $name;
$breadcrumbs = array_merge($baseBreadcrumbs, 
    ['label' => $name],
);

include('../../includes/header.php');
?>
<main class="page-main">
  <div class="container">
    <div class="card">
      <div class="card-content">
        <h1><?= h($page_title) ?></h1>

        <dl class="detail-list">
          <dt>Navn</dt><dd><?= h($name) ?></dd>
          <dt>Rolle</dt><dd><?= h($role !== '' ? $role : 'Ukjent') ?></dd>
          <dt>Antall artikler</dt><dd><?= h($articleCount) ?></dd>
        </dl>

      </div>
    </div>
  </div>
</main>
<?php include('../../includes/footer.php'); ?>
