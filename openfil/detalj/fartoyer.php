<?php
require_once('../includes/bootstrap.php');
$page_title = 'Fartøyer';
include('../includes/header.php');
?>
<main class="page-main">
  <div class="container">
    <div class="card">
      <div class="card-content">
        <h1 class="page-title"><?php echo h($page_title); ?></h1>

        <div class="toolbar">
          <label for="search-fartoyer" class="sr-only">Søk</label>
          <input id="search-fartoyer" type="search" class="search-bar" placeholder="Søk …" oninput="filterTablefartoyer()" />
        </div>

        <div class="table-wrap">
          <table id="table-fartoyer" class="data-table">
            <thead>
              <tr>
                <th>Navn</th><th>Type</th><th>Byggeår</th><th>Rederi</th><th>Detaljer</th>
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

        <div id="modal-fartoyer" class="modal" hidden>
          <div class="modal-content">
            <button class="btn-icon modal-close" onclick="closeModalfartoyer()" aria-label="Lukk detaljvisning">×</button>
            <div class="modal-body" id="modal-body-fartoyer">
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
function filterTablefartoyer() {
  const input = document.getElementById('search-fartoyer');
  const filter = (input.value || '').toLowerCase();
  const table = document.getElementById('table-fartoyer');
  const rows = table.tBodies[0].rows;
  for (let i = 0; i < rows.length; i++) {
    const txt = rows[i].textContent.toLowerCase();
    rows[i].style.display = txt.indexOf(filter) > -1 ? '' : 'none';
  }
}

function openModalfartoyer() {
  const m = document.getElementById('modal-fartoyer');
  m.hidden = false;
}

function closeModalfartoyer() {
  const m = document.getElementById('modal-fartoyer');
  m.hidden = true;
}
</script>

<?php include('../includes/footer.php'); ?>
