<?php
require_once('../includes/bootstrap.php');
$page_title = 'Artikler';
include('../includes/header.php');
?>
<main class="page-main">
  <div class="container">
    <div class="card">
      <div class="card-content">
        <h1 class="page-title"><?php echo h($page_title); ?></h1>

        <div class="table-wrap" data-table-wrap>
          <div id="artikler-pagination-top" class="table-pagination" data-pagination="top"></div>
          <div class="table-scroll">
            <table id="table-artikler" class="data-table" data-list-table data-rows-per-page="40" data-empty-message="Ingen treff.">
              <thead>
                <tr>
                  <th>Tittel</th><th>Forfatter</th><th>&Aring;r</th><th>Type</th><th>Detaljer</th>
                </tr>
                <tr class="filter-row">
                  <th><input type="search" class="column-filter" data-filter-column="0" data-filter-mode="contains" placeholder="S&oslash;k tittel" aria-label="S&oslash;k i titler" /></th>
                  <th><input type="search" class="column-filter" data-filter-column="1" data-filter-mode="contains" placeholder="S&oslash;k forfatter" aria-label="S&oslash;k i forfattere" /></th>
                  <th><input type="search" class="column-filter" data-filter-column="2" data-filter-mode="startsWith" placeholder="S&oslash;k &aring;r" aria-label="S&oslash;k i &aring;r" /></th>
                  <th></th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
              <?php
                $db = db();
                $db->query('SET SESSION group_concat_max_len = 2048');

                $sql = "
                  SELECT
                    a.ArtID AS artikkel_id,
                    COALESCE(NULLIF(TRIM(a.ArtTittel), ''), CONCAT('Artikkel ', a.ArtID)) AS title,
                    NULLIF(TRIM(a.ArtUnderTittel), '') AS subtitle,
                    a.ArtType AS art_type,
                    b.Year AS published_year,
                    GROUP_CONCAT(
                      DISTINCT COALESCE(
                        NULLIF(TRIM(f.ForfNavn), ''),
                        NULLIF(TRIM(CONCAT_WS(' ', f.FNavn, f.ENavn)), '')
                      )
                      ORDER BY f.ENavn, f.FNavn
                      SEPARATOR ', '
                    ) AS authors
                  FROM tblArtikkel a
                  LEFT JOIN tblBlad b ON b.BladID = a.BladID
                  LEFT JOIN tblxArtForf af ON af.ArtID = a.ArtID
                  LEFT JOIN tblForfatter f ON f.ForfID = af.ForfID
                  WHERE a.ArtID IS NOT NULL
                  GROUP BY a.ArtID, a.ArtTittel, a.ArtUnderTittel, a.ArtType, b.Year, b.BladNr, a.Side
                  ORDER BY b.Year DESC, b.BladNr ASC, a.Side ASC, a.ArtTittel ASC, a.ArtUnderTittel ASC
                ";

                $result = $db->query($sql);

                if ($result->num_rows === 0) {
                  echo '<tr data-empty-row="true"><td colspan="5">Ingen artikler funnet.</td></tr>';
                } else {
                  while ($row = $result->fetch_assoc()) {
                    $title = trim((string)($row['title'] ?? ''));
                    $subtitle = trim((string)($row['subtitle'] ?? ''));
                    if ($title === '') {
                      $title = 'Artikkel ' . (int)$row['artikkel_id'];
                    }

                    $authors = trim((string)($row['authors'] ?? ''));
                    if ($authors === '') {
                      $authors = 'Ukjent forfatter';
                    }

                    $detail_url = url('openfil/detalj/artikkel_detalj.php?id=' . $row['artikkel_id']);

                    echo '<tr>';
                    echo '<td>' . h($title);
                    if ($subtitle !== '') {
                      echo '<div class="muted">' . h($subtitle) . '</div>';
                    }
                    echo '</td>';
                    echo '<td>' . h($authors) . '</td>';
                    echo '<td>' . h($row['published_year'] ?? '') . '</td>';
                    echo '<td>' . h($row['art_type'] ?? '') . '</td>';
                    echo '<td><a class="btn-small btn-small--icon" href="' . h($detail_url) . '" aria-label="Vis artikkel" title="Vis artikkel">&#128065;</a></td>';
                    echo '</tr>';
                  }
                }

                $result->free();
              ?>
              </tbody>
            </table>
          </div>
          <div id="artikler-pagination-bottom" class="table-pagination" data-pagination="bottom"></div>
        </div>

        <div id="modal-artikler" class="modal" hidden>
          <div class="modal-content">
            <button class="btn-icon modal-close" aria-label="Lukk detaljvisning">Ã</button>
            <div class="modal-body" id="modal-body-artikler">
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



