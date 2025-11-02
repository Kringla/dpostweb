<?php
// index.php - Landing page for DpostWeb
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$page_title = 'Søk i Dampskibspostens innhold';
$body_class = 'home';
$activeNav = 'home';
include __DIR__ . '/includes/header.php';
?>
<main class="home-main">
  <div class="container">
    <div class="home-hero">
      <img src="<?php echo h(asset('assets/img/dposten-logo@6x.jpg')); ?>" alt="Dampskibspostens emblem" class="home-hero__image" />
    </div>

    <h1 class="spesiell-tekst"><?php echo h($page_title); ?></h1>

    <div class="home-feature">
      <img src="<?php echo h(asset('assets/img/dampskip.jpg')); ?>" alt="Historisk dampfartø;y" class="home-feature__image" />
    </div>

    <p class="lead muted text-center">
      Under hver knapp finner du lister som viser elementer i Dampskibspostens 125 utgaver gjennom ca. 50 år.
      Alle artikler er registrert med tittel. I perioden frem til 1998 finnes flere detaljer enn sist i perioden. Dette vil endre seg ettersom registreringsarbeidet skrider frem. 
      Tegnet &laquo;#&raquo; brukes i noen av tabellenes kolonnetitler, det står for 'antall'.  
    </p>

    <div class="card centered-card">
      <div class="card-content">
        <h2>Velg hva du vil utforske</h2>
        <div class="home-actions" role="list">
          <a role="listitem" class="tile blue" href="<?= h(url('openfil/utgaver.php')) ?>">Utgaver</a>
          <a role="listitem" class="tile blue" href="<?= h(url('openfil/tema_liste.php')) ?>">Tema liste</a>
          <a role="listitem" class="tile blue" href="<?= h(url('openfil/artikler.php')) ?>">Artikler</a>
          <a role="listitem" class="tile blue" href="<?= h(url('openfil/bilder.php')) ?>">Bilder</a>
          <a role="listitem" class="tile blue" href="<?= h(url('openfil/personer.php')) ?>">Personer</a>
          <a role="listitem" class="tile blue" href="<?= h(url('openfil/fartoyer.php')) ?>">Fartø;yer</a>
          <a role="listitem" class="tile blue" href="<?= h(url('openfil/forfattere.php')) ?>">Forfattere</a>
          <a role="listitem" class="tile blue" href="<?= h(url('openfil/fotografer.php')) ?>">Fotografer</a>
          <a role="listitem" class="tile blue" href="<?= h(url('openfil/forening_org.php')) ?>">Forening/org</a>
          <a role="listitem" class="tile blue" href="<?= h(url('openfil/verft.php')) ?>">Verft</a>
          <a role="listitem" class="tile blue" href="<?= h(url('openfil/rederier.php')) ?>">Rederier</a>
          <a role="listitem" class="tile blue" href="<?= h(url('openfil/publikasjoner.php')) ?>">Omtalte bøker</a>
          <a role="listitem" class="tile blue" href="<?= h(url('openfil/pub_forfattere.php')) ?>">Bøkers forfattere</a>
          <a role="listitem" class="tile blue" href="<?= h(url('openfil/annonsor.php')) ?>">Annonsører</a>
        </div>

        <h4>Tips: Du kan sø;ke i tabellene og åpne detaljer fra hver rad.</h4>
      </div>
    </div>

    <div class="mt-2l">
      <p class="muted text-center">
        Bruk sø;kefelt og &laquo;øyet&raquo; for å se detaljer. &laquo;%&raquo; kan benyttes som jokertegn.
        I skjermbilder med tabell til venstre og tilhørende informasjon til høyre, klikk alltid på venstre felt i tabellen til venstre for å se korrekt informasjon i tabellene/felten til høyre i skjermbildet. Klikk i felt med '^' i tittelen for å se lenkeinnholdet.
      </p>

    </div>
  </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

