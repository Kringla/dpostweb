<?php
require_once('../includes/bootstrap.php');
$page_title = 'Verft';
include('../includes/header.php');
?>
<main class="page-main">
  <div class="container">
    <div class="card">
      <div class="card-content">
        <h1 class="page-title"><?php echo h($page_title); ?></h1>

        <div class="toolbar">
          <label for="search-verft" class="sr-only">Søk</label>
          <input id="search-verft" type="search" class="search-bar" placeholder="Søk …" oninput="filterTableverft()" />
        </div>

        <div class="table-wrap">
          <table id="table-verft" class="data-table">
            <thead>
              <tr>
                <th>Verft</th><th>Land</th><th>Antall fartøy</th><th>Detaljer</th>
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

        <div id="modal-verft" class="modal" hidden>
          <div class="modal-content">
            <button class="btn-icon modal-close" onclick="closeModalverft()" aria-label="Lukk detaljvisning">×</button>
            <div class="modal-body" id="modal-body-verft">
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
function filterTableverft() {
  const input = document.getElementById('search-verft');
  const filter = (input.value || '').toLowerCase();
  const table = document.getElementById('table-verft');
  const rows = table.tBodies[0].rows;
  for (let i = 0; i < rows.length; i++) {
    const txt = rows[i].textContent.toLowerCase();
    rows[i].style.display = txt.indexOf(filter) > -1 ? '' : 'none';
  }
}

function openModalverft() {
  const m = document.getElementById('modal-verft');
  m.hidden = false;
}

function closeModalverft() {
  const m = document.getElementById('modal-verft');
  m.hidden = true;
}
</script>

<?php include('../includes/footer.php'); ?>
