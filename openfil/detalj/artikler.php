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

        <div class="toolbar">
          <label for="search-artikler" class="sr-only">Søk</label>
          <input id="search-artikler" type="search" class="search-bar" placeholder="Søk …" oninput="filterTableartikler()" />
        </div>

        <div class="table-wrap">
          <table id="table-artikler" class="data-table">
            <thead>
              <tr>
                <th>Tittel</th><th>Forfatter</th><th>År</th><th>Type</th><th>Detaljer</th>
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

        <div id="modal-artikler" class="modal" hidden>
          <div class="modal-content">
            <button class="btn-icon modal-close" onclick="closeModalartikler()" aria-label="Lukk detaljvisning">×</button>
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

<script>
function filterTableartikler() {
  const input = document.getElementById('search-artikler');
  const filter = (input.value || '').toLowerCase();
  const table = document.getElementById('table-artikler');
  const rows = table.tBodies[0].rows;
  for (let i = 0; i < rows.length; i++) {
    const txt = rows[i].textContent.toLowerCase();
    rows[i].style.display = txt.indexOf(filter) > -1 ? '' : 'none';
  }
}

function openModalartikler() {
  const m = document.getElementById('modal-artikler');
  m.hidden = false;
}

function closeModalartikler() {
  const m = document.getElementById('modal-artikler');
  m.hidden = true;
}
</script>

<?php include('../includes/footer.php'); ?>
