<?php
require_once('../includes/bootstrap.php');
$page_title = 'Fartøy';

$db = db();

$vessels = [];
$vesselSql = "
  SELECT
    fn.FartID AS vessel_id,
    MAX(COALESCE(NULLIF(TRIM(fn.FartNavn), ''), CONCAT('Fartøy ', fn.FartID))) AS name,
    MAX(COALESCE(NULLIF(TRIM(ft.TypeFork), ''), '')) AS vessel_type,
    MIN(NULLIF(fn.YearNavn, 0)) AS first_year,
    MAX(NULLIF(fn.YearNavn, 0)) AS last_year,
    MAX(COALESCE(NULLIF(TRIM(r.RedNavn), ''), '')) AS rederi,
    MAX(COALESCE(NULLIF(TRIM(n.Nasjon), ''), '')) AS nation,
    MAX(COALESCE(NULLIF(TRIM(t.RegHavn), ''), '')) AS home_port,
    MAX(COALESCE(NULLIF(TRIM(t.Kallesignal), ''), '')) AS call_sign,
    (SELECT COUNT(DISTINCT af.ArtID) FROM tblxArtFart af WHERE af.FartID = fn.FartID) AS article_count,
    (SELECT COUNT(DISTINCT bf.PicID) FROM tblxBildeFart bf WHERE bf.FartID = fn.FartID) AS image_count
  FROM tblFartNavn fn
  LEFT JOIN tblFartTid t ON t.FartID = fn.FartID
  LEFT JOIN tblRederi r ON r.RedID = t.RedID
  LEFT JOIN tblzFartType ft ON ft.FTID = fn.FTIDNavn
  LEFT JOIN tblzNasjon n ON n.NaID = t.NaID
  WHERE COALESCE(fn.AntDP, 0) > 0
  GROUP BY fn.FartID
  ORDER BY name ASC, fn.FartID ASC
";

$vesselResult = $db->query($vesselSql);

if ($vesselResult instanceof mysqli_result) {
    while ($row = $vesselResult->fetch_assoc()) {
        $name = trim((string)($row['name'] ?? ''));
        if ($name === '') {
            $name = 'Fartøy ' . (int)$row['vessel_id'];
        }

        $firstYear = $row['first_year'];
        $lastYear = $row['last_year'];

        $vessels[] = [
            'id' => (int)$row['vessel_id'],
            'name' => $name,
            'type' => trim((string)$row['vessel_type']),
            'first_year' => ($firstYear !== null && (int)$firstYear !== 0) ? (int)$firstYear : null,
            'last_year' => ($lastYear !== null && (int)$lastYear !== 0) ? (int)$lastYear : null,
            'rederi' => trim((string)$row['rederi']),
            'nation' => trim((string)$row['nation']),
            'home_port' => trim((string)$row['home_port']),
            'call_sign' => trim((string)$row['call_sign']),
            'article_count' => (int)($row['article_count'] ?? 0),
            'image_count' => (int)($row['image_count'] ?? 0),
        ];
    }
    $vesselResult->free();
}

$selectedVesselId = filter_input(INPUT_GET, 'fartoy_id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$selectedVesselData = null;

if (!empty($vessels)) {
    if ($selectedVesselId === null) {
        $selectedVesselData = $vessels[0];
        $selectedVesselId = $selectedVesselData['id'];
    } else {
        foreach ($vessels as $vessel) {
            if ($vessel['id'] === $selectedVesselId) {
                $selectedVesselData = $vessel;
                break;
            }
        }
        if ($selectedVesselData === null) {
            $selectedVesselData = $vessels[0];
            $selectedVesselId = $selectedVesselData['id'];
        }
    }
}

$vesselArticles = [];
$vesselImages = [];
$vesselLinks = [];

if ($selectedVesselId !== null) {
    $articleSql = "
      SELECT
        a.ArtID AS article_id,
        COALESCE(NULLIF(TRIM(a.ArtTittel), ''), CONCAT('Artikkel ', a.ArtID)) AS title,
        MIN(b.Year) AS published_year,
        MIN(b.BladNr) AS issue_number,
        MIN(NULLIF(a.Side, 0)) AS page_number,
        a.ArtType AS art_type
      FROM tblxArtFart af
      JOIN tblArtikkel a ON a.ArtID = af.ArtID
      LEFT JOIN tblBlad b ON b.BladID = a.BladID
      WHERE af.FartID = ?
      GROUP BY a.ArtID, a.ArtTittel, a.ArtType
      ORDER BY published_year DESC, issue_number ASC, page_number ASC, title ASC
    ";

    $articleStmt = $db->prepare($articleSql);
    if ($articleStmt instanceof mysqli_stmt) {
        $articleStmt->bind_param('i', $selectedVesselId);
        $articleStmt->execute();
        $articleResult = $articleStmt->get_result();

        if ($articleResult instanceof mysqli_result) {
            while ($row = $articleResult->fetch_assoc()) {
                $title = trim((string)($row['title'] ?? ''));
                if ($title === '') {
                    $title = 'Artikkel ' . (int)$row['article_id'];
                }

                $issueParts = [];
                if (!empty($row['published_year'])) {
                    $issueParts[] = (string)$row['published_year'];
                }
                if (!empty($row['issue_number'])) {
                    $issueParts[] = 'nr ' . $row['issue_number'];
                }

                $vesselArticles[] = [
                    'id' => (int)$row['article_id'],
                    'title' => $title,
                    'issue' => implode(' ', $issueParts),
                    'page' => ($row['page_number'] !== null) ? (string)$row['page_number'] : '',
                    'type' => trim((string)($row['art_type'] ?? '')),
                ];
            }
            $articleResult->free();
        }

        $articleStmt->close();
    }

    $imageSql = "
      SELECT
        b.PicID AS image_id,
        COALESCE(
          NULLIF(TRIM(bb.PicTitBlad), ''),
          NULLIF(TRIM(b.PicMotiv), ''),
          CONCAT('Bilde ', b.PicID)
        ) AS title,
        MIN(bl.Year) AS published_year,
        MIN(bl.BladNr) AS issue_number,
        MIN(NULLIF(bb.Side, 0)) AS page_number,
        b.PicType AS pic_type
      FROM tblxBildeFart bf
      JOIN tblBilde b ON b.PicID = bf.PicID
      LEFT JOIN tblxBildeBlad bb ON bb.PicID = b.PicID
      LEFT JOIN tblBlad bl ON bl.BladID = bb.BladID
      WHERE bf.FartID = ?
      GROUP BY b.PicID, b.PicMotiv, b.PicType
      ORDER BY published_year DESC, issue_number ASC, page_number ASC, title ASC
    ";

    $imageStmt = $db->prepare($imageSql);
    if ($imageStmt instanceof mysqli_stmt) {
        $imageStmt->bind_param('i', $selectedVesselId);
        $imageStmt->execute();
        $imageResult = $imageStmt->get_result();

        if ($imageResult instanceof mysqli_result) {
            while ($row = $imageResult->fetch_assoc()) {
                $title = trim((string)($row['title'] ?? ''));
                if ($title === '') {
                    $title = 'Bilde ' . (int)$row['image_id'];
                }

                $issueParts = [];
                if (!empty($row['published_year'])) {
                    $issueParts[] = (string)$row['published_year'];
                }
                if (!empty($row['issue_number'])) {
                    $issueParts[] = 'nr ' . $row['issue_number'];
                }

                $vesselImages[] = [
                    'id' => (int)$row['image_id'],
                    'title' => $title,
                    'issue' => implode(' ', $issueParts),
                    'page' => ($row['page_number'] !== null) ? (string)$row['page_number'] : '',
                    'type' => trim((string)($row['pic_type'] ?? '')),
                ];
            }
            $imageResult->free();
        }

        $imageStmt->close();
    }

    $linkSql = "
      SELECT
        COALESCE(NULLIF(TRIM(fl.LinkInnh), ''), '') AS label,
        COALESCE(NULLIF(TRIM(fl.Link), ''), '') AS url,
        COALESCE(fl.SerNo, 999999) AS sort_order
      FROM tblxFartLink fl
      WHERE fl.FartID = ?
      ORDER BY sort_order ASC, label ASC
    ";

    $linkStmt = $db->prepare($linkSql);
    if ($linkStmt instanceof mysqli_stmt) {
        $linkStmt->bind_param('i', $selectedVesselId);
        $linkStmt->execute();
        $linkResult = $linkStmt->get_result();

        if ($linkResult instanceof mysqli_result) {
            while ($row = $linkResult->fetch_assoc()) {
                $label = trim((string)($row['label'] ?? ''));
                if ($label === '') {
                    $label = 'Lenke';
                }

                $url = trim((string)($row['url'] ?? ''));

                $vesselLinks[] = [
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
            <a class="btn" href="<?php echo h(url('openfil/fartoyer.php')); ?>" data-reset-table="<?php echo h(url('openfil/fartoyer.php')); ?>">Tilbake</a>
            <a class="btn" href="<?php echo h(url('index.php')); ?>">Til landingssiden</a>
          </div>
        </div>

        <div class="dual-table-layout">
          <section class="dual-table-panel dual-table-panel--primary">
            <div class="table-wrap" data-table-wrap>
              <div id="fartoyer-pagination-top" class="table-pagination" data-pagination="top"></div>
              <div class="table-scroll">
                <table id="table-fartoyer" class="data-table" data-list-table data-rows-per-page="25" data-empty-message="Ingen treff.">
                  <thead>
                    <tr>
                      <th class="name-col">Navn</th><th>Type</th><th>&Aring;r</th><th>Rederi</th><th class="count-col">Artikler</th><th class="count-col">Bilder</th>
                    </tr>
                    <tr class="filter-row">
                      <th><input type="search" class="column-filter" data-filter-column="0" data-filter-mode="contains" placeholder="S&oslash;k navn" aria-label="S&oslash;k i navn" /></th>
                      <th><input type="search" class="column-filter" data-filter-column="1" data-filter-mode="contains" placeholder="S&oslash;k type" aria-label="S&oslash;k i typer" /></th>
                      <th><input type="search" class="column-filter" data-filter-column="2" data-filter-mode="startsWith" placeholder="S&oslash;k &aring;r" aria-label="S&oslash;k i &aring;r" /></th>
                      <th><input type="search" class="column-filter" data-filter-column="3" data-filter-mode="contains" placeholder="S&oslash;k rederi" aria-label="S&oslash;k i rederier" /></th>
                      <th></th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php if (empty($vessels)): ?>
                    <tr data-empty-row="true"><td colspan="6">Ingen fart&oslash;y funnet.</td></tr>
                  <?php else: ?>
                    <?php foreach ($vessels as $vessel): ?>
                      <?php
                        $isSelected = ($vessel['id'] === $selectedVesselId);
                        $rowId = 'fartoy-' . $vessel['id'];
                        $queryParams = $_GET;
                        $queryParams['fartoy_id'] = $vessel['id'];
                        $selectUrl = url('openfil/fartoyer.php');
                        if (!empty($queryParams)) {
                            $selectUrl .= '?' . http_build_query($queryParams);
                        }
                        $selectUrl .= '#' . $rowId;
                        $yearText = '';
                        if ($vessel['first_year'] !== null && $vessel['last_year'] !== null && $vessel['first_year'] !== $vessel['last_year']) {
                            $yearText = $vessel['first_year'] . '-' . $vessel['last_year'];
                        } elseif ($vessel['first_year'] !== null) {
                            $yearText = (string)$vessel['first_year'];
                        } elseif ($vessel['last_year'] !== null) {
                            $yearText = (string)$vessel['last_year'];
                        }
                      ?>
                      <tr id="<?php echo h($rowId); ?>" class="<?php echo $isSelected ? 'is-selected' : ''; ?>">
                        <td class="name-col">
                          <a
                            class="author-select-link"
                            data-detail-param="fartoy_id"
                            data-detail-id="<?php echo (int)$vessel['id']; ?>"
                            data-detail-base="<?php echo h(url('openfil/fartoyer.php')); ?>"
                            data-detail-anchor="<?php echo h($rowId); ?>"
                            href="<?php echo h($selectUrl); ?>"<?php echo $isSelected ? ' aria-current="page"' : ''; ?>>
                            <?php echo h($vessel['name']); ?>
                          </a>
                        </td>
                        <td><?php echo h($vessel['type']); ?></td>
                        <td><?php echo h($yearText); ?></td>
                        <td><?php echo h($vessel['rederi']); ?></td>
                        <td class="count-col"><?php echo h($vessel['article_count']); ?></td>
                        <td class="count-col"><?php echo h($vessel['image_count']); ?></td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                  </tbody>
                </table>
              </div>
              <div id="fartoyer-pagination-bottom" class="table-pagination" data-pagination="bottom"></div>
            </div>
          </section>

          <section class="dual-table-panel dual-table-panel--secondary">
            <div class="secondary-card">
              <h2 class="secondary-title">
                Innhold<?php if ($selectedVesselData !== null) { echo ' for ' . h($selectedVesselData['name']); } ?>
              </h2>
              <?php if ($selectedVesselData !== null): ?>
                <?php
                  $summaryItems = [];
                  if ($selectedVesselData['type'] !== '') {
                      $summaryItems[] = ['label' => 'Type', 'value' => $selectedVesselData['type']];
                  }
                  if ($selectedVesselData['rederi'] !== '') {
                      $summaryItems[] = ['label' => 'Rederi', 'value' => $selectedVesselData['rederi']];
                  }
                  if ($selectedVesselData['nation'] !== '') {
                      $summaryItems[] = ['label' => 'Nasjon', 'value' => $selectedVesselData['nation']];
                  }
                  if ($selectedVesselData['home_port'] !== '') {
                      $summaryItems[] = ['label' => 'Registerhavn', 'value' => $selectedVesselData['home_port']];
                  }
                  if ($selectedVesselData['call_sign'] !== '') {
                      $summaryItems[] = ['label' => 'Kallesignal', 'value' => $selectedVesselData['call_sign']];
                  }
                  $periodText = '';
                  if ($selectedVesselData['first_year'] !== null && $selectedVesselData['last_year'] !== null && $selectedVesselData['first_year'] !== $selectedVesselData['last_year']) {
                      $periodText = $selectedVesselData['first_year'] . '-' . $selectedVesselData['last_year'];
                  } elseif ($selectedVesselData['first_year'] !== null) {
                      $periodText = (string)$selectedVesselData['first_year'];
                  } elseif ($selectedVesselData['last_year'] !== null) {
                      $periodText = (string)$selectedVesselData['last_year'];
                  }
                  if ($periodText !== '') {
                      $summaryItems[] = ['label' => 'Aktiv periode', 'value' => $periodText];
                  }
                  $summaryItems[] = ['label' => 'Antall artikler', 'value' => (string)$selectedVesselData['article_count']];
                  $summaryItems[] = ['label' => 'Antall bilder', 'value' => (string)$selectedVesselData['image_count']];
                ?>
                <?php if (!empty($summaryItems)): ?>
                <div class="selection-summary">
                  <?php foreach ($summaryItems as $item): ?>
                    <div>
                      <strong><?php echo $item['label']; ?></strong>
                      <?php echo h($item['value']); ?>
                    </div>
                  <?php endforeach; ?>
                </div>
                <?php endif; ?>
              <?php endif; ?>

              <div class="secondary-section">
                <h3>Artikler</h3>
                <div class="table-wrap table-wrap--static">
                  <div class="table-scroll">
                    <table class="data-table data-table--compact">
                      <thead>
                        <tr>
                          <th class="title-col">Tittel</th><th>Utgave</th><th>Side</th><th>Type</th>
                        </tr>
                      </thead>
                      <tbody>
                      <?php if ($selectedVesselId === null): ?>
                        <tr data-empty-row="true"><td colspan="4">Ingen fart&oslash;y valgt.</td></tr>
                      <?php elseif (empty($vesselArticles)): ?>
                        <tr data-empty-row="true"><td colspan="4">Ingen artikler registrert for dette fart&oslash;yet.</td></tr>
                      <?php else: ?>
                        <?php foreach ($vesselArticles as $article): ?>
                          <tr>
                            <td class="title-col"><?php echo h($article['title']); ?></td>
                            <td><?php echo h($article['issue']); ?></td>
                            <td><?php echo h($article['page']); ?></td>
                            <td><?php echo h($article['type']); ?></td>
                          </tr>
                        <?php endforeach; ?>
                      <?php endif; ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

              <div class="secondary-section">
                <h3>Bilder</h3>
                <div class="table-wrap table-wrap--static">
                  <div class="table-scroll">
                    <table class="data-table data-table--compact">
                      <thead>
                        <tr>
                          <th class="title-col">Tittel</th><th>Utgave</th><th>Side</th><th>Type</th>
                        </tr>
                      </thead>
                      <tbody>
                      <?php if ($selectedVesselId === null): ?>
                        <tr data-empty-row="true"><td colspan="4">Ingen fart&oslash;y valgt.</td></tr>
                      <?php elseif (empty($vesselImages)): ?>
                        <tr data-empty-row="true"><td colspan="4">Ingen bilder registrert for dette fart&oslash;yet.</td></tr>
                      <?php else: ?>
                        <?php foreach ($vesselImages as $image): ?>
                          <tr>
                            <td class="title-col"><?php echo h($image['title']); ?></td>
                            <td><?php echo h($image['issue']); ?></td>
                            <td><?php echo h($image['page']); ?></td>
                            <td><?php echo h($image['type']); ?></td>
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
                      <?php if ($selectedVesselId === null): ?>
                        <tr data-empty-row="true"><td colspan="2">Ingen fart&oslash;y valgt.</td></tr>
                      <?php elseif (empty($vesselLinks)): ?>
                        <tr data-empty-row="true"><td colspan="2">Ingen lenker registrert for dette fart&oslash;yet.</td></tr>
                      <?php else: ?>
                        <?php foreach ($vesselLinks as $link): ?>
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
