<?php
require_once('../../includes/bootstrap.php');

$activeNav = 'vessels';
$baseBreadcrumbs = [
    ['label' => 'Hjem', 'url' => url('index.php')],
    ['label' => 'Fartoyer', 'url' => url('openfil/fartoyer.php')],
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
            <p>Foresporselen mangler et gyldig fartoy-id.</p>
          </div>
        </div>
      </div>
    </main>
    <?php
    include('../../includes/footer.php');
    return;
}

$fartoyId = (int) $_GET['id'];
$requestedObjId = filter_input(INPUT_GET, 'obj_id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$requestedTidId = filter_input(INPUT_GET, 'tid', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$db = db();

$composeName = static function (?string $type, ?string $name, ?string $tilnavn = null): string {
    $parts = [];
    $type = $type !== null ? trim($type) : '';
    $name = $name !== null ? trim($name) : '';
    $tilnavn = $tilnavn !== null ? trim($tilnavn) : '';
    if ($type !== '') {
        $parts[] = $type;
    }
    if ($name !== '') {
        $parts[] = $name;
    }
    $label = implode(' ', $parts);
    if ($tilnavn !== '') {
        $label = $label !== '' ? $label . ' (' . $tilnavn . ')' : $tilnavn;
    }
    return $label;
};

$formatYearMonth = static function (?int $year, ?int $month): string {
    $year = $year ?? 0;
    $month = $month ?? 0;
    if ($year <= 0 && $month <= 0) {
        return 'Ukjent';
    }
    if ($year > 0 && $month > 0) {
        return $year . '/' . str_pad((string)$month, 2, '0', STR_PAD_LEFT);
    }
    if ($year > 0) {
        return (string)$year;
    }
    return 'Mnd ' . str_pad((string)$month, 2, '0', STR_PAD_LEFT);
};

$computeEventLabel = static function (array $row): string {
    $isNewBuild = !empty($row['is_newbuild']);
    $isConversion = !empty($row['is_conversion']);
    $isOwnerChange = !empty($row['is_owner_change']);
    $isNameChange = !empty($row['is_name_change']);

    if ($isNewBuild) {
        return 'Nybygg';
    }
    if ($isConversion && !$isOwnerChange && !$isNameChange) {
        return 'Ombygd';
    }
    if (!$isConversion && $isOwnerChange && !$isNameChange) {
        return 'Ny eier';
    }
    if (!$isConversion && $isOwnerChange && $isNameChange) {
        return 'Ny eier/navn';
    }
    if ($isConversion && $isOwnerChange && $isNameChange) {
        return 'Ny eier/navn/ombygd';
    }
    if ($isConversion && !$isOwnerChange && $isNameChange) {
        return 'Nytt navn/ombygd';
    }
    if (!$isConversion && !$isOwnerChange && $isNameChange) {
        return 'Nytt navn';
    }
    $fallback = isset($row['hendelse_text']) ? trim((string)$row['hendelse_text']) : '';
    return $fallback !== '' ? $fallback : 'Annet';
};

$timelineSqlBase = "
  SELECT
    ft.TidID AS tid_id,
    ft.FartID AS fart_id,
    ft.ObjID AS obj_id,
    ft.SpesID AS spes_id,
    ft.YearTid AS year_tid,
    ft.MndTid AS month_tid,
    ft.Objekt AS is_newbuild,
    ft.Bygging AS is_conversion,
    ft.Eierskifte AS is_owner_change,
    ft.Navning AS is_name_change,
    ft.Hendelse AS hendelse_text,
    COALESCE(NULLIF(TRIM(ztName.TypeFork), ''), NULLIF(TRIM(ztObj.TypeFork), '')) AS type_fork,
    COALESCE(NULLIF(TRIM(fn.FartNavn), ''), NULLIF(TRIM(fo.FartObjNavn), '')) AS vessel_name,
    NULLIF(TRIM(fn.Tilnavn), '') AS vessel_tilnavn
  FROM tblFartTid ft
  LEFT JOIN tblFartNavn fn ON fn.FartID = ft.FartID AND fn.ObjID = ft.ObjID
  LEFT JOIN tblFartObj fo ON fo.ObjID = ft.ObjID
  LEFT JOIN tblzFartType ztName ON ztName.FTID = fn.FTIDNavn
  LEFT JOIN tblzFartType ztObj ON ztObj.FTID = fo.FTIDObj
";

if ($requestedObjId !== null) {
    $timelineSql = $timelineSqlBase . "
  WHERE (ft.FartID = ? OR ft.ObjID = ?)
  ORDER BY
    CASE WHEN ft.YearTid IS NULL OR ft.YearTid = 0 THEN 1 ELSE 0 END,
    ft.YearTid,
    CASE WHEN ft.MndTid IS NULL OR ft.MndTid = 0 THEN 1 ELSE 0 END,
    ft.MndTid,
    ft.TidID
";
    $timelineStmt = $db->prepare($timelineSql);
    $timelineStmt->bind_param('ii', $fartoyId, $requestedObjId);
} else {
    $timelineSql = $timelineSqlBase . "
  WHERE ft.FartID = ?
  ORDER BY
    CASE WHEN ft.YearTid IS NULL OR ft.YearTid = 0 THEN 1 ELSE 0 END,
    ft.YearTid,
    CASE WHEN ft.MndTid IS NULL OR ft.MndTid = 0 THEN 1 ELSE 0 END,
    ft.MndTid,
    ft.TidID
";
    $timelineStmt = $db->prepare($timelineSql);
    $timelineStmt->bind_param('i', $fartoyId);
}

$timelineRows = [];
$timelineStmt->execute();
$timelineResult = $timelineStmt->get_result();
while ($row = $timelineResult->fetch_assoc()) {
    $year = isset($row['year_tid']) ? (int)$row['year_tid'] : null;
    $month = isset($row['month_tid']) ? (int)$row['month_tid'] : null;
    $displayName = $composeName($row['type_fork'] ?? '', $row['vessel_name'] ?? '', $row['vessel_tilnavn'] ?? '');
    $timelineRows[] = [
        'tid_id' => (int)$row['tid_id'],
        'fart_id' => (int)$row['fart_id'],
        'obj_id' => $row['obj_id'] !== null ? (int)$row['obj_id'] : null,
        'year_month' => $formatYearMonth($year, $month),
        'event_label' => $computeEventLabel($row),
        'display_name' => $displayName !== '' ? $displayName : ('Fartoy ' . $fartoyId),
        'type_fork' => $row['type_fork'] ?? '',
        'vessel_name' => $row['vessel_name'] ?? '',
        'tilnavn' => $row['vessel_tilnavn'] ?? '',
    ];
}
$timelineStmt->close();

$selectedTimeline = null;
$requestedFartId = $fartoyId;
if ($requestedTidId !== null) {
    foreach ($timelineRows as $candidate) {
        if ($candidate['tid_id'] === $requestedTidId) {
            $selectedTimeline = $candidate;
            break;
        }
    }
}
if ($selectedTimeline === null && $requestedObjId !== null) {
    foreach ($timelineRows as $candidate) {
        if (
            $candidate['obj_id'] !== null &&
            $candidate['obj_id'] === $requestedObjId &&
            $candidate['fart_id'] === $requestedFartId
        ) {
            $selectedTimeline = $candidate;
            break;
        }
    }
}
if ($selectedTimeline === null && $requestedObjId !== null) {
    foreach ($timelineRows as $candidate) {
        if ($candidate['obj_id'] !== null && $candidate['obj_id'] === $requestedObjId) {
            $selectedTimeline = $candidate;
            break;
        }
    }
}
if ($selectedTimeline === null && $requestedFartId !== null) {
    foreach ($timelineRows as $candidate) {
        if ($candidate['fart_id'] === $requestedFartId) {
            $selectedTimeline = $candidate;
            break;
        }
    }
}
if ($selectedTimeline === null && !empty($timelineRows)) {
    $selectedTimeline = $timelineRows[0];
}

$selectedTidId = $selectedTimeline['tid_id'] ?? null;
$selectedObjId = $selectedTimeline['obj_id'] ?? ($requestedObjId ?: null);

$detailData = null;
if ($selectedTidId !== null) {
    $detailSql = "
      SELECT
        ft.TidID AS tid_id,
        COALESCE(NULLIF(TRIM(ztName.TypeFork), ''), NULLIF(TRIM(ztObj.TypeFork), '')) AS name_type_code,
        COALESCE(
          NULLIF(TRIM(fn.FartNavn), ''),
          NULLIF(TRIM(fo.FartObjNavn), ''),
          CONCAT('Fartoy ', ft.FartID)
        ) AS primary_name,
        NULLIF(TRIM(fn.Tilnavn), '') AS tilnavn,
        NULLIF(TRIM(org.OrgNavn), '') AS org_name,
        NULLIF(TRIM(fn.PicFile), '') AS pic_file,
        NULLIF(TRIM(ztObj.TypeFork), '') AS object_type_code,
        NULLIF(TRIM(fo.FartObjNavn), '') AS object_name,
        fo.IMO AS imo_number,
        CASE WHEN fo.StroketYear IS NULL OR fo.StroketYear = 0 THEN NULL ELSE fo.StroketYear END AS stroket_year,
        NULLIF(TRIM(fo.StroketGrunn), '') AS stroket_reason,
        NULLIF(TRIM(verft.VerftNavn), '') AS verft_name,
        CASE WHEN fs.Byggenr IS NULL OR fs.Byggenr = 0 THEN NULL ELSE fs.Byggenr END AS byggenr,
        NULLIF(TRIM(zFunk.TypeFunksjon), '') AS function_type,
        NULLIF(TRIM(fs.FunkDetalj), '') AS function_detail,
        NULLIF(TRIM(zDrift.DriftMiddel), '') AS drive_type,
        NULLIF(TRIM(fs.Rigg), '') AS rigg,
        NULLIF(TRIM(zSkrog.TypeSkrog), '') AS hull_type,
        fs.Tonnasje AS tonnage,
        NULLIF(TRIM(fs.MotorType), '') AS motor_type,
        NULLIF(TRIM(fs.MotorEff), '') AS motor_eff,
        fs.MaxFart AS max_speed,
        fs.Lengde AS length,
        fs.Bredde AS breadth,
        fs.Dypg AS draft
      FROM tblFartTid ft
      LEFT JOIN tblFartNavn fn ON fn.FartID = ft.FartID AND fn.ObjID = ft.ObjID
      LEFT JOIN tblFartObj fo ON fo.ObjID = ft.ObjID
      LEFT JOIN tblFartSpes fs ON fs.SpesID = ft.SpesID
      LEFT JOIN tblVerft verft ON verft.VerftID = fs.VerftID
      LEFT JOIN tblzFartType ztName ON ztName.FTID = fn.FTIDNavn
      LEFT JOIN tblzFartType ztObj ON ztObj.FTID = fo.FTIDObj
      LEFT JOIN tblOrganisasjon org ON org.OrgID = fn.OrgID
      LEFT JOIN tblzFartFunk zFunk ON zFunk.FartFunkID = fs.FartFunkID
      LEFT JOIN tblzFartDrift zDrift ON zDrift.FartDriftID = fs.FartDriftID
      LEFT JOIN tblzFartSkrog zSkrog ON zSkrog.FartSkrogID = fs.FartSkrogID
      WHERE ft.TidID = ?
      LIMIT 1
    ";
    $detailStmt = $db->prepare($detailSql);
    $detailStmt->bind_param('i', $selectedTidId);
    $detailStmt->execute();
    $detailResult = $detailStmt->get_result();
    $detailData = $detailResult->fetch_assoc() ?: null;
    $detailStmt->close();
}

$nameRow = null;
if (!$detailData && empty($timelineRows)) {
    $nameSql = "
      SELECT
        COALESCE(NULLIF(TRIM(zt.TypeFork), ''), '') AS name_type_code,
        COALESCE(NULLIF(TRIM(fn.FartNavn), ''), CONCAT('Fartoy ', fn.FartID)) AS primary_name,
        NULLIF(TRIM(fn.Tilnavn), '') AS tilnavn
      FROM tblFartNavn fn
      LEFT JOIN tblzFartType zt ON zt.FTID = fn.FTIDNavn
      WHERE fn.FartID = ?
      ORDER BY fn.SortPri DESC, fn.YearNavn ASC, fn.MndNavn ASC
      LIMIT 1
    ";
    $nameStmt = $db->prepare($nameSql);
    $nameStmt->bind_param('i', $fartoyId);
    $nameStmt->execute();
    $nameRow = $nameStmt->get_result()->fetch_assoc() ?: null;
    $nameStmt->close();
}

if (!$detailData && empty($timelineRows) && $nameRow === null) {
    http_response_code(404);
    $page_title = 'Fartoy ikke funnet';
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
            <p>Vi fant ikke fartoy #<?= h($fartoyId) ?>.</p>
          </div>
        </div>
      </div>
    </main>
    <?php
    include('../../includes/footer.php');
    return;
}

$pageName = '';
if ($selectedTimeline && ($selectedTimeline['display_name'] ?? '') !== '') {
    $pageName = $selectedTimeline['display_name'];
} elseif ($detailData) {
    $pageName = $composeName(
        $detailData['name_type_code'] ?? '',
        $detailData['primary_name'] ?? '',
        $detailData['tilnavn'] ?? ''
    );
} elseif (!empty($timelineRows)) {
    $first = $timelineRows[0];
    $pageName = $first['display_name'] ?? '';
}
if ($pageName === '' && $nameRow) {
    $pageName = $composeName(
        $nameRow['name_type_code'] ?? '',
        $nameRow['primary_name'] ?? '',
        $nameRow['tilnavn'] ?? ''
    );
}
if ($pageName === '') {
    $pageName = 'Fartoy ' . $fartoyId;
}

$page_title = 'Fartoy: ' . $pageName;
$breadcrumbs = array_merge($baseBreadcrumbs, [
    ['label' => $pageName],
]);

$detailSections = [];
if ($detailData) {
    $sectionNameRows = [];
    $detailPrimaryName = $composeName(
        $detailData['name_type_code'] ?? '',
        $detailData['primary_name'] ?? '',
        $detailData['tilnavn'] ?? ''
    );
    if ($detailPrimaryName === '') {
        $detailPrimaryName = $pageName;
    }
    if ($detailPrimaryName !== '') {
        $sectionNameRows[] = ['label' => 'Navn', 'value' => $detailPrimaryName];
    }
    if (!empty($detailData['org_name'])) {
        $sectionNameRows[] = ['label' => 'Forening', 'value' => $detailData['org_name']];
    }
    if (!empty($detailData['pic_file'])) {
        $picFile = basename((string)$detailData['pic_file']);
        if ($picFile !== '') {
            $picUrl = 'https://ihlen.net/installs/DPfilerPIC/' . rawurlencode($picFile);
            $sectionNameRows[] = [
                'label' => 'Bilde',
                'value' => $picUrl,
                'link_text' => $picFile,
                'is_link' => true,
            ];
        }
    }
    if (!empty($sectionNameRows)) {
        $detailSections[] = [
            'title' => 'Navn og tilhorighet',
            'rows' => $sectionNameRows,
        ];
    }

    $objectRows = [];
    $objectName = $composeName(
        $detailData['object_type_code'] ?? '',
        $detailData['object_name'] ?? '',
        null
    );
    if ($objectName !== '') {
        $objectRows[] = ['label' => 'Navn', 'value' => $objectName];
    }
    $imoNumber = $detailData['imo_number'];
    if ($imoNumber !== null && (int)$imoNumber > 0) {
        $objectRows[] = ['label' => 'IMO nr', 'value' => (string)(int)$imoNumber];
    }
    $stroketYear = $detailData['stroket_year'] ?? null;
    $stroketReason = $detailData['stroket_reason'] ?? '';
    $stroketReason = trim((string)$stroketReason);
    if (($stroketYear !== null && (int)$stroketYear > 0) || $stroketReason !== '') {
        $parts = [];
        if ($stroketYear !== null && (int)$stroketYear > 0) {
            $parts[] = (string)(int)$stroketYear;
        }
        if ($stroketReason !== '') {
            $parts[] = $stroketReason;
        }
        $objectRows[] = ['label' => 'Stroket', 'value' => implode(' ', $parts)];
    }
    if (!empty($objectRows)) {
        $detailSections[] = [
            'title' => 'Objekt',
            'rows' => $objectRows,
        ];
    }

    $specRows = [];
    $verftParts = [];
    if (!empty($detailData['verft_name'])) {
        $verftParts[] = $detailData['verft_name'];
    }
    if (!empty($detailData['byggenr'])) {
        $verftParts[] = 'Byggenr ' . $detailData['byggenr'];
    }
    if (!empty($verftParts)) {
        $specRows[] = ['label' => 'Verft', 'value' => implode(', ', $verftParts)];
    }
    $funcParts = [];
    if (!empty($detailData['function_type'])) {
        $funcParts[] = $detailData['function_type'];
    }
    if (!empty($detailData['function_detail'])) {
        $funcParts[] = $detailData['function_detail'];
    }
    if (!empty($funcParts)) {
        $specRows[] = ['label' => 'Funksjon', 'value' => implode(' ', $funcParts)];
    }
    if (!empty($detailData['drive_type'])) {
        $specRows[] = ['label' => 'Fremdrift', 'value' => $detailData['drive_type']];
    }
    if (!empty($detailData['rigg'])) {
        $specRows[] = ['label' => 'Rigg', 'value' => $detailData['rigg']];
    }
    if (!empty($detailData['hull_type'])) {
        $specRows[] = ['label' => 'Skrogtype', 'value' => $detailData['hull_type']];
    }
    if ($detailData['tonnage'] !== null && $detailData['tonnage'] !== '') {
        $tonnageValue = (float)$detailData['tonnage'];
        $specRows[] = ['label' => 'Tonn (Brt)', 'value' => number_format($tonnageValue, 0, ',', ' ')];
    }
    $motorParts = [];
    if (!empty($detailData['motor_type'])) {
        $motorParts[] = $detailData['motor_type'];
    }
    if (!empty($detailData['motor_eff'])) {
        $motorParts[] = $detailData['motor_eff'] . ' HK';
    }
    if ($detailData['max_speed'] !== null && $detailData['max_speed'] !== '') {
        $motorParts[] = 'Max fart ca ' . trim((string)$detailData['max_speed']);
    }
    if (!empty($motorParts)) {
        $specRows[] = ['label' => 'Motor', 'value' => implode(', ', $motorParts)];
    }
    $length = $detailData['length'];
    $breadth = $detailData['breadth'];
    $draft = $detailData['draft'];
    if (($length !== null && $length !== '') || ($breadth !== null && $breadth !== '') || ($draft !== null && $draft !== '')) {
        $dimensions = [];
        foreach ([$length, $breadth, $draft] as $dim) {
            $dim = trim((string)$dim);
            if ($dim === '') {
                continue;
            }
            $dimensions[] = $dim;
        }
        $specRows[] = ['label' => 'Lengde/Bredde/dypg (fot)', 'value' => implode(' / ', $dimensions)];
    }
    if (!empty($specRows)) {
        $detailSections[] = [
            'title' => 'Spesifikasjoner',
            'rows' => $specRows,
        ];
    }
}

include('../../includes/header.php');
?>
<main class="page-main">
  <div class="container">
    <div class="card vessel-detail-card">
      <div class="card-content vessel-detail-card__content">
        <h1 class="page-title"><?= h($page_title) ?></h1>
        <div class="vessel-detail-grid">
          <aside class="vessel-detail-grid__sidebar">
            <h2 class="vessel-timeline__title">Hendelser</h2>
            <?php if (empty($timelineRows)): ?>
              <p class="vessel-timeline__empty">Ingen hendelser registrert for dette fartøyet.</p>
            <?php else: ?>
              <div class="vessel-timeline__table-wrap">
                <table class="timeline-table">
                  <thead>
                    <tr>
                      <th>Navn</th><th>År/Mnd</th><th>Hendelse</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php foreach ($timelineRows as $row): ?>
                    <?php
                      $isActive = $selectedTidId !== null && $row['tid_id'] === $selectedTidId;
                      $linkParams = [
                          'id' => !empty($row['fart_id']) ? $row['fart_id'] : $fartoyId,
                          'tid' => $row['tid_id'],
                      ];
                      if ($row['obj_id'] !== null) {
                          $linkParams['obj_id'] = $row['obj_id'];
                      } elseif ($selectedObjId !== null) {
                          $linkParams['obj_id'] = $selectedObjId;
                      }
                      $rowUrl = url('openfil/detalj/fartoy_detalj.php') . '?' . http_build_query($linkParams);
                    ?>
                    <tr class="<?= $isActive ? 'is-active' : ''; ?>">
                      <td>
                        <a class="timeline-table__link" href="<?= h($rowUrl); ?>"<?= $isActive ? ' aria-current="page"' : ''; ?>>
                          <?= h($row['display_name']); ?>
                        </a>
                      </td>
                      <td>
                        <a class="timeline-table__link timeline-table__link--muted" href="<?= h($rowUrl); ?>">
                          <?= h($row['year_month']); ?>
                        </a>
                      </td>
                      <td>
                        <a class="timeline-table__link timeline-table__link--muted" href="<?= h($rowUrl); ?>">
                          <?= h($row['event_label']); ?>
                        </a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php endif; ?>
          </aside>
          <section class="vessel-detail-grid__content">
            <?php if (!empty($detailSections)): ?>
              <?php foreach ($detailSections as $section): ?>
                <div class="vessel-detail-section">
                  <h2 class="vessel-detail-section__title"><?= h($section['title']); ?></h2>
                  <dl class="detail-list detail-list--stacked">
                    <?php foreach ($section['rows'] as $row): ?>
                      <dt><?= h($row['label']); ?></dt>
                      <dd>
                        <?php if (!empty($row['is_link'])): ?>
                          <a href="<?= h($row['value']); ?>" target="_blank" rel="noopener">
                            <?= h($row['link_text'] ?? $row['value']); ?>
                          </a>
                        <?php else: ?>
                          <?= h($row['value']); ?>
                        <?php endif; ?>
                      </dd>
                    <?php endforeach; ?>
                  </dl>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <p class="vessel-detail-empty">Ingen detaljer tilgjengelig for valgt hendelse.</p>
            <?php endif; ?>
          </section>
        </div>
      </div>
    </div>
  </div>
</main>
<?php include('../../includes/footer.php'); ?>
