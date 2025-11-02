<?php
require_once('../../includes/bootstrap.php');

$activeNav = 'shipping';
$baseBreadcrumbs = [
    ['label' => 'Hjem', 'url' => url('index.php')],
    ['label' => 'Rederier', 'url' => url('openfil/rederier.php')],
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
            <h1><?= h($page_title) ?></h1>
            <p>Forespørselen mangler et gyldig rederi-id.</p>
          </div>
        </div>
      </div>
    </main>
    <?php
    include('../../includes/footer.php');
    return;
}

$rederiId = (int) $_GET['id'];
$db = db();

$sql = "
  SELECT
    r.RedID AS id,
    COALESCE(NULLIF(TRIM(r.RedNavn), ''), CONCAT('Rederi ', r.RedID)) AS name,
    COALESCE(NULLIF(TRIM(r.Sted), ''), '') AS location,
    COUNT(DISTINCT CASE WHEN fn.FartID IS NOT NULL THEN fn.FartID END) AS vessel_count
  FROM tblRederi r
  LEFT JOIN tblFartTid ft
    ON ft.RedID = r.RedID AND (ft.Navning IS NULL OR ft.Navning <> 0)
  LEFT JOIN tblFartNavn fn
    ON fn.FartID = ft.FartID AND (fn.AntDP IS NULL OR fn.AntDP > 0)
  WHERE r.RedID = ?
  GROUP BY r.RedID, r.RedNavn, r.Sted
  LIMIT 1
";

$stmt = $db->prepare($sql);
$stmt->bind_param('i', $rederiId);
$stmt->execute();
$result = $stmt->get_result();
$rederi = $result->fetch_assoc();
$stmt->close();

if (!$rederi) {
    http_response_code(404);
    $page_title = 'Rederi ikke funnet';
    $breadcrumbs = array_merge($baseBreadcrumbs, [
        ['label' => $page_title],
    ]);
    include('../../includes/header.php');
    ?>
    <main class="page-main">
      <div class="container">
        <div class="card">
          <div class="card-content">
            <h1><?= h($page_title) ?></h1>
            <p>Vi fant ikke rederi #<?= h($rederiId) ?>.</p>
          </div>
        </div>
      </div>
    </main>
    <?php
    include('../../includes/footer.php');
    return;
}

$name = trim((string)($rederi['name'] ?? ''));
if ($name === '') {
    $name = 'Rederi ' . $rederiId;
}

$location = trim((string)($rederi['location'] ?? ''));
$vesselCount = (int)($rederi['vessel_count'] ?? 0);

$page_title = 'Rederi: ' . $name;
include('../../includes/header.php');
?>
<main class="page-main">
  <div class="container">
    <div class="card">
      <div class="card-content">
        <h1><?= h($page_title) ?></h1>

        <dl class="detail-list">
          <dt>Rederi</dt><dd><?= h($name) ?></dd>
          <dt>Hjemsted</dt><dd><?= $location !== '' ? h($location) : 'Ukjent' ?></dd>
          <dt>Antall fartøy</dt><dd><?= h((string)$vesselCount) ?></dd>
        </dl>

      </div>
    </div>
  </div>
</main>
<?php include('../../includes/footer.php'); ?>
