<?php
require_once('../includes/bootstrap.php');
$page_title = 'Personer';

$db = db();

$persons = [];
$personSql = "
  SELECT
    p.PersID AS person_id,
    TRIM(CONCAT_WS(' ', NULLIF(p.FNavn, ''), NULLIF(p.ENavn, ''))) AS name,
    COALESCE(p.PersTittel, '') AS title,
    COALESCE(p.PersYrke, '') AS occupation,
    COALESCE(p.PersBorn, '') AS birth_info,
    COALESCE(p.PersNasjon, '') AS nation,
    COALESCE(p.RelFartBedr, '') AS related_orgs,
    COALESCE(p.RelSted, '') AS related_places,
    COALESCE(p.TilNavn, '') AS nickname,
    COALESCE(p.Link, '') AS external_link,
    COALESCE(p.AntArt, 0) AS article_count,
    COALESCE(p.AntBilde, 0) AS image_count
  FROM tblPerson p
  WHERE COALESCE(p.AntDP, 0) > 0
  ORDER BY name ASC, p.PersID ASC
";

$personResult = $db->query($personSql);

if ($personResult instanceof mysqli_result) {
    while ($row = $personResult->fetch_assoc()) {
        $name = trim((string)($row['name'] ?? ''));
        if ($name === '') {
            $name = 'Person ' . (int)$row['person_id'];
        }

        $persons[] = [
            'id' => (int)$row['person_id'],
            'name' => $name,
            'title' => trim((string)$row['title']),
            'occupation' => trim((string)$row['occupation']),
            'birth_info' => trim((string)$row['birth_info']),
            'nation' => trim((string)$row['nation']),
            'related_orgs' => trim((string)$row['related_orgs']),
            'related_places' => trim((string)$row['related_places']),
            'nickname' => trim((string)$row['nickname']),
            'external_link' => trim((string)$row['external_link']),
            'article_count' => (int)($row['article_count'] ?? 0),
            'image_count' => (int)($row['image_count'] ?? 0),
        ];
    }
    $personResult->free();
}

$selectedPersonId = filter_input(INPUT_GET, 'person_id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$selectedPersonData = null;

if (!empty($persons)) {
    if ($selectedPersonId === null) {
        $selectedPersonData = $persons[0];
        $selectedPersonId = $selectedPersonData['id'];
    } else {
        foreach ($persons as $person) {
            if ($person['id'] === $selectedPersonId) {
                $selectedPersonData = $person;
                break;
            }
        }
        if ($selectedPersonData === null) {
            $selectedPersonData = $persons[0];
            $selectedPersonId = $selectedPersonData['id'];
        }
    }
}

$personArticles = [];
$personImages = [];

if ($selectedPersonId !== null) {
    $articleSql = "
      SELECT
        a.ArtID AS article_id,
        COALESCE(NULLIF(TRIM(a.ArtTittel), ''), CONCAT('Artikkel ', a.ArtID)) AS title,
        MIN(b.Year) AS published_year,
        MIN(b.BladNr) AS issue_number,
        MIN(NULLIF(pb.Side, 0)) AS page_number,
        a.ArtType AS art_type
      FROM tblxPersBlad pb
      JOIN tblArtikkel a ON a.ArtID = pb.ArtID
      LEFT JOIN tblBlad b ON b.BladID = a.BladID
      WHERE pb.PersID = ? AND pb.ArtID > 0
      GROUP BY a.ArtID, a.ArtTittel, a.ArtType
      ORDER BY published_year DESC, issue_number ASC, page_number ASC, title ASC
    ";

    $articleStmt = $db->prepare($articleSql);
    if ($articleStmt instanceof mysqli_stmt) {
        $articleStmt->bind_param('i', $selectedPersonId);
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

                $personArticles[] = [
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
      FROM tblxPersBlad pb
      JOIN tblBilde b ON b.PicID = pb.PicID
      LEFT JOIN tblxBildeBlad bb ON bb.PicID = b.PicID
      LEFT JOIN tblBlad bl ON bl.BladID = bb.BladID
      WHERE pb.PersID = ? AND pb.PicID > 0
      GROUP BY b.PicID, b.PicMotiv, b.PicType
      ORDER BY published_year DESC, issue_number ASC, page_number ASC, title ASC
    ";

    $imageStmt = $db->prepare($imageSql);
    if ($imageStmt instanceof mysqli_stmt) {
        $imageStmt->bind_param('i', $selectedPersonId);
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

                $personImages[] = [
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
            <a class="btn" href="<?php echo h(url('openfil/personer.php')); ?>" data-reset-table="<?php echo h(url('openfil/personer.php')); ?>">Tilbake</a>
            <a class="btn" href="<?php echo h(url('index.php')); ?>">Til landingssiden</a>
          </div>
        </div>

        <div class="dual-table-layout">
          <section class="dual-table-panel dual-table-panel--primary">
            <div class="table-wrap" data-table-wrap>
              <div id="personer-pagination-top" class="table-pagination" data-pagination="top"></div>
              <div class="table-scroll">
                <table id="table-personer" class="data-table" data-list-table data-rows-per-page="25" data-empty-message="Ingen treff.">
                  <thead>
                    <tr>
                      <th class="name-col">Navn</th><th>Rolle</th><th class="count-col">Artikler</th><th class="count-col">Bilder</th>
                    </tr>
                    <tr class="filter-row">
                      <th><input type="search" class="column-filter" data-filter-column="0" data-filter-mode="contains" placeholder="Søk navn" aria-label="Søk i navn" /></th>
                      <th><input type="search" class="column-filter" data-filter-column="1" data-filter-mode="contains" placeholder="Søk rolle" aria-label="Søk i roller" /></th>
                      <th></th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php if (empty($persons)): ?>
                    <tr data-empty-row="true"><td colspan="4">Ingen personer funnet.</td></tr>
                  <?php else: ?>
                    <?php foreach ($persons as $person): ?>
                      <?php
                        $isSelected = ($person['id'] === $selectedPersonId);
                        $rowId = 'person-' . $person['id'];
                        $queryParams = $_GET;
                        $queryParams['person_id'] = $person['id'];
                        $selectUrl = url('openfil/personer.php');
                        if (!empty($queryParams)) {
                            $selectUrl .= '?' . http_build_query($queryParams);
                        }
                        $selectUrl .= '#' . $rowId;
                        $roleText = $person['title'] !== '' ? $person['title'] : $person['occupation'];
                      ?>
                      <tr id="<?php echo h($rowId); ?>" class="<?php echo $isSelected ? 'is-selected' : ''; ?>">
                        <td class="name-col">
                          <a
                            class="author-select-link"
                            data-detail-param="person_id"
                            data-detail-id="<?php echo (int)$person['id']; ?>"
                            data-detail-base="<?php echo h(url('openfil/personer.php')); ?>"
                            data-detail-anchor="<?php echo h($rowId); ?>"
                            href="<?php echo h($selectUrl); ?>"<?php echo $isSelected ? ' aria-current="page"' : ''; ?>>
                            <?php echo h($person['name']); ?>
                          </a>
                        </td>
                        <td><?php echo h($roleText); ?></td>
                        <td class="count-col"><?php echo h($person['article_count']); ?></td>
                        <td class="count-col"><?php echo h($person['image_count']); ?></td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                  </tbody>
                </table>
              </div>
              <div id="personer-pagination-bottom" class="table-pagination" data-pagination="bottom"></div>
            </div>
          </section>

          <section class="dual-table-panel dual-table-panel--secondary">
            <div class="secondary-card">
              <h2 class="secondary-title">
                Innhold<?php if ($selectedPersonData !== null) { echo ' om ' . h($selectedPersonData['name']); } ?>
              </h2>
              <?php if ($selectedPersonData !== null): ?>
                <?php
                  $summaryItems = [];
                  if ($selectedPersonData['title'] !== '') {
                      $summaryItems[] = ['label' => 'Tittel', 'value' => $selectedPersonData['title']];
                  }
                  if ($selectedPersonData['occupation'] !== '') {
                      $summaryItems[] = ['label' => 'Yrke', 'value' => $selectedPersonData['occupation']];
                  }
                  if ($selectedPersonData['nickname'] !== '') {
                      $summaryItems[] = ['label' => 'Tilnavn', 'value' => $selectedPersonData['nickname']];
                  }
                  if ($selectedPersonData['related_orgs'] !== '') {
                      $summaryItems[] = ['label' => 'Relatert organisasjon', 'value' => $selectedPersonData['related_orgs']];
                  }
                  if ($selectedPersonData['related_places'] !== '') {
                      $summaryItems[] = ['label' => 'Relatert sted', 'value' => $selectedPersonData['related_places']];
                  }
                  if ($selectedPersonData['external_link'] !== '') {
                      $summaryItems[] = ['label' => 'Lenke', 'value' => $selectedPersonData['external_link']];
                  }
                ?>
                <?php if (!empty($summaryItems)): ?>
                  <dl class="detail-list">
                    <?php foreach ($summaryItems as $item): ?>
                      <div class="detail-list__item">
                        <dt><?php echo h($item['label']); ?></dt>
                        <dd><?php echo h($item['value']); ?></dd>
                      </div>
                    <?php endforeach; ?>
                  </dl>
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
                      <?php if ($selectedPersonId === null): ?>
                        <tr data-empty-row="true"><td colspan="4">Ingen person valgt.</td></tr>
                      <?php elseif (empty($personArticles)): ?>
                        <tr data-empty-row="true"><td colspan="4">Ingen artikler registrert for denne personen.</td></tr>
                      <?php else: ?>
                        <?php foreach ($personArticles as $article): ?>
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
                      <?php if ($selectedPersonId === null): ?>
                        <tr data-empty-row="true"><td colspan="4">Ingen person valgt.</td></tr>
                      <?php elseif (empty($personImages)): ?>
                        <tr data-empty-row="true"><td colspan="4">Ingen bilder registrert for denne personen.</td></tr>
                      <?php else: ?>
                        <?php foreach ($personImages as $image): ?>
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

            </div>
          </section>
        </div>

      </div>
    </div>
  </div>
</main>

<?php include('../includes/footer.php'); ?>
