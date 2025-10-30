<?php
require_once('../../includes/bootstrap.php');

if (!isset($_GET['id']) || !is_valid_id($_GET['id'])) {
    http_response_code(400);
    $page_title = "Ugyldig forespørsel";
    include('../../includes/header.php');
    ?>
    <main class="page-main">
      <div class="container">
        <div class="card">
          <div class="card-content">
            <h1 class="page-title"><?= h($page_title) ?></h1>
            <p>Forespørselen mangler et gyldig fartøy-id.</p>
            <a class="btn" href="<?= h(url('openfil/fartøyer.php')) ?>">Tilbake</a>
          </div>
        </div>
      </div>
    </main>
    <?php
    include('../../includes/footer.php');
    return;
}

$fartøyId = (int) $_GET['id'];
$db = db();

$detailSql = "
  SELECT
    fs.ObjID AS fartøy_id,
    COALESCE(NULLIF(TRIM(fs.FartObjNavn), ''), CONCAT('Fartøy ', fs.ObjID)) AS name,
    NULLIF(TRIM(fs.TypeFork), '') AS vessel_type,
    NULLIF(fs.YearSpes, 0) AS built_year,
    NULLIF(TRIM(fs.VerftNavn), '') AS yard_name,
    NULLIF(TRIM(fs.Sted), '') AS yard_location,
    NULLIF(TRIM(fs.Nasjon), '') AS nationality,
    NULLIF(TRIM(fs.TypeFunksjon), '') AS function_type,
    NULLIF(TRIM(fs.TypeSkrog), '') AS hull_type,
    NULLIF(TRIM(fs.DriftMiddel), '') AS propulsion,
    NULLIF(TRIM(fs.MotorType), '') AS engine_type,
    NULLIF(fs.MotorEff, 0) AS engine_power,
    NULLIF(fs.Lengde, 0) AS length_m,
    NULLIF(fs.Bredde, 0) AS beam_m,
    NULLIF(fs.Dypg, 0) AS draft_m,
    NULLIF(fs.Tonnasje, 0) AS tonnage
  FROM vwFartObjSpes fs
  WHERE fs.ObjID = ?
  LIMIT 1
";

$detailStmt = $db->prepare($detailSql);
$detailStmt->bind_param('i', $fartøyId);
$detailStmt->execute();
$detailResult = $detailStmt->get_result();
$detail = $detailResult->fetch_assoc();
$detailStmt->close();

if (!$detail) {
    http_response_code(404);
    $page_title = 'Fartøy ikke funnet';
    include('../../includes/header.php');
    ?>
    <main class="page-main">
      <div class="container">
        <div class="card">
          <div class="card-content">
            <h1 class="page-title"><?= h($page_title) ?></h1>
            <p>Vi fant ikke fartøy #<?= h($fartøyId) ?>.</p>
            <a class="btn" href="<?= h(url('openfil/fartøyer.php')) ?>">Tilbake</a>
          </div>
        </div>
      </div>
    </main>
    <?php
    include('../../includes/footer.php');
    return;
}

$assocSql = <<<'SQL'
  SELECT
    GROUP_CONCAT(
      DISTINCT NULLIF(TRIM(nav.RedNavn), '')
      ORDER BY nav.RedNavn
      SEPARATOR ', '
    ) AS rederier,
    GROUP_CONCAT(
      DISTINCT NULLIF(TRIM(nav.RegHavn), '')
      ORDER BY nav.RegHavn
      SEPARATOR ', '
    ) AS registrering
  FROM vwFartNavnDposten nav
  WHERE nav.ObjID = ?
SQL;

$assocStmt = $db->prepare($assocSql);
$assocStmt->bind_param('i', $fartøyId);
$assocStmt->execute();
$assocData = $assocStmt->get_result()->fetch_assoc() ?: ['rederier' => null, 'registrering' => null];
$assocStmt->close();

$name = trim((string)($detail['name'] ?? ''));
if ($name === '') {
    $name = 'Fartøy ' . $fartøyId;
}

$vesselType = trim((string)($detail['vessel_type'] ?? 'Ukjent type'));
$builtYear = $detail['built_year'] !== null ? (string) $detail['built_year'] : '';
$yardName = trim((string)($detail['yard_name'] ?? ''));
$yardLocation = trim((string)($detail['yard_location'] ?? ''));
$nationality = trim((string)($detail['nationality'] ?? ''));
$functionType = trim((string)($detail['function_type'] ?? ''));
$hullType = trim((string)($detail['hull_type'] ?? ''));
$propulsion = trim((string)($detail['propulsion'] ?? ''));
$engineType = trim((string)($detail['engine_type'] ?? ''));
$enginePower = $detail['engine_power'] !== null ? number_format((float)$detail['engine_power'], 0, ',', ' ') . ' hk' : '';
$length = $detail['length_m'] !== null ? number_format((float)$detail['length_m'], 1, ',', ' ') . ' m' : '';
$beam = $detail['beam_m'] !== null ? number_format((float)$detail['beam_m'], 1, ',', ' ') . ' m' : '';
$draft = $detail['draft_m'] !== null ? number_format((float)$detail['draft_m'], 1, ',', ' ') . ' m' : '';
$tonnage = $detail['tonnage'] !== null ? number_format((float)$detail['tonnage'], 1, ',', ' ') . ' brutto tonn' : '';
$rederier = trim((string)($assocData['rederier'] ?? ''));
$registrering = trim((string)($assocData['registrering'] ?? ''));

$page_title = 'Fartøy: ' . $name;
include('../../includes/header.php');
?>
<main class="page-main">
  <div class="container">
    <div class="card">
      <div class="card-content">
        <h1 class="page-title"><?= h($page_title) ?></h1>

        <dl class="detail-list">
          <dt>Navn</dt><dd><?= h($name) ?></dd>
          <dt>Type</dt><dd><?= h($vesselType) ?></dd>
          <dt>Byggeår</dt><dd><?= h($builtYear) ?></dd>
          <dt>Verft</dt><dd><?= h($yardName) ?></dd>
          <dt>Verftsted</dt><dd><?= h($yardLocation) ?></dd>
          <dt>Nasjonalitet</dt><dd><?= h($nationality) ?></dd>
          <dt>Rederi</dt><dd><?= $rederier !== '' ? h($rederier) : 'Ukjent' ?></dd>
          <dt>Registreringshavn</dt><dd><?= $registrering !== '' ? h($registrering) : 'Ukjent' ?></dd>
          <dt>Funksjon</dt><dd><?= h($functionType) ?></dd>
          <dt>Skrogtype</dt><dd><?= h($hullType) ?></dd>
          <dt>Driftsmiddel</dt><dd><?= h($propulsion) ?></dd>
          <dt>Motor</dt><dd><?= h($engineType) ?></dd>
          <dt>Motoreffekt</dt><dd><?= h($enginePower) ?></dd>
          <dt>Lengde</dt><dd><?= h($length) ?></dd>
          <dt>Bredde</dt><dd><?= h($beam) ?></dd>
          <dt>Dypgående</dt><dd><?= h($draft) ?></dd>
          <dt>Tonnasje</dt><dd><?= h($tonnage) ?></dd>
        </dl>

        <a href="<?= h(url('openfil/fartøyer.php')) ?>" class="btn">Tilbake</a>
      </div>
    </div>
  </div>
</main>
<?php include('../../includes/footer.php'); ?>


