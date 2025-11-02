<?php
require_once('../includes/bootstrap.php');

$page_title = 'Publikasjoner';
$activeNav = 'publications';
$db = db();

$pdfBaseUrl = 'https://ihlen.net/installs/DPfilerPDF/';

$publications = [];
$pubListSql = "
  SELECT
    PubID,
    NULLIF(TRIM(PubTittel), '') AS title,
    NULLIF(TRIM(PubTittelSub), '') AS subtitle,
    NULLIF(TRIM(Forlag), '') AS publisher,
    UtgittYear,
    NULLIF(TRIM(SalgWeb), '') AS sales_web
  FROM tblPublikasjon
  ORDER BY
    CASE WHEN NULLIF(TRIM(PubTittel), '') IS NULL THEN 1 ELSE 0 END,
    PubTittel ASC,
    PubID ASC
";

$pubListResult = $db->query($pubListSql);
if ($pubListResult instanceof mysqli_result) {
    while ($row = $pubListResult->fetch_assoc()) {
        $title = trim((string)($row['title'] ?? ''));
        if ($title === '') {
            $title = 'Publikasjon ' . (int)$row['PubID'];
        }

        $subtitle = trim((string)($row['subtitle'] ?? ''));
        $displayTitle = $title;
        if ($subtitle !== '') {
            $displayTitle .= ' — ' . $subtitle;
        }

        $publisher = trim((string)($row['publisher'] ?? ''));
        $year = $row['UtgittYear'];
        $salesWeb = trim((string)($row['sales_web'] ?? ''));

        $publications[] = [
            'id' => (int)$row['PubID'],
            'title' => $displayTitle,
            'base_title' => $title,
            'subtitle' => $subtitle,
            'publisher' => $publisher,
            'year' => is_numeric($year) ? (int)$year : null,
            'sales_web' => $salesWeb,
        ];
    }
    $pubListResult->free();
}

$selectedPublicationId = filter_input(INPUT_GET, 'pub_id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$selectedPublicationId = $selectedPublicationId !== false ? $selectedPublicationId : null;

$selectedPublication = null;
if (!empty($publications)) {
    $valid = false;
    if ($selectedPublicationId !== null) {
        foreach ($publications as $entry) {
            if ($entry['id'] === $selectedPublicationId) {
                $selectedPublication = $entry;
                $valid = true;
                break;
            }
        }
    }

    if (!$valid) {
        $selectedPublication = $publications[0];
        $selectedPublicationId = $selectedPublication['id'];
    }
}

$publicationDetail = null;
$publicationAuthors = [];
$publicationArticles = [];

if ($selectedPublicationId !== null) {
    $detailSql = "
      SELECT
        PubID,
        NULLIF(TRIM(PubTittel), '') AS title,
        NULLIF(TRIM(PubTittelSub), '') AS subtitle,
        NULLIF(TRIM(ISBN), '') AS isbn,
        NULLIF(TRIM(Forlag), '') AS publisher,
        UtgittYear,
        Sider,
        NULLIF(TRIM(SalgNavn), '') AS sales_contact,
        NULLIF(TRIM(SalgEpost), '') AS sales_email,
        NULLIF(TRIM(SalgWeb), '') AS sales_web,
        Bok,
        NULLIF(TRIM(PubInnh), '') AS content
      FROM tblPublikasjon
      WHERE PubID = ?
      LIMIT 1
    ";
    $detailStmt = $db->prepare($detailSql);
    if ($detailStmt instanceof mysqli_stmt) {
        $detailStmt->bind_param('i', $selectedPublicationId);
        $detailStmt->execute();
        $detailResult = $detailStmt->get_result();
        $publicationDetail = $detailResult ? $detailResult->fetch_assoc() : null;
        $detailStmt->close();
    }

    if ($publicationDetail) {
        $authorSql = "
          SELECT
            x.PuFoID,
            COALESCE(
              NULLIF(TRIM(f.PubForfNavn), ''),
              NULLIF(
                TRIM(CONCAT_WS(' ',
                  NULLIF(TRIM(f.PubFNavn), ''),
                  NULLIF(TRIM(f.PubENavn), '')
                )),
                ''
              ),
              CONCAT('Forfatter ', f.PubForfID)
            ) AS author_name
          FROM tblxPubForf x
          LEFT JOIN tblPubForfatter f ON f.PubForfID = x.PubForfID
          WHERE x.PubID = ?
          ORDER BY author_name ASC, x.PuFoID ASC
        ";
        $authorStmt = $db->prepare($authorSql);
        if ($authorStmt instanceof mysqli_stmt) {
            $authorStmt->bind_param('i', $selectedPublicationId);
            $authorStmt->execute();
            $authorResult = $authorStmt->get_result();
            if ($authorResult instanceof mysqli_result) {
                while ($row = $authorResult->fetch_assoc()) {
                    $publicationAuthors[] = [
                        'id' => (int)$row['PuFoID'],
                        'name' => trim((string)($row['author_name'] ?? '')),
                    ];
                }
                $authorResult->free();
            }
            $authorStmt->close();
        }

        $articleSql = "
          SELECT
            ap.ArtPubID,
            a.ArtID,
            COALESCE(NULLIF(TRIM(a.ArtTittel), ''), CONCAT('Artikkel ', a.ArtID)) AS title,
            a.Side,
            b.Year,
            b.BladNr,
            b.Lenke
          FROM tblxArtPub ap
          LEFT JOIN tblArtikkel a ON a.ArtID = ap.ArtID
          LEFT JOIN tblBlad b ON b.BladID = a.BladID
          WHERE ap.PubID = ?
          ORDER BY
            COALESCE(b.Year, 0) DESC,
            COALESCE(b.BladNr, 0) ASC,
            COALESCE(a.Side, 0) ASC,
            title ASC
        ";
        $articleStmt = $db->prepare($articleSql);
        if ($articleStmt instanceof mysqli_stmt) {
            $articleStmt->bind_param('i', $selectedPublicationId);
            $articleStmt->execute();
            $articleResult = $articleStmt->get_result();
            if ($articleResult instanceof mysqli_result) {
                while ($row = $articleResult->fetch_assoc()) {
                    $issueParts = [];
                    if (!empty($row['Year'])) {
                        $issueParts[] = (string)$row['Year'];
                    }
                    if (!empty($row['BladNr'])) {
                        $issueParts[] = 'nr ' . (int)$row['BladNr'];
                    }
                    $issue = implode(' ', $issueParts);

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

                    $publicationArticles[] = [
                        'id' => (int)$row['ArtPubID'],
                        'article_id' => $row['ArtID'] !== null ? (int)$row['ArtID'] : null,
                        'title' => trim((string)($row['title'] ?? '')),
                        'issue' => $issue,
                        'page' => ($row['Side'] !== null && (int)$row['Side'] !== 0) ? (string)$row['Side'] : '',
                        'link_href' => $lenkeHref,
                        'link_text' => $lenkeText !== '' ? $lenkeText : 'Åpne',
                    ];
                }
                $articleResult->free();
            }
            $articleStmt->close();
        }
    }
}

include('../includes/header.php');
?>
<main class="page-main">
  <div class="container">
    <div class="card">
      <div class="card-content">
        <h1 class="page-title"><?php echo h($page_title); ?></h1>

        <div class="dual-table-layout dual-table-layout--list-wide">
          <section class="dual-table-panel dual-table-panel--primary">
            <div class="table-wrap" data-table-wrap>
              <div class="table-pagination" data-pagination="top"></div>
              <div class="table-scroll">
                <table id="table-publikasjoner"
                  class="data-table"
                  data-list-table
                  data-rows-per-page="25"
                  data-empty-message="Ingen treff.">
                  <thead>
                    <tr>
                      <th>Publikasjon</th>
                      <th>År</th>
                      <th>Forlag</th>
                    </tr>
                    <tr class="filter-row">
                      <th><input type="search" class="column-filter" data-filter-column="0" data-filter-mode="contains" placeholder="Søk tittel" aria-label="Søk i tittel" /></th>
                      <th><input type="search" class="column-filter" data-filter-column="1" data-filter-mode="equals" placeholder="Filtrer år" aria-label="Søk i utgivelsesår" /></th>
                      <th><input type="search" class="column-filter" data-filter-column="2" data-filter-mode="contains" placeholder="Søk forlag" aria-label="Søk i forlag" /></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (empty($publications)): ?>
                      <tr data-empty-row="true"><td colspan="3">Ingen publikasjoner funnet.</td></tr>
                    <?php else: ?>
                      <?php foreach ($publications as $entry): ?>
                        <?php
                          $isSelected = ($entry['id'] === $selectedPublicationId);
                          $rowId = 'publikasjon-' . $entry['id'];
                          $queryParams = $_GET;
                          $queryParams['pub_id'] = $entry['id'];
                          $selectUrl = url('openfil/publikasjoner.php');
                          if (!empty($queryParams)) {
                              $selectUrl .= '?' . http_build_query($queryParams);
                          }
                          $selectUrl .= '#' . $rowId;
                        ?>
                        <tr id="<?php echo h($rowId); ?>" class="<?php echo $isSelected ? 'is-selected' : ''; ?>">
                          <td>
                            <a class="navigable-link" href="<?php echo h($selectUrl); ?>"<?php echo $isSelected ? ' aria-current="page"' : ''; ?>>
                              <?php echo h($entry['title']); ?>
                            </a>
                          </td>
                          <td><?php echo $entry['year'] !== null ? h((string)$entry['year']) : '-'; ?></td>
                          <td><?php echo $entry['publisher'] !== '' ? h($entry['publisher']) : '-'; ?></td>
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
              <?php if ($publicationDetail === null): ?>
                <p>Ingen publikasjon valgt.</p>
              <?php else: ?>
                <dl class="detail-list">
                  <dt>Tittel</dt>
                  <dd><?php
                    $detailTitle = $publicationDetail['title'] ?? '';
                    if ($detailTitle === '') {
                        $detailTitle = $selectedPublication['base_title'] ?? ('Publikasjon ' . $selectedPublicationId);
                    }
                    echo h($detailTitle);
                  ?></dd>
                  <dt>Undertittel</dt>
                  <dd><?php echo h(!empty($publicationDetail['subtitle']) ? $publicationDetail['subtitle'] : '-'); ?></dd>
                  <dt>ISBN</dt>
                  <dd><?php echo h(!empty($publicationDetail['isbn']) ? $publicationDetail['isbn'] : '-'); ?></dd>
                  <dt>Forlag</dt>
                  <dd><?php echo h(!empty($publicationDetail['publisher']) ? $publicationDetail['publisher'] : '-'); ?></dd>
                  <dt>Utgitt</dt>
                  <dd><?php
                    $year = $publicationDetail['UtgittYear'] ?? null;
                    echo ($year !== null && (int)$year !== 0) ? h((string)$year) : '-';
                  ?></dd>
                  <dt>Sider</dt>
                  <dd><?php echo ($publicationDetail['Sider'] !== null && (int)$publicationDetail['Sider'] !== 0) ? h((string)$publicationDetail['Sider']) : '-'; ?></dd>
                  <dt>Format</dt>
                  <dd><?php echo (!empty($publicationDetail['Bok']) && (int)$publicationDetail['Bok'] === 1) ? 'Bok' : 'Annen publikasjon'; ?></dd>
                  <dt>Salgskontakt</dt>
                  <dd><?php echo h(!empty($publicationDetail['sales_contact']) ? $publicationDetail['sales_contact'] : '-'); ?></dd>
                  <dt>Kontakt e-post</dt>
                  <dd>
                    <?php if (!empty($publicationDetail['sales_email'])): ?>
                      <a href="mailto:<?php echo h($publicationDetail['sales_email']); ?>"><?php echo h($publicationDetail['sales_email']); ?></a>
                    <?php else: ?>
                      -
                    <?php endif; ?>
                  </dd>
                  <dt>Nettside</dt>
                  <dd>
                    <?php
                      $salesWeb = $publicationDetail['sales_web'] ?? '';
                      $webHref = '';
                      if ($salesWeb !== '') {
                          $webHref = preg_match('~^https?://~i', $salesWeb) ? $salesWeb : ('https://' . ltrim($salesWeb, '/'));
                      }
                    ?>
                    <?php if (!empty($salesWeb)): ?>
                      <a href="<?php echo h($webHref); ?>" target="_blank" rel="noopener">
                        <?php echo h($salesWeb); ?>
                      </a>
                    <?php else: ?>
                      -
                    <?php endif; ?>
                  </dd>
                </dl>

                <?php if (!empty($publicationDetail['content'])): ?>
                  <h3>Innhold</h3>
                  <p><?php echo nl2br(h($publicationDetail['content'])); ?></p>
                <?php endif; ?>

                <h3>Forfattere</h3>
                <div class="table-wrap table-wrap--static">
                  <div class="table-scroll">
                    <table class="data-table data-table--compact">
                      <thead>
                        <tr>
                          <th>Navn</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php if (empty($publicationAuthors)): ?>
                          <tr><td>Ingen forfattere registrert.</td></tr>
                        <?php else: ?>
                          <?php foreach ($publicationAuthors as $author): ?>
                            <tr>
                              <td><?php echo h($author['name']); ?></td>
                            </tr>
                          <?php endforeach; ?>
                        <?php endif; ?>
                      </tbody>
                    </table>
                  </div>
                </div>

                <h3>Artikler i Dampskibsposten</h3>
                <div class="table-wrap table-wrap--static">
                  <div class="table-scroll">
                    <table class="data-table data-table--compact">
                      <thead>
                        <tr>
                          <th>Tittel</th>
                          <th>Utgave</th>
                          <th>Side</th>
                          <th>Lenke</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php if (empty($publicationArticles)): ?>
                          <tr><td colspan="4">Ingen artikler registrert for denne publikasjonen.</td></tr>
                        <?php else: ?>
                          <?php foreach ($publicationArticles as $article): ?>
                            <tr>
                              <td>
                                <?php if (!empty($article['article_id'])): ?>
                                  <a href="<?php echo h(url('openfil/detalj/artikkel_detalj.php?id=' . $article['article_id'])); ?>" target="_blank" rel="noopener">
                                    <?php echo h($article['title']); ?>
                                  </a>
                                <?php else: ?>
                                  <?php echo h($article['title']); ?>
                                <?php endif; ?>
                              </td>
                              <td><?php echo $article['issue'] !== '' ? h($article['issue']) : '-'; ?></td>
                              <td><?php echo $article['page'] !== '' ? h($article['page']) : '-'; ?></td>
                              <td>
                                <?php if ($article['link_href'] !== ''): ?>
                                  <a href="<?php echo h($article['link_href']); ?>" target="_blank" rel="noopener">
                                    <?php echo h($article['link_text']); ?>
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
