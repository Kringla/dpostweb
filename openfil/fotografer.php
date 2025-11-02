<?php
require_once('../includes/bootstrap.php');
$page_title = 'Fotografer';

$db = db();

$photographers = [];
$photographerSql = "
  SELECT
    f.ForfID AS fotograf_id,
    COALESCE(
      NULLIF(TRIM(f.ForfNavn), ''),
      NULLIF(TRIM(CONCAT_WS(' ', f.FNavn, f.ENavn)), '')
    ) AS name,
    COUNT(DISTINCT b.PicID) AS image_count,
    MIN(
      NULLIF(
        COALESCE(NULLIF(b.TattYear, 0), NULLIF(b.TattYearSenest, 0), bl.Year),
        0
      )
    ) AS first_year,
    MAX(
      NULLIF(
        COALESCE(NULLIF(b.TattYear, 0), NULLIF(b.TattYearSenest, 0), bl.Year),
        0
      )
    ) AS last_year
  FROM tblForfatter f
  LEFT JOIN tblBilde b ON b.ForfID = f.ForfID
  LEFT JOIN tblxBildeBlad bb ON bb.PicID = b.PicID
  LEFT JOIN tblBlad bl ON bl.BladID = bb.BladID
  WHERE f.ForfID > 1
    AND (f.Fotograf <> 0 OR COALESCE(f.AntBilde, 0) > 0 OR b.PicID IS NOT NULL)
  GROUP BY f.ForfID, f.ForfNavn, f.FNavn, f.ENavn
  ORDER BY name ASC
";

$photographerResult = $db->query($photographerSql);

if ($photographerResult instanceof mysqli_result) {
    while ($row = $photographerResult->fetch_assoc()) {
        $name = trim((string)($row['name'] ?? ''));
        if ($name === '') {
            $name = 'Fotograf ' . (int)$row['fotograf_id'];
        }

        $photographers[] = [
            'id' => (int)$row['fotograf_id'],
            'name' => $name,
            'image_count' => (int)($row['image_count'] ?? 0),
            'first_year' => ($row['first_year'] !== null && $row['first_year'] !== '' && (int)$row['first_year'] !== 0) ? (int)$row['first_year'] : null,
            'last_year' => ($row['last_year'] !== null && $row['last_year'] !== '' && (int)$row['last_year'] !== 0) ? (int)$row['last_year'] : null,
        ];
    }
    $photographerResult->free();
}

$selectedPhotographerId = filter_input(INPUT_GET, 'fotograf_id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$selectedPhotographerId = $selectedPhotographerId !== false ? $selectedPhotographerId : null;
$selectedPhotographerData = null;

if (!empty($photographers)) {
    if ($selectedPhotographerId === null) {
        $selectedPhotographerId = $photographers[0]['id'];
        $selectedPhotographerData = $photographers[0];
    } else {
        foreach ($photographers as $photographer) {
            if ($photographer['id'] === $selectedPhotographerId) {
                $selectedPhotographerData = $photographer;
                break;
            }
        }
        if ($selectedPhotographerData === null) {
            $selectedPhotographerId = $photographers[0]['id'];
            $selectedPhotographerData = $photographers[0];
        }
    }
}

$photos = [];

if ($selectedPhotographerId !== null) {
    $photoSql = "
      SELECT
        b.PicID AS bilde_id,
        MAX(NULLIF(TRIM(bb.PicTitBlad), '')) AS blad_title,
        b.PicMotiv AS motiv,
        b.PicType AS pic_type,
        MIN(bb.Side) AS page_number,
        MIN(bl.Year) AS published_year,
        MIN(bl.BladNr) AS issue_number,
        b.TattYear AS taken_year,
        b.TattYearSenest AS taken_year_latest
      FROM tblBilde b
      LEFT JOIN tblxBildeBlad bb ON bb.PicID = b.PicID
      LEFT JOIN tblBlad bl ON bl.BladID = bb.BladID
      WHERE b.ForfID = ?
      GROUP BY b.PicID, b.PicMotiv, b.PicType, b.TattYear, b.TattYearSenest
      ORDER BY
        published_year DESC,
        issue_number ASC,
        page_number ASC,
        blad_title ASC,
        b.PicMotiv ASC
    ";

    $photoStmt = $db->prepare($photoSql);
    if ($photoStmt instanceof mysqli_stmt) {
        $photoStmt->bind_param('i', $selectedPhotographerId);
        $photoStmt->execute();
        $photoResult = $photoStmt->get_result();

        if ($photoResult instanceof mysqli_result) {
            while ($row = $photoResult->fetch_assoc()) {
                $bladTitle = trim((string)($row['blad_title'] ?? ''));
                $motiv = trim((string)($row['motiv'] ?? ''));
                $title = $bladTitle !== '' ? $bladTitle : ($motiv !== '' ? $motiv : 'Bilde ' . (int)$row['bilde_id']);

                $year = $row['published_year'];
                $issue = $row['issue_number'];
                $fallbackYear = null;

                if (!empty($row['taken_year']) && (int)$row['taken_year'] !== 0) {
                    $fallbackYear = (int)$row['taken_year'];
                } elseif (!empty($row['taken_year_latest']) && (int)$row['taken_year_latest'] !== 0) {
                    $fallbackYear = (int)$row['taken_year_latest'];
                }

                $issueParts = [];
                if ($year !== null && $year !== '' && (int)$year !== 0) {
                    $issueParts[] = (string)$year;
                } elseif ($fallbackYear !== null) {
                    $issueParts[] = (string)$fallbackYear;
                }
                if ($issue !== null && $issue !== '' && (int)$issue !== 0) {
                    $issueParts[] = 'nr ' . (int)$issue;
                }

                $photos[] = [
                    'id' => (int)$row['bilde_id'],
                    'title' => $title,
                    'issue' => implode(' ', $issueParts),
                    'page' => ($row['page_number'] !== null && $row['page_number'] !== '' && (int)$row['page_number'] !== 0) ? (string)$row['page_number'] : '',
                    'type' => trim((string)($row['pic_type'] ?? '')),
                ];
            }
            $photoResult->free();
        }

        $photoStmt->close();
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
              <div id="fotografer-pagination-top" class="table-pagination" data-pagination="top"></div>
              <div class="table-scroll">
                <table id="table-fotografer" class="data-table" data-list-table data-rows-per-page="25" data-empty-message="Ingen treff.">
                  <thead>
                    <tr>
                      <th class="name-col">Navn</th><th class="count-col">Bilder</th>
                    </tr>
                    <tr class="filter-row">
                      <th><input type="search" class="column-filter" data-filter-column="0" data-filter-mode="contains" placeholder="Søk navn" aria-label="Søk i navn" /></th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php if (empty($photographers)): ?>
                    <tr data-empty-row="true"><td colspan="2">Ingen fotografer funnet.</td></tr>
                  <?php else: ?>
                    <?php foreach ($photographers as $photographer): ?>
                      <?php
                        $isSelected = ($photographer['id'] === $selectedPhotographerId);
                        $rowId = 'fotograf-' . $photographer['id'];
                        $queryParams = $_GET;
                        $queryParams['fotograf_id'] = $photographer['id'];
                        $selectUrl = url('openfil/fotografer.php');
                        if (!empty($queryParams)) {
                            $selectUrl .= '?' . http_build_query($queryParams);
                        }
                        $selectUrl .= '#' . $rowId;
                      ?>
                      <tr id="<?php echo h($rowId); ?>" class="<?php echo $isSelected ? 'is-selected' : ''; ?>">
                        <td class="name-col">
                          <a
                            class="author-select-link"
                            data-detail-param="fotograf_id"
                            data-detail-id="<?php echo (int)$photographer['id']; ?>"
                            data-detail-base="<?php echo h(url('openfil/fotografer.php')); ?>"
                            data-detail-anchor="<?php echo h($rowId); ?>"
                            href="<?php echo h($selectUrl); ?>"<?php echo $isSelected ? ' aria-current="page"' : ''; ?>>
                            <?php echo h($photographer['name']); ?>
                          </a>
                        </td>
                        <td class="count-col"><?php echo h($photographer['image_count']); ?></td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                  </tbody>
                </table>
              </div>
              <div id="fotografer-pagination-bottom" class="table-pagination" data-pagination="bottom"></div>
            </div>
          </section>

          <section class="dual-table-panel dual-table-panel--secondary">
            <div class="secondary-card">
              <h2 class="secondary-title">
                Bilder<?php if ($selectedPhotographerData !== null) { echo ' av ' . h($selectedPhotographerData['name']); } ?>
              </h2>
              <?php if ($selectedPhotographerData !== null): ?>
              <div class="selection-summary">
                <div>
                  <strong>Navn</strong>
                  <?php echo h($selectedPhotographerData['name']); ?>
                </div>
                <div>
                  <strong>Antall bilder</strong>
                  <?php echo h($selectedPhotographerData['image_count']); ?>
                </div>
                <div>
                  <strong>Aktiv periode</strong>
                  <?php
                    $first = $selectedPhotographerData['first_year'];
                    $last = $selectedPhotographerData['last_year'];
                    if ($first === null && $last === null) {
                        echo 'Ukjent';
                    } elseif ($first !== null && $last !== null && $first !== $last) {
                        echo h($first) . '–' . h($last);
                    } else {
                        echo h($first ?? $last);
                    }
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
                    <?php if ($selectedPhotographerId === null): ?>
                      <tr data-empty-row="true"><td colspan="4">Ingen fotograf valgt.</td></tr>
                    <?php elseif (empty($photos)): ?>
                      <tr data-empty-row="true"><td colspan="4">Ingen bilder registrert for denne fotografen.</td></tr>
                    <?php else: ?>
                      <?php foreach ($photos as $photo): ?>
                        <tr>
                          <td class="title-col"><?php echo h($photo['title']); ?></td>
                          <td><?php echo h($photo['issue']); ?></td>
                          <td><?php echo h($photo['page']); ?></td>
                          <td><?php echo h($photo['type']); ?></td>
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
