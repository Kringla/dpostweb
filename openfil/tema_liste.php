<?php
require_once __DIR__ . '/../includes/bootstrap.php';
$page_title = 'Tema';

$db = db();

$temaer = [];
$temaSql = "
  SELECT
    t.TID AS tema_id,
    COALESCE(NULLIF(TRIM(t.Tema), ''), CONCAT('Tema ', t.TID)) AS name,
    COALESCE(t.TemaInnhold, '') AS description,
    COALESCE(t.AntArt, 0) AS article_count,
    COALESCE(t.AntBilde, 0) AS image_count
  FROM tblTema t
  WHERE t.TID > 1
  ORDER BY name ASC
";

$result = $db->query($temaSql);
if ($result instanceof mysqli_result) {
    while ($row = $result->fetch_assoc()) {
        $name = trim((string)($row['name'] ?? ''));
        if ($name === '') {
            $name = 'Tema ' . (int)$row['tema_id'];
        }

        $temaer[] = [
            'id' => (int)$row['tema_id'],
            'name' => $name,
            'description' => trim((string)($row['description'] ?? '')),
            'article_count' => (int)($row['article_count'] ?? 0),
            'image_count' => (int)($row['image_count'] ?? 0),
        ];
    }
    $result->free();
}

$selectedTemaId = filter_input(INPUT_GET, 'tema_id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$selectedTema = null;

if (!empty($temaer)) {
    if ($selectedTemaId === null) {
        $selectedTema = $temaer[0];
        $selectedTemaId = $selectedTema['id'];
    } else {
        foreach ($temaer as $tema) {
            if ($tema['id'] === $selectedTemaId) {
                $selectedTema = $tema;
                break;
            }
        }
        if ($selectedTema === null) {
            $selectedTema = $temaer[0];
            $selectedTemaId = $selectedTema['id'];
        }
    }
}

$temaArticles = [];
$temaImages = [];

if ($selectedTemaId !== null) {
    $articleSql = "
      SELECT
        a.ArtID AS article_id,
        COALESCE(NULLIF(TRIM(a.ArtTittel), ''), CONCAT('Artikkel ', a.ArtID)) AS title,
        MIN(b.Year) AS published_year,
        MIN(b.BladNr) AS issue_number,
        MIN(NULLIF(a.Side, 0)) AS page_number,
        a.ArtType AS art_type
      FROM tblxArtTema at
      JOIN tblArtikkel a ON a.ArtID = at.ArtID
      LEFT JOIN tblBlad b ON b.BladID = a.BladID
      WHERE at.TID = ?
      GROUP BY a.ArtID, a.ArtTittel, a.ArtType
      ORDER BY published_year DESC, issue_number ASC, page_number ASC, title ASC
    ";

    $stmtArt = $db->prepare($articleSql);
    if ($stmtArt instanceof mysqli_stmt) {
        $stmtArt->bind_param('i', $selectedTemaId);
        $stmtArt->execute();
        $resArt = $stmtArt->get_result();
        if ($resArt instanceof mysqli_result) {
            while ($row = $resArt->fetch_assoc()) {
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

                $temaArticles[] = [
                    'id' => (int)$row['article_id'],
                    'title' => $title,
                    'issue' => implode(' ', $issueParts),
                    'page' => ($row['page_number'] !== null) ? (string)$row['page_number'] : '',
                    'type' => trim((string)($row['art_type'] ?? '')),
                ];
            }
            $resArt->free();
        }
        $stmtArt->close();
    }

    $imageSql = "
      SELECT
        b.PicID AS image_id,
        MAX(NULLIF(TRIM(bb.PicTitBlad), '')) AS blad_title,
        b.PicMotiv,
        MIN(bl.Year) AS published_year,
        MIN(bl.BladNr) AS issue_number,
        MIN(NULLIF(bb.Side, 0)) AS page_number,
        b.PicType AS pic_type
      FROM tblxBildeTema bt
      JOIN tblBilde b ON b.PicID = bt.PicID
      LEFT JOIN tblxBildeBlad bb ON bb.PicID = b.PicID
      LEFT JOIN tblBlad bl ON bl.BladID = bb.BladID
      WHERE bt.TID = ?
      GROUP BY b.PicID, b.PicMotiv, b.PicType
      ORDER BY published_year DESC, issue_number ASC, page_number ASC, blad_title ASC
    ";

    $stmtImg = $db->prepare($imageSql);
    if ($stmtImg instanceof mysqli_stmt) {
        $stmtImg->bind_param('i', $selectedTemaId);
        $stmtImg->execute();
        $resImg = $stmtImg->get_result();
        if ($resImg instanceof mysqli_result) {
            while ($row = $resImg->fetch_assoc()) {
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

                $title = trim((string)($row['blad_title'] ?? ''));
                if ($title === '') {
                    $title = trim((string)($row['PicMotiv'] ?? ''));
                }
                if ($title === '') {
                    $title = 'Bilde ' . (int)$row['image_id'];
                }

                $temaImages[] = [
                    'id' => (int)$row['image_id'],
                    'title' => $title,
                    'issue' => implode(' ', $issueParts),
                    'page' => ($row['page_number'] !== null) ? (string)$row['page_number'] : '',
                    'type' => trim((string)($row['pic_type'] ?? '')),
                ];
            }
            $resImg->free();
        }
        $stmtImg->close();
    }
}

include __DIR__ . '/../includes/header.php';
?>
<main class="page-main">
  <div class="container">
    <div class="card">
      <div class="card-content">
        <h1 class="page-title"><?php echo h($page_title); ?></h1>

        <div class="dual-table-layout dual-table-layout--even">
          <section class="dual-table-panel dual-table-panel--primary">
            <div class="table-wrap" data-table-wrap>
              <div id="tema-pagination-top" class="table-pagination" data-pagination="top"></div>
              <div class="table-scroll">
                <table id="table-tema" class="data-table" data-list-table data-rows-per-page="25" data-empty-message="Ingen treff.">
                  <thead>
                    <tr>
                      <th class="name-col">Tema</th><th class="count-col">Artikler</th><th class="count-col">Bilder</th>
                    </tr>
                    <tr class="filter-row">
                      <th><input type="search" class="column-filter" data-filter-column="0" data-filter-mode="contains" placeholder="Søk tema" aria-label="Søk i tema" /></th>
                      <th></th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php if (empty($temaer)): ?>
                    <tr data-empty-row="true"><td colspan="3">Ingen tema funnet.</td></tr>
                  <?php else: ?>
                    <?php foreach ($temaer as $tema): ?>
                      <?php
                        $isSelected = ($tema['id'] === $selectedTemaId);
                        $rowId = 'tema-' . $tema['id'];
                        $queryParams = $_GET;
                        $queryParams['tema_id'] = $tema['id'];
                        $selectUrl = url('openfil/tema_liste.php');
                        if (!empty($queryParams)) {
                            $selectUrl .= '?' . http_build_query($queryParams);
                        }
                        $selectUrl .= '#' . $rowId;
                      ?>
                      <tr id="<?php echo h($rowId); ?>" class="<?php echo $isSelected ? 'is-selected' : ''; ?>">
                        <td class="name-col">
                          <a
                            class="author-select-link"
                            data-detail-param="tema_id"
                            data-detail-id="<?php echo (int)$tema['id']; ?>"
                            data-detail-base="<?php echo h(url('openfil/tema_liste.php')); ?>"
                            data-detail-anchor="<?php echo h($rowId); ?>"
                            href="<?php echo h($selectUrl); ?>"<?php echo $isSelected ? ' aria-current="page"' : ''; ?>>
                            <?php echo h($tema['name']); ?>
                          </a>
                        </td>
                        <td class="count-col"><?php echo h($tema['article_count']); ?></td>
                        <td class="count-col"><?php echo h($tema['image_count']); ?></td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                  </tbody>
                </table>
              </div>
              <div id="tema-pagination-bottom" class="table-pagination" data-pagination="bottom"></div>
            </div>
          </section>

          <section class="dual-table-panel dual-table-panel--secondary">
            <div class="secondary-card">
              <h2 class="secondary-title">
                Innhold<?php if ($selectedTema !== null) { echo ' for ' . h($selectedTema['name']); } ?>
              </h2>
              <?php if ($selectedTema !== null): ?>
                <?php
                  $summary = [];
                  if ($selectedTema['description'] !== '') {
                      $summary[] = ['label' => 'Beskrivelse', 'value' => $selectedTema['description']];
                  }
                  $summary[] = ['label' => 'Antall artikler', 'value' => (string)$selectedTema['article_count']];
                  $summary[] = ['label' => 'Antall bilder', 'value' => (string)$selectedTema['image_count']];
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
                <h3>Artikler</h3>
                <div class="table-wrap" data-table-wrap>
                  <div id="tema-artikler-pagination-top" class="table-pagination" data-pagination="top"></div>
                  <div class="table-scroll">
                    <table
                      id="table-tema-artikler"
                      class="data-table data-table--compact tema-detail-table"
                      data-list-table
                      data-rows-per-page="20"
                      data-empty-message="Ingen artikler registrert for dette temaet."
                    >
                      <thead>
                        <tr>
                          <th class="title-col">Tittel</th><th class="col-issue">Utgave</th><th>Side</th><th>Type</th>
                        </tr>
                      </thead>
                      <tbody>
                      <?php if ($selectedTemaId === null): ?>
                        <tr data-empty-row="true"><td colspan="4">Ingen tema valgt.</td></tr>
                      <?php elseif (empty($temaArticles)): ?>
                        <tr data-empty-row="true"><td colspan="4">Ingen artikler registrert for dette temaet.</td></tr>
                      <?php else: ?>
                        <?php foreach ($temaArticles as $article): ?>
                          <tr>
                            <td class="title-col"><?php echo h($article['title']); ?></td>
                            <td class="col-issue"><?php echo h($article['issue']); ?></td>
                            <td><?php echo h($article['page']); ?></td>
                            <td><?php echo h($article['type']); ?></td>
                          </tr>
                        <?php endforeach; ?>
                      <?php endif; ?>
                      </tbody>
                    </table>
                  </div>
                  <div id="tema-artikler-pagination-bottom" class="table-pagination" data-pagination="bottom"></div>
                </div>
              </div>

              <div class="secondary-section">
                <h3>Bilder</h3>
                <div class="table-wrap" data-table-wrap>
                  <div id="tema-bilder-pagination-top" class="table-pagination" data-pagination="top"></div>
                  <div class="table-scroll">
                    <table
                      id="table-tema-bilder"
                      class="data-table data-table--compact tema-detail-table"
                      data-list-table
                      data-rows-per-page="20"
                      data-empty-message="Ingen bilder registrert for dette temaet."
                    >
                      <thead>
                        <tr>
                          <th class="title-col">Tittel</th><th class="col-issue">Utgave</th><th>Side</th><th>Type</th>
                        </tr>
                      </thead>
                      <tbody>
                      <?php if ($selectedTemaId === null): ?>
                        <tr data-empty-row="true"><td colspan="4">Ingen tema valgt.</td></tr>
                      <?php elseif (empty($temaImages)): ?>
                        <tr data-empty-row="true"><td colspan="4">Ingen bilder registrert for dette temaet.</td></tr>
                      <?php else: ?>
                        <?php foreach ($temaImages as $image): ?>
                          <tr>
                            <td class="title-col"><?php echo h($image['title']); ?></td>
                            <td class="col-issue"><?php echo h($image['issue']); ?></td>
                            <td><?php echo h($image['page']); ?></td>
                            <td><?php echo h($image['type']); ?></td>
                          </tr>
                        <?php endforeach; ?>
                      <?php endif; ?>
                      </tbody>
                    </table>
                  </div>
                  <div id="tema-bilder-pagination-bottom" class="table-pagination" data-pagination="bottom"></div>
                </div>
              </div>

            </div>
          </section>
        </div>

      </div>
    </div>
  </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
