<?php
require_once('../includes/bootstrap.php');
$page_title = 'Verft';

$db = db();

$yards = [];
$yardSql = "
  SELECT
    v.VerftID AS verft_id,
    COALESCE(NULLIF(TRIM(v.VerftNavn), ''), CONCAT('Verft ', v.VerftID)) AS name,
    COALESCE(NULLIF(TRIM(v.Sted), ''), '') AS location,
    COALESCE(n.Nasjon, '') AS nation,
    COALESCE(v.AndreNavn, '') AS alt_names,
    COALESCE(NULLIF(v.Etablert, 0), NULL) AS established,
    COALESCE(NULLIF(v.Nedlagt, 0), NULL) AS closed,
    COUNT(DISTINCT CASE WHEN fn.FartID IS NOT NULL THEN fn.FartID END) AS vessel_count
  FROM tblVerft v
  LEFT JOIN tblzNasjon n ON n.NaID = v.NaID
  LEFT JOIN tblFartSpes fs ON fs.VerftID = v.VerftID
  LEFT JOIN tblFartNavn fn ON fn.ObjID = fs.ObjID AND (fn.AntDP IS NULL OR fn.AntDP > 0)
  WHERE v.VerftID > 1
  GROUP BY v.VerftID, v.VerftNavn, v.Sted, n.Nasjon, v.AndreNavn, v.Etablert, v.Nedlagt
  ORDER BY name ASC
";

$result = $db->query($yardSql);
if ($result instanceof mysqli_result) {
    while ($row = $result->fetch_assoc()) {
        $name = trim((string)($row['name'] ?? ''));
        if ($name === '') {
            $name = 'Verft ' . (int)$row['verft_id'];
        }

        $yards[] = [
            'id' => (int)$row['verft_id'],
            'name' => $name,
            'location' => trim((string)($row['location'] ?? '')),
            'nation' => trim((string)($row['nation'] ?? '')),
            'alt_names' => trim((string)($row['alt_names'] ?? '')),
            'established' => $row['established'] !== null ? (int)$row['established'] : null,
            'closed' => $row['closed'] !== null ? (int)$row['closed'] : null,
            'vessel_count' => (int)($row['vessel_count'] ?? 0),
        ];
    }
    $result->free();
}

$selectedYardId = filter_input(INPUT_GET, 'verft_id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$selectedYard = null;

if (!empty($yards)) {
    if ($selectedYardId === null) {
        $selectedYard = $yards[0];
        $selectedYardId = $selectedYard['id'];
    } else {
        foreach ($yards as $yard) {
            if ($yard['id'] === $selectedYardId) {
                $selectedYard = $yard;
                break;
            }
        }
        if ($selectedYard === null) {
            $selectedYard = $yards[0];
            $selectedYardId = $selectedYard['id'];
        }
    }
}

$yardVessels = [];
$yardLinks = [];

if ($selectedYardId !== null) {
    $yardVesselSql = "
      SELECT
        fn.FartID AS vessel_id,
        MAX(COALESCE(NULLIF(TRIM(fn.FartNavn), ''), CONCAT('Fartøy ', fn.FartID))) AS name,
        MIN(NULLIF(fn.YearNavn, 0)) AS first_year,
        MAX(NULLIF(fn.YearNavn, 0)) AS last_year,
        MAX(COALESCE(zft.TypeFork, '')) AS vessel_type,
        MAX(NULLIF(fs.Byggenr, 0)) AS build_number
      FROM tblFartSpes fs
      JOIN tblFartNavn fn ON fn.ObjID = fs.ObjID AND (fn.AntDP IS NULL OR fn.AntDP > 0)
      LEFT JOIN tblzFartType zft ON zft.FTID = fn.FTIDNavn
      WHERE fs.VerftID = ?
      GROUP BY fn.FartID
    ";

    $stmt = $db->prepare($yardVesselSql);
    if ($stmt instanceof mysqli_stmt) {
        $stmt->bind_param('i', $selectedYardId);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res instanceof mysqli_result) {
            while ($row = $res->fetch_assoc()) {
                $first = $row['first_year'];
                $last = $row['last_year'];
                $firstYearVal = ($first !== null && $first !== '' && (int)$first !== 0) ? (int)$first : null;
                $lastYearVal = ($last !== null && $last !== '' && (int)$last !== 0) ? (int)$last : null;
                $period = '';
                if ($firstYearVal !== null && $lastYearVal !== null && $firstYearVal !== $lastYearVal) {
                    $period = $firstYearVal . '-' . $lastYearVal;
                } elseif ($firstYearVal !== null) {
                    $period = (string)$firstYearVal;
                } elseif ($lastYearVal !== null) {
                    $period = (string)$lastYearVal;
                }

                $buildNo = $row['build_number'];
                $buildText = ($buildNo !== null && $buildNo !== '') ? (string)(int)$buildNo : '';

                $yardVessels[] = [
                    'id' => (int)$row['vessel_id'],
                    'name' => trim((string)($row['name'] ?? '')) ?: ('Fartoy ' . (int)$row['vessel_id']),
                    'type' => trim((string)($row['vessel_type'] ?? '')),
                    'period' => $period,
                    'build_number' => $buildText,
                    'first_year_value' => $firstYearVal,
                ];
            }
            $res->free();
        }
        if (!empty($yardVessels)) {
            usort($yardVessels, function (array $a, array $b): int {
                $aYear = $a['first_year_value'] ?? null;
                $bYear = $b['first_year_value'] ?? null;
                if ($aYear === null && $bYear === null) {
                    return strcmp($a['name'], $b['name']);
                }
                if ($aYear === null) {
                    return 1;
                }
                if ($bYear === null) {
                    return -1;
                }
                $cmp = $aYear <=> $bYear;
                return $cmp !== 0 ? $cmp : strcmp($a['name'], $b['name']);
            });
            foreach ($yardVessels as &$vesselItem) {
                unset($vesselItem['first_year_value']);
            }
            unset($vesselItem);
        }
        $stmt->close();
    }

    $linkSql = "
      SELECT
        COALESCE(NULLIF(TRIM(vl.LinkInnh), ''), '') AS label,
        COALESCE(NULLIF(TRIM(vl.Link), ''), '') AS url,
        COALESCE(vl.SerNo, 999999) AS sort_order
      FROM tblxVerftLink vl
      WHERE vl.VerftID = ?
      ORDER BY sort_order ASC, label ASC
    ";

    $linkStmt = $db->prepare($linkSql);
    if ($linkStmt instanceof mysqli_stmt) {
        $linkStmt->bind_param('i', $selectedYardId);
        $linkStmt->execute();
        $linkResult = $linkStmt->get_result();

        if ($linkResult instanceof mysqli_result) {
            while ($row = $linkResult->fetch_assoc()) {
                $label = trim((string)($row['label'] ?? ''));
                if ($label === '') {
                    $label = 'Lenke';
                }

                $url = trim((string)($row['url'] ?? ''));

                $yardLinks[] = [
                    'label' => $label,
                    'url' => $url,
                ];
            }
            $linkResult->free();
        }

        $linkStmt->close();
    }
}

include('../includes/header.php');
?>
<main class="page-main">
  <div class="container">
    <div class="card">
      <div class="card-content">
        <h1 class="page-title"><?php echo h($page_title); ?></h1>

        <div class="page-toolbar">
          <div class="toolbar-actions">
            <a class="btn" href="<?php echo h(url('openfil/verft.php')); ?>" data-reset-table="<?php echo h(url('openfil/verft.php')); ?>">Tilbake</a>
            <a class="btn" href="<?php echo h(url('index.php')); ?>">Til landingssiden</a>
          </div>
        </div>

        <div class="dual-table-layout">
          <section class="dual-table-panel dual-table-panel--primary">
            <div class="table-wrap" data-table-wrap>
              <div id="verft-pagination-top" class="table-pagination" data-pagination="top"></div>
              <div class="table-scroll">
                <table id="table-verft" class="data-table" data-list-table data-rows-per-page="25" data-empty-message="Ingen treff.">
                  <thead>
                    <tr>
                      <th class="name-col">Verft</th><th>Sted</th><th class="count-col">Fart&oslash;yer</th>
                    </tr>
                    <tr class="filter-row">
                      <th><input type="search" class="column-filter" data-filter-column="0" data-filter-mode="contains" placeholder="S&oslash;k verft" aria-label="S&oslash;k i verft" /></th>
                      <th><input type="search" class="column-filter" data-filter-column="1" data-filter-mode="contains" placeholder="S&oslash;k sted" aria-label="S&oslash;k i steder" /></th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php if (empty($yards)): ?>
                    <tr data-empty-row="true"><td colspan="3">Ingen verft funnet.</td></tr>
                  <?php else: ?>
                    <?php foreach ($yards as $yard): ?>
                      <?php
                        $isSelected = ($yard['id'] === $selectedYardId);
                        $rowId = 'verft-' . $yard['id'];
                        $queryParams = $_GET;
                        $queryParams['verft_id'] = $yard['id'];
                        $selectUrl = url('openfil/verft.php');
                        if (!empty($queryParams)) {
                            $selectUrl .= '?' . http_build_query($queryParams);
                        }
                        $selectUrl .= '#' . $rowId;
                      ?>
                      <tr id="<?php echo h($rowId); ?>" class="<?php echo $isSelected ? 'is-selected' : ''; ?>">
                        <td class="name-col">
                          <a
                            class="author-select-link"
                            data-detail-param="verft_id"
                            data-detail-id="<?php echo (int)$yard['id']; ?>"
                            data-detail-base="<?php echo h(url('openfil/verft.php')); ?>"
                            data-detail-anchor="<?php echo h($rowId); ?>"
                            href="<?php echo h($selectUrl); ?>"<?php echo $isSelected ? ' aria-current="page"' : ''; ?>>
                            <?php echo h($yard['name']); ?>
                          </a>
                        </td>
                        <td><?php echo h($yard['location']); ?></td>
                        <td class="count-col"><?php echo h($yard['vessel_count']); ?></td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                  </tbody>
                </table>
              </div>
              <div id="verft-pagination-bottom" class="table-pagination" data-pagination="bottom"></div>
            </div>
          </section>

          <section class="dual-table-panel dual-table-panel--secondary">
            <div class="secondary-card">
              <h2 class="secondary-title">
                Fart&oslash;yer<?php if ($selectedYard !== null) { echo ' fra ' . h($selectedYard['name']); } ?>
              </h2>
              <?php if ($selectedYard !== null): ?>
                <?php
                  $summary = [];
                  if ($selectedYard['location'] !== '') {
                      $summary[] = ['label' => 'Sted', 'value' => $selectedYard['location']];
                  }
                  if ($selectedYard['nation'] !== '') {
                      $summary[] = ['label' => 'Nasjon', 'value' => $selectedYard['nation']];
                  }
                  if ($selectedYard['alt_names'] !== '') {
                      $summary[] = ['label' => 'Andre navn', 'value' => $selectedYard['alt_names']];
                  }
                  if ($selectedYard['established'] !== null) {
                      $summary[] = ['label' => 'Etablert', 'value' => (string)$selectedYard['established']];
                  }
                  if ($selectedYard['closed'] !== null) {
                      $summary[] = ['label' => 'Nedlagt', 'value' => (string)$selectedYard['closed']];
                  }
                  $summary[] = ['label' => 'Antall fartøyer', 'value' => (string)$selectedYard['vessel_count']];
                ?>
                <?php if (!empty($summary)): ?>
                <div class="selection-summary">
                  <?php foreach ($summary as $item): ?>
                    <div>
                      <strong><?php echo h($item['label']); ?></strong>
                      <?php echo h($item['value']); ?>
                    </div>
                  <?php endforeach; ?>
                </div>
                <?php endif; ?>
              <?php endif; ?>

              <div class="secondary-section">
                <div class="table-wrap table-wrap--static">
                  <div class="table-scroll">
                    <table class="data-table data-table--compact">
                      <thead>
                        <tr>
                          <th class="title-col">Fart&oslash;y</th><th>Type</th><th>Periode</th><th>Byggenr</th>
                        </tr>
                      </thead>
                      <tbody>
                      <?php if ($selectedYardId === null): ?>
                        <tr data-empty-row="true"><td colspan="4">Ingen verft valgt.</td></tr>
                      <?php elseif (empty($yardVessels)): ?>
                        <tr data-empty-row="true"><td colspan="4">Ingen fart&oslash;y registrert for dette verftet.</td></tr>
                      <?php else: ?>
                        <?php foreach ($yardVessels as $vessel): ?>
                          <tr>
                            <td class="title-col"><?php echo h($vessel['name']); ?></td>
                            <td><?php echo h($vessel['type']); ?></td>
                            <td><?php echo h($vessel['period']); ?></td>
                            <td><?php echo h($vessel['build_number']); ?></td>
                          </tr>
                        <?php endforeach; ?>
                      <?php endif; ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

              <div class="secondary-section">
                <h3>Lenker</h3>
                <div class="table-wrap table-wrap--static">
                  <div class="table-scroll">
                    <table class="data-table data-table--compact">
                      <thead>
                        <tr>
                          <th class="title-col">Beskrivelse</th><th>Lenke</th>
                        </tr>
                      </thead>
                      <tbody>
                      <?php if ($selectedYardId === null): ?>
                        <tr data-empty-row="true"><td colspan="2">Ingen verft valgt.</td></tr>
                      <?php elseif (empty($yardLinks)): ?>
                        <tr data-empty-row="true"><td colspan="2">Ingen lenker registrert for dette verftet.</td></tr>
                      <?php else: ?>
                        <?php foreach ($yardLinks as $link): ?>
                          <tr>
                            <td class="title-col"><?php echo h($link['label']); ?></td>
                            <td>
                              <?php if ($link['url'] !== ''): ?>
                                <a href="<?php echo h($link['url']); ?>" target="_blank" rel="noopener"><?php echo h($link['url']); ?></a>
                              <?php else: ?>
                                &ndash;
                              <?php endif; ?>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      <?php endif; ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

            </div>
          </section>
        </div>

      </div>
    </div>
  </div>
</main>

<?php include('../includes/footer.php'); ?>
