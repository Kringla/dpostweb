<?php
require_once('../includes/bootstrap.php');
$page_title = 'Forening / org';
include('../includes/header.php');
?>
<main class="page-main">
  <div class="container">
    <div class="card">
      <div class="card-content">
        <h1 class="page-title"><?php echo h($page_title); ?></h1>


        <div class="table-wrap" data-table-wrap>
          <div id="forening-pagination-top" class="table-pagination" data-pagination="top"></div>
          <div class="table-scroll">
            <table id="table-foreningorg" class="data-table" data-list-table data-rows-per-page="40" data-empty-message="Ingen treff.">
              <thead>
                <tr>
                  <th>Navn</th><th>Lokasjon</th><th class="count-col">Antall omtaler</th><th>Detaljer</th>
                </tr>
                <tr class="filter-row">
                  <th><input type="search" class="column-filter" data-filter-column="0" data-filter-mode="contains" placeholder="S&oslash;k navn" aria-label="S&oslash;k i navn" /></th>
                  <th><input type="search" class="column-filter" data-filter-column="1" data-filter-mode="contains" placeholder="S&oslash;k lokasjon" aria-label="S&oslash;k i lokasjoner" /></th>
                  <th></th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
              <?php
                $db = db();
                $sql = "
                  SELECT
                    o.OrgID AS org_id,
                    COALESCE(NULLIF(TRIM(o.OrgNavn), ''), CONCAT(''Organisasjon '', o.OrgID)) AS name,
                    TRIM(CONCAT_WS(', ', NULLIF(o.Poststed, ''), NULLIF(o.Postnr, ''))) AS location,
                    COALESCE(o.AntDP, 0) AS mention_count
                  FROM vwOrgDposten o
                  ORDER BY name ASC
                ";
                $result = $db->query($sql);
                while ($row = $result->fetch_assoc()) {
                  $name = trim((string)($row['name'] ?? ''));
                  if ($name === '') {
                    $name = 'Organisasjon ' . (int)$row['org_id'];
                  }
                  $location = trim((string)($row['location'] ?? ''));
                  $mentions = (int)($row['mention_count'] ?? 0);
                  $detailUrl = url('openfil/detalj/forening_org_detalj.php?id=' . (int)$row['org_id']);
              ?>
                <tr>
                  <td><?= h($name) ?></td>
                  <td><?= h($location) ?></td>
                  <td class="count-col"><?= h($mentions) ?></td>
                  <td><a class="btn-small btn-small--icon" href="<?= h($detailUrl) ?>" aria-label="Vis forening" title="Vis forening">&#128065;</a></td>
                </tr>
              <?php
                }
                $result->free();
              ?>
              </tbody>
            </table>
          </div>
          <div id="forening-pagination-bottom" class="table-pagination" data-pagination="bottom"></div>
        </div>

        <div id="modal-foreningorg" class="modal" hidden>
          <div class="modal-content">
            <button class="btn-icon modal-close" aria-label="Lukk detaljvisning">&times;</button>
            <div class="modal-body" id="modal-body-foreningorg">
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




