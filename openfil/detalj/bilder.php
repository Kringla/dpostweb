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

        <div class="toolbar">
          <label for="search-bilder" class="sr-only">Søk</label>
          <input id="search-bilder" type="search" class="search-bar" placeholder="Søk …" oninput="filterTablebilder()" />
        </div>

        <div class="table-wrap">
          <table id="table-bilder" class="data-table">
            <thead>
              <tr>
                <th>Tittel</th><th>Fotograf</th><th>År</th><th>DIMU-kode</th><th>Detaljer</th>
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

        <div id="modal-bilder" class="modal" hidden>
          <div class="modal-content">
            <button class="btn-icon modal-close" onclick="closeModalbilder()" aria-label="Lukk detaljvisning">×</button>
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

<script>
function filterTablebilder() {
  const input = document.getElementById('search-bilder');
  const filter = (input.value || '').toLowerCase();
  const table = document.getElementById('table-bilder');
  const rows = table.tBodies[0].rows;
  for (let i = 0; i < rows.length; i++) {
    const txt = rows[i].textContent.toLowerCase();
    rows[i].style.display = txt.indexOf(filter) > -1 ? '' : 'none';
  }
}

function openModalbilder() {
  const m = document.getElementById('modal-bilder');
  m.hidden = false;
}

function closeModalbilder() {
  const m = document.getElementById('modal-bilder');
  m.hidden = true;
}
</script>

<?php include('../includes/footer.php'); ?>
