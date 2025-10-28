<?php
require_once('../includes/bootstrap.php');
$page_title = 'Tema liste';
include('../includes/header.php');
?>
<main class="page-main">
  <div class="container">
    <div class="card">
      <div class="card-content">
        <h1 class="page-title"><?php echo h($page_title); ?></h1>

        <div class="toolbar">
          <label for="search-temaliste" class="sr-only">Søk</label>
          <input id="search-temaliste" type="search" class="search-bar" placeholder="Søk …" oninput="filterTabletemaliste()" />
        </div>

        <div class="table-wrap">
          <table id="table-temaliste" class="data-table">
            <thead>
              <tr>
                <th>Tema</th><th>Artikler</th><th>Bilder</th><th>Detaljer</th>
              </tr>
            </thead>
            <tbody>
              <!-- TODO: Hent data fra database og echo <tr> … </tr> her. Placeholder-felter -->
              <!-- Eksempel på kolonne-mapping:
                   'title'        => Tittel
                   'author'       => Forfatter
                   'year'         => År
                   'count'        => Antall
                   'image_count'  => Antall bilder
                   'type'         => Type
                   'place'        => Lokasjon/Hjemsted
              -->
            </tbody>
          </table>
        </div>

        <div id="modal-temaliste" class="modal" hidden>
          <div class="modal-content">
            <button class="btn-icon modal-close" onclick="closeModaltemaliste()" aria-label="Lukk detaljvisning">×</button>
            <div class="modal-body" id="modal-body-temaliste">
              <!-- TODO: Fyll detaljvisning via server (egen side) eller klient (AJAX) -->
              <p>Detaljer vises her.</p>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</main>

<script>
function filterTabletemaliste() {
  const input = document.getElementById('search-temaliste');
  const filter = (input.value || '').toLowerCase();
  const table = document.getElementById('table-temaliste');
  const rows = table.tBodies[0].rows;
  for (let i = 0; i < rows.length; i++) {
    const txt = rows[i].textContent.toLowerCase();
    rows[i].style.display = txt.indexOf(filter) > -1 ? '' : 'none';
  }
}

function openModaltemaliste() {
  const m = document.getElementById('modal-temaliste');
  m.hidden = false;
}

function closeModaltemaliste() {
  const m = document.getElementById('modal-temaliste');
  m.hidden = true;
}
</script>

<?php include('../includes/footer.php'); ?>
