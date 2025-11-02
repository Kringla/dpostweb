<?php
require_once('../includes/bootstrap.php');
$page_title = 'Forening / org';

$db = db();

$organizations = [];
$listSql = "
  SELECT
    o.OrgID AS org_id,
    COALESCE(NULLIF(TRIM(o.OrgNavn), ''), CONCAT('Organisasjon ', o.OrgID)) AS name,
    TRIM(CONCAT_WS(', ', NULLIF(o.Poststed, ''), NULLIF(o.Postnr, ''))) AS location,
    COALESCE(o.AntDP, 0) AS mention_count
  FROM tblOrganisasjon o
  WHERE (o.VernOrg <> 0 OR o.MuseumOrg <> 0)
  ORDER BY name ASC
";

$listResult = $db->query($listSql);
if ($listResult instanceof mysqli_result) {
    while ($row = $listResult->fetch_assoc()) {
        $name = trim((string)($row['name'] ?? ''));
        if ($name === '') {
            $name = 'Organisasjon ' . (int)$row['org_id'];
        }

        $organizations[] = [
            'id' => (int)$row['org_id'],
            'name' => $name,
            'location' => trim((string)($row['location'] ?? '')),
            'mention_count' => (int)($row['mention_count'] ?? 0),
        ];
    }
    $listResult->free();
}

$selectedOrgId = filter_input(INPUT_GET, 'org_id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$selectedOrganization = null;

if (!empty($organizations)) {
    if ($selectedOrgId === null) {
        $selectedOrganization = $organizations[0];
        $selectedOrgId = $selectedOrganization['id'];
    } else {
        foreach ($organizations as $entry) {
            if ($entry['id'] === $selectedOrgId) {
                $selectedOrganization = $entry;
                break;
            }
        }
        if ($selectedOrganization === null) {
            $selectedOrganization = $organizations[0];
            $selectedOrgId = $selectedOrganization['id'];
        }
    }
}

$organizationDetail = null;
$organizationArticles = [];

if ($selectedOrgId !== null) {
    $detailSql = "
      SELECT
        o.OrgID,
        COALESCE(NULLIF(TRIM(o.OrgNavn), ''), CONCAT('Organisasjon ', o.OrgID)) AS name,
        NULLIF(TRIM(o.Adresse), '') AS address,
        NULLIF(TRIM(o.Postnr), '') AS postal_code,
        NULLIF(TRIM(o.Poststed), '') AS city,
        NULLIF(TRIM(o.EpostAdresse), '') AS email,
        NULLIF(TRIM(o.WebSide), '') AS website,
        NULLIF(TRIM(o.FB), '') AS facebook,
        NULLIF(TRIM(o.Notater), '') AS notes,
        COALESCE(o.AntDP, 0) AS mention_total,
        COALESCE(o.AntArt, 0) AS article_count,
        COALESCE(o.AntNot, 0) AS notice_count,
        COALESCE(o.AntSpalte, 0) AS column_count,
        COALESCE(o.AntHoved, 0) AS main_count,
        COALESCE(o.AntOmtalt, 0) AS cited_count,
        COALESCE(o.StatOrg, '') AS stat_org,
        COALESCE(o.Nedlagt, 0) AS nedlagt
      FROM tblOrganisasjon o
      WHERE o.OrgID = ?
        AND (o.VernOrg <> 0 OR o.MuseumOrg <> 0)
      LIMIT 1
    ";

    $detailStmt = $db->prepare($detailSql);
    if ($detailStmt instanceof mysqli_stmt) {
        $detailStmt->bind_param('i', $selectedOrgId);
        $detailStmt->execute();
        $detailResult = $detailStmt->get_result();
        $organizationDetail = $detailResult ? $detailResult->fetch_assoc() : null;
        $detailStmt->close();
    }

    $articleSql = "
      SELECT
        a.ArtID AS article_id,
        COALESCE(NULLIF(TRIM(a.ArtTittel), ''), CONCAT('Artikkel ', a.ArtID)) AS title,
        MIN(b.Year) AS published_year,
        MIN(b.BladNr) AS issue_number,
        MIN(NULLIF(a.Side, 0)) AS page_number,
        a.ArtType AS art_type
      FROM tblxArtOrg ao
      JOIN tblArtikkel a ON a.ArtID = ao.ArtID
      LEFT JOIN tblBlad b ON b.BladID = a.BladID
      WHERE ao.OrgID = ? AND ao.ArtID > 0
      GROUP BY a.ArtID, a.ArtTittel, a.ArtType
      ORDER BY published_year DESC, issue_number ASC, page_number ASC, title ASC
    ";

    $articleStmt = $db->prepare($articleSql);
    if ($articleStmt instanceof mysqli_stmt) {
        $articleStmt->bind_param('i', $selectedOrgId);
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

                $organizationArticles[] = [
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
}

include('../includes/header.php');
?>
<main class="page-main">
  <div class="container">
    <div class="card">
      <div class="card-content">
        <h1 class="page-title"><?php echo h($page_title); ?></h1>

        <div class="dual-table-layout dual-table-layout--even">
          <section class="dual-table-panel dual-table-panel--primary">
            <div class="table-wrap" data-table-wrap>
              <div id="forening-pagination-top" class="table-pagination" data-pagination="top"></div>
              <div class="table-scroll">
                <table id="table-foreningorg" class="data-table" data-list-table data-rows-per-page="25" data-empty-message="Ingen treff.">
                  <thead>
                    <tr>
                      <th>Navn</th><th>Lokasjon</th><th class="count-col">Antall omtaler</th>
                    </tr>
                    <tr class="filter-row">
                      <th><input type="search" class="column-filter" data-filter-column="0" data-filter-mode="contains" placeholder="Søk navn" aria-label="Søk i navn" /></th>
                      <th><input type="search" class="column-filter" data-filter-column="1" data-filter-mode="contains" placeholder="Søk lokasjon" aria-label="Søk i lokasjoner" /></th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php if (empty($organizations)): ?>
                    <tr data-empty-row="true"><td colspan="3">Ingen organisasjoner funnet.</td></tr>
                  <?php else: ?>
                    <?php foreach ($organizations as $org): ?>
                      <?php
                        $isSelected = ($org['id'] === $selectedOrgId);
                        $rowId = 'org-' . $org['id'];
                        $queryParams = $_GET;
                        $queryParams['org_id'] = $org['id'];
                        $selectUrl = url('openfil/forening_org.php');
                        if (!empty($queryParams)) {
                            $selectUrl .= '?' . http_build_query($queryParams);
                        }
                        $selectUrl .= '#' . $rowId;
                      ?>
                      <tr id="<?php echo h($rowId); ?>" class="<?php echo $isSelected ? 'is-selected' : ''; ?>">
                        <td class="name-col">
                          <a
                            class="navigable-link"
                            data-detail-param="org_id"
                            data-detail-id="<?php echo (int)$org['id']; ?>"
                            data-detail-base="<?php echo h(url('openfil/forening_org.php')); ?>"
                            data-detail-anchor="<?php echo h($rowId); ?>"
                            href="<?php echo h($selectUrl); ?>"<?php echo $isSelected ? ' aria-current="page"' : ''; ?>>
                            <?php echo h($org['name']); ?>
                          </a>
                        </td>
                        <td><?php echo h($org['location']); ?></td>
                        <td class="count-col"><?php echo h((string)$org['mention_count']); ?></td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                  </tbody>
                </table>
              </div>
              <div id="forening-pagination-bottom" class="table-pagination" data-pagination="bottom"></div>
            </div>
          </section>

          <section class="dual-table-panel dual-table-panel--secondary">
            <div class="secondary-card">
              <h2 class="secondary-title">
                Innhold<?php if ($selectedOrganization !== null) { echo ' for ' . h($selectedOrganization['name']); } ?>
              </h2>
              <?php if ($organizationDetail !== null): ?>
                <?php
                  $locationParts = array_filter([
                      $organizationDetail['postal_code'] ?? '',
                      $organizationDetail['city'] ?? '',
                  ], static function ($value) {
                      return $value !== null && $value !== '';
                  });
                  $location = trim(implode(' ', $locationParts));

                  $summaryItems = [];
                  if (!empty($organizationDetail['address'])) {
                      $summaryItems[] = ['label' => 'Adresse', 'value' => $organizationDetail['address']];
                  }
                  if ($location !== '') {
                      $summaryItems[] = ['label' => 'Lokasjon', 'value' => $location];
                  }
                  if (!empty($organizationDetail['email'])) {
                      $summaryItems[] = [
                          'label' => 'E-post',
                          'value' => '<a href="mailto:' . h($organizationDetail['email']) . '">' . h($organizationDetail['email']) . '</a>',
                      ];
                  }
                  if (!empty($organizationDetail['website'])) {
                      $webHref = preg_match('~^https?://~i', $organizationDetail['website'])
                        ? $organizationDetail['website']
                        : ('https://' . ltrim($organizationDetail['website'], '/'));
                      $summaryItems[] = [
                          'label' => 'Nettside',
                          'value' => '<a href="' . h($webHref) . '" target="_blank" rel="noopener">' . h($organizationDetail['website']) . '</a>',
                      ];
                  }
                  if (!empty($organizationDetail['facebook'])) {
                      $fbHref = preg_match('~^https?://~i', $organizationDetail['facebook'])
                        ? $organizationDetail['facebook']
                        : ('https://' . ltrim($organizationDetail['facebook'], '/'));
                      $summaryItems[] = [
                          'label' => 'Facebook',
                          'value' => '<a href="' . h($fbHref) . '" target="_blank" rel="noopener">' . h($organizationDetail['facebook']) . '</a>',
                      ];
                  }
                  $summaryItems[] = ['label' => 'Antall omtaler', 'value' => (string)$organizationDetail['mention_total']];
                  $summaryItems[] = ['label' => 'Artikler', 'value' => (string)$organizationDetail['article_count']];
                  $summaryItems[] = ['label' => 'Notiser', 'value' => (string)$organizationDetail['notice_count']];
                  $summaryItems[] = ['label' => 'Spalter', 'value' => (string)$organizationDetail['column_count']];
                  $summaryItems[] = ['label' => 'Hovedsaker', 'value' => (string)$organizationDetail['main_count']];
                  $summaryItems[] = ['label' => 'Sitert', 'value' => (string)$organizationDetail['cited_count']];
                  if (!empty($organizationDetail['stat_org'])) {
                      $summaryItems[] = ['label' => 'Kategori', 'value' => $organizationDetail['stat_org']];
                  }
                  $summaryItems[] = ['label' => 'Status', 'value' => ((int)$organizationDetail['nedlagt'] !== 0) ? 'Nedlagt' : 'Aktiv / ukjent'];
                ?>
                <?php if (!empty($summaryItems)): ?>
                  <div class="selection-summary">
                    <?php foreach ($summaryItems as $item): ?>
                      <div>
                        <strong><?php echo h($item['label']); ?></strong>
                        <?php echo (strpos($item['value'], '<a ') !== false) ? $item['value'] : h($item['value']); ?>
                      </div>
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?>

                <?php if (!empty($organizationDetail['notes'])): ?>
                  <div class="secondary-section">
                    <h3>Notater</h3>
                    <p><?php echo nl2br(h($organizationDetail['notes'])); ?></p>
                  </div>
                <?php endif; ?>
              <?php else: ?>
                <p>Ingen organisasjon valgt.</p>
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
                      <?php if ($selectedOrgId === null): ?>
                        <tr data-empty-row="true"><td colspan="4">Ingen organisasjon valgt.</td></tr>
                      <?php elseif (empty($organizationArticles)): ?>
                        <tr data-empty-row="true"><td colspan="4">Ingen artikler registrert for denne organisasjonen.</td></tr>
                      <?php else: ?>
                        <?php foreach ($organizationArticles as $article): ?>
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
            </div>
          </section>
        </div>

      </div>
    </div>
  </div>
</main>

<?php include('../includes/footer.php'); ?>
