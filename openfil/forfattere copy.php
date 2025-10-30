<?php
require_once('../includes/bootstrap.php');
$page_title = 'Forfattere';
include('../includes/header.php');
?>
<main class="page-main">
  <div class="container">
    <div class="card">
      <div class="card-content">
        <h1 class="page-title"><?php echo h($page_title); ?></h1>


        <div class="table-wrap" data-table-wrap>
          <div id="forfattere-pagination-top" class="table-pagination" data-pagination="top"></div>
          <div class="table-scroll">
            <table id="table-forfattere" class="data-table" data-list-table data-rows-per-page="40" data-empty-message="Ingen treff.">
              <thead>
                <tr>
                  <th>Navn</th><th class="count-col">Artikler</th><th>F&oslash;rste &aring;r</th><th>Detaljer</th>
                </tr>
                <tr class="filter-row">
                  <th><input type="search" class="column-filter" data-filter-column="0" data-filter-mode="contains" placeholder="S&oslash;k navn" aria-label="S&oslash;k i navn" /></th>
                  <th></th>
                  <th><input type="search" class="column-filter" data-filter-column="2" data-filter-mode="startsWith" placeholder="S&oslash;k &aring;r" aria-label="S&oslash;k i &aring;r" /></th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
              <?php
                $db = db();
                $sql = "
                  SELECT
                    f.ForfID AS forfatter_id,
                    COALESCE(NULLIF(TRIM(f.ForfNavn), ''), NULLIF(TRIM(CONCAT_WS('' '', f.FNavn, f.ENavn)), '')) AS name,
                    COALESCE(f.AntArt, 0) AS article_count,
                    MIN(bl.Year) AS first_year
                  FROM tblForfatter f
                  LEFT JOIN tblxArtForf af ON af.ForfID = f.ForfID
                  LEFT JOIN tblArtikkel a ON a.ArtID = af.ArtID
                  LEFT JOIN tblBlad bl ON bl.BladID = a.BladID
                  WHERE f.ForfID > 1 AND (f.Forfatter <> 0 OR COALESCE(f.AntArt, 0) > 0)
                  GROUP BY f.ForfID, f.ForfNavn, f.FNavn, f.ENavn, f.AntArt
                  ORDER BY name ASC
                ";
                $stmt = $db->query($sql);
                if (!$stmt || $stmt->num_rows === 0) {
                  echo '<tr data-empty-row="true"><td colspan="4">Ingen forfattere funnet.</td></tr>';
                } else {
                  while ($row = $stmt->fetch_assoc()) {
                    $name = trim((string)($row['name'] ?? ''));
                    if ($name === '') {
                      $name = 'Forfatter ' . (int)$row['forfatter_id'];
                    }
                    $articleCount = (int)($row['article_count'] ?? 0);
                    $firstYear = $row['first_year'];
                    $firstYearText = ($firstYear !== null && $firstYear !== '' && (int)$firstYear !== 0) ? (string)$firstYear : '';
                    $detailUrl = url('openfil/detalj/forfatter_detalj.php?id=' . (int)$row['forfatter_id']);
              ?>
                <tr>
                  <td><?= h($name) ?></td>
                  <td class="count-col"><?= h($articleCount) ?></td>
                  <td><?= h($firstYearText) ?></td>
                  <td><a class="btn-small btn-small--icon" href="<?= h($detailUrl) ?>" aria-label="Vis forfatter" title="Vis forfatter">&#128065;</a></td>
                </tr>
              <?php
                  }
                  $stmt->free();
                }
              ?>
              </tbody>
            </table>
          </div>
          <div id="forfattere-pagination-bottom" class="table-pagination" data-pagination="bottom"></div>
        </div>

      </div>
    </div>
  </div>
</main>



<?php include('../includes/footer.php'); ?>


