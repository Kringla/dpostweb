<?php
require_once('../includes/bootstrap.php');
$page_title = 'Forfattere';

$db = db();

$authors = [];
$authorSql = "
  SELECT
    f.ForfID AS forfatter_id,
    COALESCE(
      NULLIF(TRIM(f.ForfNavn), ''),
      NULLIF(TRIM(CONCAT_WS(' ', f.FNavn, f.ENavn)), '')
    ) AS name,
    COUNT(DISTINCT af.ArtID) AS article_count,
    COALESCE(f.Forfatter, 0) AS is_author_flag,
    COALESCE(f.Fotograf, 0) AS is_photographer_flag,
    MIN(NULLIF(bl.Year, 0)) AS first_year,
    MAX(NULLIF(bl.Year, 0)) AS last_year
  FROM tblForfatter f
  LEFT JOIN tblxArtForf af ON af.ForfID = f.ForfID
  LEFT JOIN tblArtikkel a ON a.ArtID = af.ArtID
  LEFT JOIN tblBlad bl ON bl.BladID = a.BladID
  WHERE f.ForfID > 1
    AND (f.Forfatter <> 0 OR COALESCE(f.AntArt, 0) > 0 OR af.ArtID IS NOT NULL)
  GROUP BY f.ForfID, f.ForfNavn, f.FNavn, f.ENavn, f.Forfatter, f.Fotograf
  ORDER BY name ASC
";

$authorResult = $db->query($authorSql);

if ($authorResult instanceof mysqli_result) {
    while ($row = $authorResult->fetch_assoc()) {
        $name = trim((string)($row['name'] ?? ''));
        if ($name === '') {
            $name = 'Forfatter ' . (int)$row['forfatter_id'];
        }

        $authors[] = [
            'id' => (int)$row['forfatter_id'],
            'name' => $name,
            'article_count' => (int)($row['article_count'] ?? 0),
            'first_year' => ($row['first_year'] !== null && $row['first_year'] !== '' && (int)$row['first_year'] !== 0) ? (int)$row['first_year'] : null,
            'last_year' => ($row['last_year'] !== null && $row['last_year'] !== '' && (int)$row['last_year'] !== 0) ? (int)$row['last_year'] : null,
            'is_author' => (int)$row['is_author_flag'] !== 0,
            'is_photographer' => (int)$row['is_photographer_flag'] !== 0,
        ];
    }
    $authorResult->free();
}

$selectedAuthorId = filter_input(INPUT_GET, 'forfatter_id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$selectedAuthorId = $selectedAuthorId !== false ? $selectedAuthorId : null;
$selectedAuthorData = null;

if (!empty($authors)) {
    if ($selectedAuthorId === null) {
        $selectedAuthorId = $authors[0]['id'];
        $selectedAuthorData = $authors[0];
    } else {
        foreach ($authors as $author) {
            if ($author['id'] === $selectedAuthorId) {
                $selectedAuthorData = $author;
                break;
            }
        }
        if ($selectedAuthorData === null) {
            $selectedAuthorId = $authors[0]['id'];
            $selectedAuthorData = $authors[0];
        }
    }
}

$articles = [];

if ($selectedAuthorId !== null) {
    $articleSql = "
      SELECT
        a.ArtID AS artikkel_id,
        COALESCE(NULLIF(TRIM(a.ArtTittel), ''), CONCAT('Artikkel ', a.ArtID)) AS title,
        a.ArtType AS art_type,
        b.Year AS published_year,
        b.BladNr AS issue_number,
        a.Side AS page_number
      FROM tblArtikkel a
      INNER JOIN tblxArtForf af ON af.ArtID = a.ArtID
      LEFT JOIN tblBlad b ON b.BladID = a.BladID
      WHERE af.ForfID = ?
      GROUP BY a.ArtID, a.ArtTittel, a.ArtType, b.Year, b.BladNr, a.Side
      ORDER BY b.Year DESC, b.BladNr ASC, a.Side ASC, a.ArtTittel ASC
    ";

    $articleStmt = $db->prepare($articleSql);
    if ($articleStmt instanceof mysqli_stmt) {
        $articleStmt->bind_param('i', $selectedAuthorId);
        $articleStmt->execute();
        $articleResult = $articleStmt->get_result();

        if ($articleResult instanceof mysqli_result) {
            while ($row = $articleResult->fetch_assoc()) {
                $title = trim((string)($row['title'] ?? ''));
                if ($title === '') {
                    $title = 'Artikkel ' . (int)$row['artikkel_id'];
                }

                $issueParts = [];
                if (!empty($row['published_year'])) {
                    $issueParts[] = (string)$row['published_year'];
                }
                if (!empty($row['issue_number'])) {
                    $issueParts[] = 'nr ' . $row['issue_number'];
                }

                $articles[] = [
                    'id' => (int)$row['artikkel_id'],
                    'title' => $title,
                    'issue' => implode(' ', $issueParts),
                    'page' => ($row['page_number'] !== null && (int)$row['page_number'] !== 0) ? (string)$row['page_number'] : '',
                    'type' => trim((string)($row['art_type'] ?? '')),
                ];
            }
            $articleResult->free();
        }

        $articleStmt->close();
    }
}

include('../includes/header.php');
?>
<main class="page-main">
  <div class="container">
    <div class="card">
      <div class="card-content">
        <h1 class="page-title"><?php echo h($page_title); ?></h1>

        <div class="dual-table-layout dual-table-layout--detail-wide">
          <section class="dual-table-panel dual-table-panel--primary">
            <div class="table-wrap" data-table-wrap>
              <div id="forfattere-pagination-top" class="table-pagination" data-pagination="top"></div>
              <div class="table-scroll">
                <table id="table-forfattere" class="data-table" data-list-table data-rows-per-page="25" data-empty-message="Ingen treff.">
                  <thead>
                    <tr>
                      <th class="name-col">Navn</th><th class="count-col">Artikler</th>
                    </tr>
                    <tr class="filter-row">
                      <th><input type="search" class="column-filter" data-filter-column="0" data-filter-mode="contains" placeholder="Søk navn" aria-label="Søk i navn" /></th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php if (empty($authors)): ?>
                    <tr data-empty-row="true"><td colspan="2">Ingen forfattere funnet.</td></tr>
                  <?php else: ?>
                    <?php foreach ($authors as $author): ?>
                      <?php
                        $isSelected = ($author['id'] === $selectedAuthorId);
                        $rowId = 'forfatter-' . $author['id'];
                        $queryParams = $_GET;
                        $queryParams['forfatter_id'] = $author['id'];
                        $selectUrl = url('openfil/forfattere.php');
                        if (!empty($queryParams)) {
                            $selectUrl .= '?' . http_build_query($queryParams);
                        }
                        $selectUrl .= '#' . $rowId;
                      ?>
                      <tr id="<?php echo h($rowId); ?>" class="<?php echo $isSelected ? 'is-selected' : ''; ?>">
                        <td class="name-col">
                          <a
                            class="author-select-link"
                            data-detail-param="forfatter_id"
                            data-detail-id="<?php echo (int)$author['id']; ?>"
                            data-detail-base="<?php echo h(url('openfil/forfattere.php')); ?>"
                            data-detail-anchor="<?php echo h($rowId); ?>"
                            href="<?php echo h($selectUrl); ?>"<?php echo $isSelected ? ' aria-current="page"' : ''; ?>>
                            <?php echo h($author['name']); ?>
                          </a>
                        </td>
                        <td class="count-col"><?php echo h($author['article_count']); ?></td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                  </tbody>
                </table>
              </div>
              <div id="forfattere-pagination-bottom" class="table-pagination" data-pagination="bottom"></div>
            </div>
          </section>

          <section class="dual-table-panel dual-table-panel--secondary">
            <div class="secondary-card">
              <h2 class="secondary-title">
                Artikler<?php if ($selectedAuthorData !== null) { echo ' av ' . h($selectedAuthorData['name']); } ?>
              </h2>
              <?php if ($selectedAuthorData !== null): ?>
              <div class="selection-summary">
                <div>
                  <strong>Navn</strong>
                  <?php echo h($selectedAuthorData['name']); ?>
                </div>
                <div>
                  <strong>Antall artikler</strong>
                  <?php echo h($selectedAuthorData['article_count']); ?>
                </div>
                <div>
                  <strong>Aktiv periode</strong>
                  <?php
                    $first = $selectedAuthorData['first_year'];
                    $last = $selectedAuthorData['last_year'];
                    if ($first === null && $last === null) {
                        echo 'Ukjent';
                    } elseif ($first !== null && $last !== null && $first !== $last) {
                        echo h($first) . '–' . h($last);
                    } else {
                        echo h($first ?? $last);
                    }
                  ?>
                </div>
                <div>
                  <strong>Roller</strong>
                  <?php
                    $roles = [];
                    if ($selectedAuthorData['is_author']) {
                        $roles[] = 'Forfatter';
                    }
                    if ($selectedAuthorData['is_photographer']) {
                        $roles[] = 'Fotograf';
                    }
                    echo !empty($roles) ? h(implode(', ', $roles)) : 'Ukjent';
                  ?>
                </div>
              </div>
              <?php endif; ?>
              <div class="table-wrap table-wrap--static">
                <div class="table-scroll">
                  <table class="data-table data-table--compact">
                    <thead>
                      <tr>
                        <th class="title-col">Tittel</th><th>Utgave</th><th>Side</th><th>Type</th>
                      </tr>
                    </thead>
                    <tbody>
                    <?php if ($selectedAuthorId === null): ?>
                      <tr data-empty-row="true"><td colspan="4">Ingen forfatter valgt.</td></tr>
                    <?php elseif (empty($articles)): ?>
                      <tr data-empty-row="true"><td colspan="4">Ingen artikler registrert for denne forfatteren.</td></tr>
                    <?php else: ?>
                      <?php foreach ($articles as $article): ?>
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
          </section>
        </div>

      </div>
    </div>
  </div>
</main>

<?php include('../includes/footer.php'); ?>
