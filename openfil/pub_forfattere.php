<?php
require_once('../includes/bootstrap.php');

$page_title = 'Publikasjonsforfattere';
$activeNav = 'pubauthors';
$db = db();

$pdfBaseUrl = 'https://ihlen.net/installs/DPfilerPDF/';

$authors = [];
$authorSql = "
  SELECT
    f.PubForfID AS author_id,
    COALESCE(
      NULLIF(TRIM(f.PubForfNavn), ''),
      NULLIF(
        TRIM(
          CONCAT_WS(' ',
            NULLIF(TRIM(f.PubFNavn), ''),
            NULLIF(TRIM(f.PubENavn), '')
          )
        ),
        ''
      ),
      CONCAT('Forfatter ', f.PubForfID)
    ) AS name,
    COUNT(DISTINCT x.PubID) AS publication_count,
    NULLIF(TRIM(n.Nasjon), '') AS country
  FROM tblPubForfatter f
  LEFT JOIN tblxPubForf x ON x.PubForfID = f.PubForfID
  LEFT JOIN tblzNasjon n ON n.NaID = f.NaID
  GROUP BY f.PubForfID, f.PubForfNavn, f.PubFNavn, f.PubENavn, n.Nasjon
  HAVING publication_count > 0
  ORDER BY name ASC, author_id ASC
";

$authorResult = $db->query($authorSql);
if ($authorResult instanceof mysqli_result) {
    while ($row = $authorResult->fetch_assoc()) {
        $authors[] = [
            'id' => (int)$row['author_id'],
            'name' => trim((string)$row['name']),
            'count' => (int)$row['publication_count'],
            'country' => trim((string)($row['country'] ?? '')),
        ];
    }
    $authorResult->free();
}

$selectedAuthorId = filter_input(INPUT_GET, 'forfatter_id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$selectedAuthorId = $selectedAuthorId !== false ? $selectedAuthorId : null;

$selectedAuthor = null;
if (!empty($authors)) {
    $found = false;
    if ($selectedAuthorId !== null) {
        foreach ($authors as $entry) {
            if ($entry['id'] === $selectedAuthorId) {
                $selectedAuthor = $entry;
                $found = true;
                break;
            }
        }
    }

    if (!$found) {
        $selectedAuthor = $authors[0];
        $selectedAuthorId = $selectedAuthor['id'];
    }
}

$authorDetail = null;
$authorPublications = [];
$authorPublicationIssues = [];

if ($selectedAuthorId !== null) {
    $detailSql = "
      SELECT
        f.PubForfID,
        NULLIF(TRIM(f.PubForfNavn), '') AS name,
        NULLIF(TRIM(f.PubFNavn), '') AS first_name,
        NULLIF(TRIM(f.PubENavn), '') AS last_name,
        NULLIF(TRIM(n.Nasjon), '') AS country
      FROM tblPubForfatter f
      LEFT JOIN tblzNasjon n ON n.NaID = f.NaID
      WHERE f.PubForfID = ?
      LIMIT 1
    ";
    $detailStmt = $db->prepare($detailSql);
    if ($detailStmt instanceof mysqli_stmt) {
        $detailStmt->bind_param('i', $selectedAuthorId);
        $detailStmt->execute();
        $detailResult = $detailStmt->get_result();
        $authorDetail = $detailResult ? $detailResult->fetch_assoc() : null;
        $detailStmt->close();
    }

    $publicationSql = "
      SELECT
        p.PubID,
        NULLIF(TRIM(p.PubTittel), '') AS title,
        NULLIF(TRIM(p.PubTittelSub), '') AS subtitle,
        NULLIF(TRIM(p.Forlag), '') AS publisher,
        p.UtgittYear,
        NULLIF(TRIM(p.ISBN), '') AS isbn
      FROM tblxPubForf x
      INNER JOIN tblPublikasjon p ON p.PubID = x.PubID
      WHERE x.PubForfID = ?
      GROUP BY p.PubID, p.PubTittel, p.PubTittelSub, p.Forlag, p.UtgittYear, p.ISBN
      ORDER BY
        CASE WHEN NULLIF(TRIM(p.PubTittel), '') IS NULL THEN 1 ELSE 0 END,
        p.PubTittel ASC,
        p.PubID ASC
    ";
    $publicationStmt = $db->prepare($publicationSql);
    if ($publicationStmt instanceof mysqli_stmt) {
        $publicationStmt->bind_param('i', $selectedAuthorId);
        $publicationStmt->execute();
        $publicationResult = $publicationStmt->get_result();
        if ($publicationResult instanceof mysqli_result) {
            while ($row = $publicationResult->fetch_assoc()) {
                $title = trim((string)($row['title'] ?? ''));
                if ($title === '') {
                    $title = 'Publikasjon ' . (int)$row['PubID'];
                }

                $subtitle = trim((string)($row['subtitle'] ?? ''));
                $displayTitle = $title;
                if ($subtitle !== '') {
                    $displayTitle .= ' — ' . $subtitle;
                }

                $authorPublications[] = [
                    'id' => (int)$row['PubID'],
                    'title' => $displayTitle,
                    'year' => is_numeric($row['UtgittYear']) ? (int)$row['UtgittYear'] : null,
                    'publisher' => trim((string)($row['publisher'] ?? '')),
                    'isbn' => trim((string)($row['isbn'] ?? '')),
                ];
            }
            $publicationResult->free();
        }
        $publicationStmt->close();
    }

    $issueSql = "
      SELECT
        p.PubID,
        COALESCE(
          NULLIF(TRIM(p.PubTittel), ''),
          CONCAT('Publikasjon ', p.PubID)
        ) AS title,
        b.Year,
        b.BladNr,
        a.Side AS page_number,
        b.Lenke
      FROM tblxPubForf x
      INNER JOIN tblPublikasjon p ON p.PubID = x.PubID
      LEFT JOIN tblxArtPub ap ON ap.PubID = p.PubID
      LEFT JOIN tblArtikkel a ON a.ArtID = ap.ArtID
      LEFT JOIN tblBlad b ON b.BladID = a.BladID
      WHERE x.PubForfID = ?
      ORDER BY
        COALESCE(b.Year, 0) DESC,
        COALESCE(b.BladNr, 0) ASC,
        COALESCE(a.Side, 0) ASC,
        title ASC
    ";
    $issueStmt = $db->prepare($issueSql);
    if ($issueStmt instanceof mysqli_stmt) {
        $issueStmt->bind_param('i', $selectedAuthorId);
        $issueStmt->execute();
        $issueResult = $issueStmt->get_result();
        if ($issueResult instanceof mysqli_result) {
            while ($row = $issueResult->fetch_assoc()) {
                $lenke = trim((string)($row['Lenke'] ?? ''));
                $lenkeHref = '';
                $lenkeText = '';
                if ($lenke !== '') {
                    if (preg_match('~^https?://~i', $lenke)) {
                        $lenkeHref = $lenke;
                    } else {
                        $lenkeHref = rtrim($pdfBaseUrl, '/') . '/' . ltrim($lenke, '/');
                    }
                    $basename = basename($lenke);
                    $lenkeText = $basename !== '' ? $basename : $lenke;
                }

                $issueParts = [];
                if (!empty($row['Year'])) {
                    $issueParts[] = (string)$row['Year'];
                }
                if (!empty($row['BladNr'])) {
                    $issueParts[] = 'nr ' . (int)$row['BladNr'];
                }
                $issueLabel = implode(' ', $issueParts);

                $authorPublicationIssues[] = [
                    'title' => trim((string)($row['title'] ?? '')),
                    'issue' => $issueLabel,
                    'page' => ($row['page_number'] !== null && (int)$row['page_number'] !== 0) ? (string)$row['page_number'] : '',
                    'link_href' => $lenkeHref,
                    'link_text' => $lenkeText !== '' ? $lenkeText : 'Åpne',
                ];
            }
            $issueResult->free();
        }
        $issueStmt->close();
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
              <div class="table-pagination" data-pagination="top"></div>
              <div class="table-scroll">
                <table id="table-pub-forfattere"
                  class="data-table"
                  data-list-table
                  data-rows-per-page="25"
                  data-empty-message="Ingen treff.">
                  <thead>
                    <tr>
                      <th>Forfatter</th>
                      <th>Publikasjoner</th>
                      <th>Land</th>
                    </tr>
                    <tr class="filter-row">
                      <th><input type="search" class="column-filter" data-filter-column="0" data-filter-mode="contains" placeholder="Søk navn" aria-label="Søk i forfatter" /></th>
                      <th><input type="search" class="column-filter" data-filter-column="1" data-filter-mode="equals" placeholder="Antall" aria-label="Filtrer på antall publikasjoner" /></th>
                      <th><input type="search" class="column-filter" data-filter-column="2" data-filter-mode="contains" placeholder="Søk land" aria-label="Søk i land" /></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (empty($authors)): ?>
                      <tr data-empty-row="true"><td colspan="3">Ingen forfattere funnet.</td></tr>
                    <?php else: ?>
                      <?php foreach ($authors as $entry): ?>
                        <?php
                          $isSelected = ($entry['id'] === $selectedAuthorId);
                          $rowId = 'pub-forfatter-' . $entry['id'];
                          $queryParams = $_GET;
                          $queryParams['forfatter_id'] = $entry['id'];
                          $selectUrl = url('openfil/pub_forfattere.php');
                          if (!empty($queryParams)) {
                              $selectUrl .= '?' . http_build_query($queryParams);
                          }
                          $selectUrl .= '#' . $rowId;
                        ?>
                        <tr id="<?php echo h($rowId); ?>" class="<?php echo $isSelected ? 'is-selected' : ''; ?>">
                          <td>
                            <a class="navigable-link" href="<?php echo h($selectUrl); ?>"<?php echo $isSelected ? ' aria-current="page"' : ''; ?>>
                              <?php echo h($entry['name']); ?>
                            </a>
                          </td>
                          <td><?php echo h((string)$entry['count']); ?></td>
                          <td><?php echo $entry['country'] !== '' ? h($entry['country']) : '-'; ?></td>
                        </tr>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
              <div class="table-pagination" data-pagination="bottom"></div>
            </div>
          </section>

          <section class="dual-table-panel dual-table-panel--secondary">
            <div class="secondary-card">
              <h2 class="secondary-title">Detaljer</h2>
              <?php if ($authorDetail === null): ?>
                <p>Ingen forfatter valgt.</p>
              <?php else: ?>
                <dl class="detail-list">
                  <dt>Navn</dt>
                  <dd><?php echo h($authorDetail['name'] ?? $selectedAuthor['name'] ?? ('Forfatter ' . $selectedAuthorId)); ?></dd>
                  <dt>Fornavn</dt>
                  <dd><?php echo !empty($authorDetail['first_name']) ? h($authorDetail['first_name']) : '-'; ?></dd>
                  <dt>Etternavn</dt>
                  <dd><?php echo !empty($authorDetail['last_name']) ? h($authorDetail['last_name']) : '-'; ?></dd>
                  <dt>Land</dt>
                  <dd><?php echo !empty($authorDetail['country']) ? h($authorDetail['country']) : '-'; ?></dd>
                  <dt>Antall publikasjoner</dt>
                  <dd><?php echo h((string)($selectedAuthor['count'] ?? 0)); ?></dd>
                </dl>

                <h3>Publikasjoner</h3>
                <div class="table-wrap table-wrap--static">
                  <div class="table-scroll">
                    <table class="data-table data-table--compact">
                      <thead>
                        <tr>
                          <th>Tittel</th>
                          <th>År</th>
                          <th>Forlag</th>
                          <th>ISBN</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php if (empty($authorPublications)): ?>
                          <tr><td colspan="4">Ingen publikasjoner registrert.</td></tr>
                        <?php else: ?>
                          <?php foreach ($authorPublications as $publication): ?>
                            <tr>
                              <td><?php echo h($publication['title']); ?></td>
                              <td><?php echo $publication['year'] !== null ? h((string)$publication['year']) : '-'; ?></td>
                              <td><?php echo $publication['publisher'] !== '' ? h($publication['publisher']) : '-'; ?></td>
                              <td><?php echo $publication['isbn'] !== '' ? h($publication['isbn']) : '-'; ?></td>
                            </tr>
                          <?php endforeach; ?>
                        <?php endif; ?>
                      </tbody>
                    </table>
                  </div>
                </div>

                <h3>Omtale i Dampskibsposten</h3>
                <div class="table-wrap table-wrap--static">
                  <div class="table-scroll">
                    <table class="data-table data-table--compact">
                      <thead>
                        <tr>
                          <th>Publikasjon</th>
                          <th>Utgave</th>
                          <th>Side</th>
                          <th>Lenke</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php if (empty($authorPublicationIssues)): ?>
                          <tr><td colspan="4">Ingen omtale registrert.</td></tr>
                        <?php else: ?>
                          <?php foreach ($authorPublicationIssues as $issue): ?>
                            <tr>
                              <td><?php echo h($issue['title']); ?></td>
                              <td><?php echo $issue['issue'] !== '' ? h($issue['issue']) : '-'; ?></td>
                              <td><?php echo $issue['page'] !== '' ? h($issue['page']) : '-'; ?></td>
                              <td>
                                <?php if ($issue['link_href'] !== ''): ?>
                                  <a href="<?php echo h($issue['link_href']); ?>" target="_blank" rel="noopener">
                                    <?php echo h($issue['link_text']); ?>
                                  </a>
                                <?php else: ?>
                                  -
                                <?php endif; ?>
                              </td>
                            </tr>
                          <?php endforeach; ?>
                        <?php endif; ?>
                      </tbody>
                    </table>
                  </div>
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
