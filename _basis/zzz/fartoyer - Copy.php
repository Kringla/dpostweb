<?php
require_once('../includes/bootstrap.php');
$page_title = 'Fartoy';

$db = db();

$vessels = [];
$vesselSql = "
  SELECT
    fn.FartID AS vessel_id,
    MAX(COALESCE(NULLIF(TRIM(fn.FartNavn), ''), CONCAT('Fartoy ', fn.FartID))) AS name,
    MAX(COALESCE(NULLIF(TRIM(ft.TypeFork), ''), '')) AS vessel_type,
    MIN(NULLIF(fn.YearNavn, 0)) AS first_year,
    MAX(NULLIF(fn.YearNavn, 0)) AS last_year,
    MAX(COALESCE(NULLIF(TRIM(r.RedNavn), ''), '')) AS rederi,
    MAX(COALESCE(NULLIF(TRIM(n.Nasjon), ''), '')) AS nation,
    MAX(COALESCE(NULLIF(TRIM(t.RegHavn), ''), '')) AS home_port,
    MAX(CASE WHEN fn.ObjID IS NULL OR fn.ObjID = 0 THEN NULL ELSE fn.ObjID END) AS obj_id,
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
            $name = 'Fartoy ' . (int)$row['vessel_id'];
        }

        $vessels[] = [
            'id' => (int)$row['vessel_id'],
            'name' => $name,
            'type' => trim((string)$row['vessel_type']),
            'first_year' => ($row['first_year'] !== null && (int)$row['first_year'] !== 0) ? (int)$row['first_year'] : null,
            'last_year' => ($row['last_year'] !== null && (int)$row['last_year'] !== 0) ? (int)$row['last_year'] : null,
            'rederi' => trim((string)$row['rederi']),
            'nation' => trim((string)$row['nation']),
            'home_port' => trim((string)$row['home_port']),
            'obj_id' => isset($row['obj_id']) ? (int)$row['obj_id'] : null,
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
        AND TRIM(COALESCE(fl.Link, '')) <> ''
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
                $url = trim((string)($row['url'] ?? ''));

                if ($url === '') {
                    continue;
                }

                if ($label === '') {
                    $label = preg_replace('~^https?://(www\.)?~i', '', $url);
                    $label = trim((string)$label);
                }

                if ($label === '') {
                    $label = 'Lenke';
                }

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

        <div class="dual-table-layout">
          <section class="dual-table-panel dual-table-panel--primary">
            <div class="table-wrap" data-table-wrap>
              <div id="fartoyer-pagination-top" class="table-pagination" data-pagination="top"></div>
              <div class="table-scroll">
                <table id="table-fartoyer" class="data-table" data-list-table data-rows-per-page="25" data-empty-message="Ingen treff.">
                  <thead>
                    <tr>
                      <th class="name-col">Navn</th><th>Type</th><th>År</th><th>Rederi</th><th class="count-col">Artikler</th><th class="count-col">Bilder</th><th class="action-col">Detaljer</th>
                    </tr>
                    <tr class="filter-row">
                      <th><input type="search" class="column-filter" data-filter-column="0" data-filter-mode="contains" placeholder="Søk navn" aria-label="Søk i navn" /></th>
                      <th><input type="search" class="column-filter" data-filter-column="1" data-filter-mode="contains" placeholder="Søk type" aria-label="Søk i typer" /></th>
                      <th><input type="search" class="column-filter" data-filter-column="2" data-filter-mode="startsWith" placeholder="Søk år" aria-label="Søk i år" /></th>
                      <th><input type="search" class="column-filter" data-filter-column="3" data-filter-mode="contains" placeholder="Søk rederi" aria-label="Søk i rederier" /></th>
                      <th></th>
                      <th></th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php if (empty($vessels)): ?>
                    <tr data-empty-row="true"><td colspan="7">Ingen fartøy funnet.</td></tr>
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
                        $years = [];
                        if ($vessel['first_year'] !== null) {
                            $years[] = (string)$vessel['first_year'];
                        }
                        if ($vessel['last_year'] !== null && $vessel['last_year'] !== $vessel['first_year']) {
                            $years[] = (string)$vessel['last_year'];
                        }
                        $yearText = empty($years) ? '' : implode(' - ', $years);
                      ?>
                      <tr id="<?php echo h($rowId); ?>" class="<?php echo $isSelected ? 'is-selected' : ''; ?>">
                        <td class="name-col"><?php echo h($vessel['name']); ?></td>
                        <td><?php echo h($vessel['type']); ?></td>
                        <td><?php echo h($yearText); ?></td>
                        <td><?php echo h($vessel['rederi']); ?></td>
                        <td class="count-col"><?php echo h($vessel['article_count']); ?></td>
                        <td class="count-col"><?php echo h($vessel['image_count']); ?></td>
                        <td class="action-col">
                          <a
                            class="author-select-link"
                            data-detail-param="fartoy_id"
                            data-detail-id="<?php echo (int)$vessel['id']; ?>"
                            data-detail-base="<?php echo h(url('openfil/fartoyer.php')); ?>"
                            data-detail-anchor="<?php echo h($rowId); ?>"
                            href="<?php echo h($selectUrl); ?>"<?php echo $isSelected ? ' aria-current="page"' : ''; ?>>
                            Vis
                          </a>
                        </td>
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
                Innhold<?php if ($selectedVesselData !== null) { echo ' om ' . h($selectedVesselData['name']); } ?>
              </h2>

              <?php if ($selectedVesselData !== null): ?>
                <dl class="detail-list">
                  <div class="detail-list__item"><dt>Type</dt><dd><?php echo h($selectedVesselData['type']); ?></dd></div>
                  <div class="detail-list__item"><dt>Rederi</dt><dd><?php echo h($selectedVesselData['rederi']); ?></dd></div>
                  <div class="detail-list__item"><dt>Nasjon</dt><dd><?php echo h($selectedVesselData['nation']); ?></dd></div>
                  <div class="detail-list__item"><dt>Hjemmehavn</dt><dd><?php echo h($selectedVesselData['home_port']); ?></dd></div>
                  <div class="detail-list__item"><dt>Kallesignal</dt><dd><?php echo h($selectedVesselData['call_sign']); ?></dd></div>
                </dl>
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
                        <tr data-empty-row="true"><td colspan="4">Ingen fartøy valgt.</td></tr>
                      <?php elseif (empty($vesselArticles)): ?>
                        <tr data-empty-row="true"><td colspan="4">Ingen artikler registrert for dette fartøyet.</td></tr>
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
                        <tr data-empty-row="true"><td colspan="4">Ingen fartøy valgt.</td></tr>
                      <?php elseif (empty($vesselImages)): ?>
                        <tr data-empty-row="true"><td colspan="4">Ingen bilder registrert for dette fartøyet.</td></tr>
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

              <?php if (!empty($vesselLinks)): ?>
              <div class="secondary-section">
                <h3>Lenker</h3>
                <ul>
                  <?php foreach ($vesselLinks as $link): ?>
                    <li><a href="<?php echo h($link['url']); ?>" target="_blank" rel="noopener"><?php echo h($link['label']); ?></a></li>
                  <?php endforeach; ?>
                </ul>
              </div>
              <?php endif; ?>

            </div>
          </section>
        </div>

      </div>
    </div>
  </div>
</main>

<?php include('../includes/footer.php'); ?>
