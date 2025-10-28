<?php
// index.php — Landing page for DpostWeb
// Uses shared includes and constants as defined in project structure docs.
require_once __DIR__ . '/includes/bootstrap.php'; // loads config, constants, functions
// No auth required on landing page

$page_title = 'Søk i Dampskibspostens innhold';
$body_class = 'home';
include __DIR__ . '/includes/header.php';
?>
<main class="home-main">
  <div class="container">
    <h1><?php echo h($page_title); ?></h1>
    <p class="lead muted">
      Under hver knapp finner du lister som viser elementer i Dampskibspostens 125 utgaver gjennom ca. 50 år.
      Bruk søkefelt og «øyet» for å se detaljer. «%» kan benyttes som jokertegn.
    </p>

    <nav class="nav tabs" aria-label="Hovedvalg">
      <ul class="tabs-list" role="list">
        <li class="tab-item">
          <a class="tab-link is-active" href="<?php echo BASE_URL; ?>index.php" aria-current="page">Hjem</a>
        </li>
        <li class="tab-item">
          <a class="tab-link" href="<?php echo BASE_URL; ?>openfil/change_password.php">Bruker</a>
        </li>
        <?php if (is_admin() || is_super()): ?>
        <li class="tab-item">
          <a class="tab-link" href="<?php echo BASE_URL; ?>protfil/param_admin.php">Admin</a>
        </li>
        <?php endif; ?>
      </ul>
    </nav>

    <div class="card centered-card">
      <div class="card-content">
        <h2>Velg hva du vil utforske</h2>
        <div class="home-grid" role="list">
          <a role="listitem" class="tile blue" href="<?php echo BASE_URL; ?>openfil/utgaver.php">Utgaver</a>
          <a role="listitem" class="tile blue" href="<?php echo BASE_URL; ?>openfil/tema_liste.php">Tema liste</a>
          <a role="listitem" class="tile blue" href="<?php echo BASE_URL; ?>openfil/artikler.php">Artikler</a>
          <a role="listitem" class="tile blue" href="<?php echo BASE_URL; ?>openfil/bilder.php">Bilder</a>
          <a role="listitem" class="tile blue" href="<?php echo BASE_URL; ?>openfil/personer.php">Personer</a>
          <a role="listitem" class="tile blue" href="<?php echo BASE_URL; ?>openfil/fartoyer.php">Fartøyer</a>
          <a role="listitem" class="tile blue" href="<?php echo BASE_URL; ?>openfil/forfattere.php">Forfattere</a>
          <a role="listitem" class="tile blue" href="<?php echo BASE_URL; ?>openfil/fotografer.php">Fotografer</a>
          <a role="listitem" class="tile blue" href="<?php echo BASE_URL; ?>openfil/forening_org.php">Forening/org</a>
          <a role="listitem" class="tile blue" href="<?php echo BASE_URL; ?>openfil/verft.php">Verft</a>
          <a role="listitem" class="tile blue" href="<?php echo BASE_URL; ?>openfil/rederier.php">Rederier</a>

          <?php if (is_admin() || is_super()): ?>
            <a role="listitem" class="tile admin" href="<?php echo BASE_URL; ?>protfil/param_admin.php">Administrasjon</a>
          <?php endif; ?>
        </div>

        <h4>Tips: Du kan søke i tabellene og åpne detaljer fra hver rad.</h4>
      </div>
    </div>

    <div class="mt-2l">
      <p class="muted">
        Alle artikler er registrert med tittel, fra 1998–2023 finnes flere detaljer. I tabellene viser tegnet «#» antall.
        Lenken «Se PDF» åpner den aktuelle utgaven (der tilgjengelig), mens «Se data» åpner eksterne kilder.
      </p>
    </div>
  </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
