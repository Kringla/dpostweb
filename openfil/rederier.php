<?php
require_once('../includes/bootstrap.php');
$page_title = 'Rederier';

$db = db();

$rederier = [];
$rederiSql = "
  SELECT
    r.RedID AS rederi_id,
    COALESCE(NULLIF(TRIM(r.RedNavn), ''), CONCAT('Rederi ', r.RedID)) AS name,
    COALESCE(NULLIF(TRIM(r.Sted), ''), '') AS location,
    COALESCE(n.Nasjon, '') AS nation,
    COALESCE(r.RegHavn, '') AS reg_harbor,
    COALESCE(r.RedSenere, '') AS later_name,
    COUNT(DISTINCT CASE WHEN fn.FartID IS NOT NULL THEN fn.FartID END) AS vessel_count
  FROM tblRederi r
  LEFT JOIN tblFartTid ft ON ft.RedID = r.RedID AND (ft.Navning IS NULL OR ft.Navning <> 0)
  LEFT JOIN tblFartNavn fn ON fn.FartID = ft.FartID AND (fn.AntDP IS NULL OR fn.AntDP > 0)
  LEFT JOIN tblzNasjon n ON n.NaID = r.NaID
  WHERE r.RedID > 1
  GROUP BY r.RedID, r.RedNavn, r.Sted, n.Nasjon, r.RegHavn, r.RedSenere
  ORDER BY name ASC
";

$result = $db->query($rederiSql);
if ($result instanceof mysqli_result) {
    while ($row = $result->fetch_assoc()) {
        $name = trim((string)($row['name'] ?? ''));
        if ($name === '') {
            $name = 'Rederi ' . (int)$row['rederi_id'];
        }

        $rederier[] = [
            'id' => (int)$row['rederi_id'],
            'name' => $name,
            'location' => trim((string)($row['location'] ?? '')),
            'nation' => trim((string)($row['nation'] ?? '')),
            'reg_harbor' => trim((string)($row['reg_harbor'] ?? '')),
            'later_name' => trim((string)($row['later_name'] ?? '')),
            'vessel_count' => (int)($row['vessel_count'] ?? 0),
        ];
    }
    $result->free();
}

$selectedRederiId = filter_input(INPUT_GET, 'rederi_id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$selectedRederi = null;

if (!empty($rederier)) {
    if ($selectedRederiId === null) {
        $selectedRederi = $rederier[0];
        $selectedRederiId = $selectedRederi['id'];
    } else {
        foreach ($rederier as $rederi) {
            if ($rederi['id'] === $selectedRederiId) {
                $selectedRederi = $rederi;
                break;
            }
        }
        if ($selectedRederi === null) {
            $selectedRederi = $rederier[0];
            $selectedRederiId = $selectedRederi['id'];
        }
    }
}

$vessels = [];

if ($selectedRederiId !== null) {
    $vesselSql = "
      SELECT
        fn.FartID AS vessel_id,
        MAX(COALESCE(NULLIF(TRIM(fn.FartNavn), ''), CONCAT('Fartøy ', fn.FartID))) AS name,
        MIN(NULLIF(fn.YearNavn, 0)) AS first_year,
        MAX(NULLIF(fn.YearNavn, 0)) AS last_year,
        MAX(COALESCE(zft.TypeFork, '')) AS vessel_type,
        MAX(COALESCE(ft.RegHavn, '')) AS reg_harbor
      FROM tblFartTid ft
      JOIN tblFartNavn fn ON fn.FartID = ft.FartID AND (fn.AntDP IS NULL OR fn.AntDP > 0)
      LEFT JOIN tblzFartType zft ON zft.FTID = fn.FTIDNavn
      WHERE ft.RedID = ?
        AND (ft.Navning IS NULL OR ft.Navning <> 0)
      GROUP BY fn.FartID
      ORDER BY (first_year IS NULL), first_year, name
    ";

    $stmt = $db->prepare($vesselSql);
    if ($stmt instanceof mysqli_stmt) {
        $stmt->bind_param('i', $selectedRederiId);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res instanceof mysqli_result) {
            while ($row = $res->fetch_assoc()) {
                $first = $row['first_year'];
                $last = $row['last_year'];
                $period = '';
                if ($first !== null && $first !== '' && (int)$first !== 0) {
                    $period = (string)(int)$first;
                }
                if ($last !== null && $last !== '' && (int)$last !== 0) {
                    if ($period !== '' && (int)$last !== (int)$first) {
                        $period .= '–' . (int)$last;
                    } elseif ($period === '') {
                        $period = (string)(int)$last;
                    }
                }

                $vessels[] = [
                    'id' => (int)$row['vessel_id'],
                    'name' => trim((string)($row['name'] ?? '')) ?: ('Fartøy ' . (int)$row['vessel_id']),
                    'type' => trim((string)($row['vessel_type'] ?? '')),
                    'period' => $period,
                    'reg_harbor' => trim((string)($row['reg_harbor'] ?? '')),
                ];
            }
            $res->free();
        }
        $stmt->close();
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
            <a class="btn" href="<?php echo h(url('openfil/rederier.php')); ?>" data-reset-table="<?php echo h(url('openfil/rederier.php')); ?>">Tilbake</a>
            <a class="btn" href="<?php echo h(url('index.php')); ?>">Til landingssiden</a>
          </div>
        </div>

        <div class="dual-table-layout">
          <section class="dual-table-panel dual-table-panel--primary">
            <div class="table-wrap" data-table-wrap>
              <div id="rederier-pagination-top" class="table-pagination" data-pagination="top"></div>
              <div class="table-scroll">
                <table id="table-rederier" class="data-table" data-list-table data-rows-per-page="25" data-empty-message="Ingen treff.">
                  <thead>
                    <tr>
                      <th class="name-col">Rederi</th><th>Hjemsted</th><th class="count-col">Fartøyer</th>
                    </tr>
                    <tr class="filter-row">
                      <th><input type="search" class="column-filter" data-filter-column="0" data-filter-mode="contains" placeholder="S&oslash;k rederi" aria-label="S&oslash;k i rederier" /></th>
                      <th><input type="search" class="column-filter" data-filter-column="1" data-filter-mode="contains" placeholder="S&oslash;k sted" aria-label="S&oslash;k i steder" /></th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php if (empty($rederier)): ?>
                    <tr data-empty-row="true"><td colspan="3">Ingen rederier funnet.</td></tr>
                  <?php else: ?>
                    <?php foreach ($rederier as $rederi): ?>
                      <?php
                        $isSelected = ($rederi['id'] === $selectedRederiId);
                        $rowId = 'rederi-' . $rederi['id'];
                        $queryParams = $_GET;
                        $queryParams['rederi_id'] = $rederi['id'];
                        $selectUrl = url('openfil/rederier.php');
                        if (!empty($queryParams)) {
                            $selectUrl .= '?' . http_build_query($queryParams);
                        }
                        $selectUrl .= '#' . $rowId;
                      ?>
                      <tr id="<?php echo h($rowId); ?>" class="<?php echo $isSelected ? 'is-selected' : ''; ?>">
                        <td class="name-col">
                          <a
                            class="author-select-link"
                            data-detail-param="rederi_id"
                            data-detail-id="<?php echo (int)$rederi['id']; ?>"
                            data-detail-base="<?php echo h(url('openfil/rederier.php')); ?>"
                            data-detail-anchor="<?php echo h($rowId); ?>"
                            href="<?php echo h($selectUrl); ?>"<?php echo $isSelected ? ' aria-current="page"' : ''; ?>>
                            <?php echo h($rederi['name']); ?>
                          </a>
                        </td>
                        <td><?php echo h($rederi['location']); ?></td>
                        <td class="count-col"><?php echo h($rederi['vessel_count']); ?></td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                  </tbody>
                </table>
              </div>
              <div id="rederier-pagination-bottom" class="table-pagination" data-pagination="bottom"></div>
            </div>
          </section>

          <section class="dual-table-panel dual-table-panel--secondary">
            <div class="secondary-card">
              <h2 class="secondary-title">
                Fartøyer<?php if ($selectedRederi !== null) { echo ' for ' . h($selectedRederi['name']); } ?>
              </h2>
              <?php if ($selectedRederi !== null): ?>
                <?php
                  $summary = [];
                  if ($selectedRederi['location'] !== '') {
                      $summary[] = ['label' => 'Hjemsted', 'value' => $selectedRederi['location']];
                  }
                  if ($selectedRederi['nation'] !== '') {
                      $summary[] = ['label' => 'Nasjon', 'value' => $selectedRederi['nation']];
                  }
                  if ($selectedRederi['reg_harbor'] !== '') {
                      $summary[] = ['label' => 'Registerhavn', 'value' => $selectedRederi['reg_harbor']];
                  }
                  if ($selectedRederi['later_name'] !== '') {
                      $summary[] = ['label' => 'Senere navn', 'value' => $selectedRederi['later_name']];
                  }
                  $summary[] = ['label' => 'Antall fartøyer', 'value' => (string)$selectedRederi['vessel_count']];
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
                          <th class="title-col">Fart&oslash;y</th><th>Type</th><th>Periode</th><th>Registerhavn</th>
                        </tr>
                      </thead>
                      <tbody>
                      <?php if ($selectedRederiId === null): ?>
                        <tr data-empty-row="true"><td colspan="4">Ingen rederi valgt.</td></tr>
                      <?php elseif (empty($vessels)): ?>
                        <tr data-empty-row="true"><td colspan="4">Ingen fart&oslash;y registrert for dette rederiet.</td></tr>
                      <?php else: ?>
                        <?php foreach ($vessels as $vessel): ?>
                          <tr>
                            <td class="title-col"><?php echo h($vessel['name']); ?></td>
                            <td><?php echo h($vessel['type']); ?></td>
                            <td><?php echo h($vessel['period']); ?></td>
                            <td><?php echo h($vessel['reg_harbor']); ?></td>
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

