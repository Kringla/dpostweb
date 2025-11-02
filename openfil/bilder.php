<?php
require_once('../includes/bootstrap.php');
$page_title = 'Bilder';
include('../includes/header.php');
?>
<main class="page-main">
  <div class="container">
    <div class="card">
      <div class="card-content">
        <h1 class="page-title"><?php echo h($page_title); ?></h1>

        <div class="table-wrap" data-table-wrap>
          <div id="bilder-pagination-top" class="table-pagination" data-pagination="top"></div>
          <div class="table-scroll">
            <table id="table-bilder" class="data-table" data-list-table data-rows-per-page="25" data-empty-message="Ingen treff.">
              <thead>
                <tr>
                  <th>Tittel</th><th>Fotograf</th><th>År</th><th>Sted tatt</th><th>Detaljer</th>
                </tr>
                <tr class="filter-row">
                  <th><input type="search" class="column-filter" data-filter-column="0" data-filter-mode="contains" placeholder="Søk tittel" aria-label="Søk i titler" /></th>
                  <th><input type="search" class="column-filter" data-filter-column="1" data-filter-mode="contains" placeholder="Søk fotograf" aria-label="Søk i fotografer" /></th>
                  <th><input type="search" class="column-filter" data-filter-column="2" data-filter-mode="startsWith" placeholder="Søk år" aria-label="Søk i år" /></th>
                  <th><input type="search" class="column-filter" data-filter-column="3" data-filter-mode="contains" placeholder="Søk sted" aria-label="Søk i steder" /></th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
              <?php
                $db = db();
                $sql = "
                  SELECT
                    b.PicID AS bilde_id,
                    COALESCE(NULLIF(TRIM(b.PicMotiv), ''), CONCAT('Bilde ', b.PicID)) AS title,
                    COALESCE(NULLIF(TRIM(f.ForfNavn), ''), NULLIF(TRIM(CONCAT_WS(' ', f.FNavn, f.ENavn)), '')) AS photographer,
                    CASE
                      WHEN b.TattYear IS NOT NULL AND b.TattYear > 0 THEN b.TattYear
                      WHEN b.TattYearSenest IS NOT NULL AND b.TattYearSenest > 0 THEN b.TattYearSenest
                      ELSE NULL
                    END AS photo_year,
                    NULLIF(TRIM(ms.StedTatt), '') AS location
                  FROM tblBilde b
                  LEFT JOIN tblForfatter f ON f.ForfID = b.ForfID
                  LEFT JOIN tblMotivSted ms ON ms.MStedID = b.MStedID
                  ORDER BY photo_year DESC, title ASC, location ASC
                ";

                $result = $db->query($sql);

                while ($row = $result->fetch_assoc()) {
                  $title = trim((string)($row['title'] ?? ''));
                  if ($title === '') {
                    $title = 'Bilde ' . (int)$row['bilde_id'];
                  }

                  $photographer = trim((string)($row['photographer'] ?? ''));
                  if ($photographer === '') {
                    $photographer = 'Ukjent fotograf';
                  }

                  $yearValue = $row['photo_year'];
                  $yearText = '';
                  if ($yearValue !== null && $yearValue !== '' && (int)$yearValue !== 0) {
                    $yearText = (string)$yearValue;
                  }

                  $location = trim((string)($row['location'] ?? ''));
                  if ($location === '') {
                    $location = 'Ukjent sted';
                  }

                  $detailUrl = url('openfil/detalj/bilde_detalj.php?id=' . (int)$row['bilde_id']);
              ?>
                <tr>
                  <td><?= h($title) ?></td>
                  <td><?= h($photographer) ?></td>
                  <td><?= h($yearText) ?></td>
                  <td><?= h($location) ?></td>
                  <td><a class="btn-small btn-small--icon" href="<?= h($detailUrl) ?>" aria-label="Vis bilde" title="Vis bilde">&#128065;</a></td>
                </tr>
              <?php
                }

                $result->free();
              ?>
              </tbody>
            </table>
          </div>
          <div id="bilder-pagination-bottom" class="table-pagination" data-pagination="bottom"></div>
        </div>

        <div id="modal-bilder" class="modal" hidden>
          <div class="modal-content">
            <button class="btn-icon modal-close" aria-label="Lukk detaljvisning">&times;</button>
            <div class="modal-body" id="modal-body-bilder">
              <!-- TODO: Fyll detaljvisning via server (egen side) eller klient (AJAX) -->
              <p>Detaljer vises her.</p>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</main>



<?php include('../includes/footer.php'); ?>

