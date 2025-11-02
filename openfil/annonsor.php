<?php
require_once('../includes/bootstrap.php');

$page_title = 'Annonsører';
$activeNav = 'advertisers';
$db = db();

$pdfBaseUrl = 'https://ihlen.net/installs/DPfilerPDF/';

$annonsorer = [];
$listSql = "
  SELECT
    AnnID,
    TRIM(CONCAT_WS(', ', NULLIF(AnnNavn, ''), NULLIF(AnnAvd, ''))) AS display_name,
    NULLIF(Poststed, '') AS poststed,
    NULLIF(WebSide, '') AS web
  FROM tblAnnonsor
  ORDER BY AnnNavn ASC, AnnAvd ASC, AnnID ASC
";

$listResult = $db->query($listSql);
if ($listResult instanceof mysqli_result) {
    while ($row = $listResult->fetch_assoc()) {
        $name = trim((string)($row['display_name'] ?? ''));
        if ($name === '') {
            $name = 'Annonsør ' . (int)$row['AnnID'];
        }

        $annonsorer[] = [
            'id' => (int)$row['AnnID'],
            'name' => $name,
            'poststed' => trim((string)($row['poststed'] ?? '')),
            'web' => trim((string)($row['web'] ?? '')),
        ];
    }
    $listResult->free();
}

$selectedAnnonsorId = filter_input(INPUT_GET, 'annonsor_id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$selectedAnnonsorId = $selectedAnnonsorId !== false ? $selectedAnnonsorId : null;

if (!empty($annonsorer)) {
    $valid = false;
    if ($selectedAnnonsorId !== null) {
        foreach ($annonsorer as $entry) {
            if ($entry['id'] === $selectedAnnonsorId) {
                $valid = true;
                break;
            }
        }
    }

    if ($selectedAnnonsorId === null || !$valid) {
        $selectedAnnonsorId = $annonsorer[0]['id'];
    }
}

$selectedAnnonsor = null;
$annonser = [];
$bladReferanser = [];

if ($selectedAnnonsorId !== null) {
    $detailSql = "
      SELECT AnnID, AnnNavn, AnnAvd, Adresse, Postnr, Poststed, Land, Utland,
             EpostAdresse, WebSide, KontaktPers, KontaktEpost, KontaktTelf, Notater
      FROM tblAnnonsor
      WHERE AnnID = ?
      LIMIT 1
    ";
    $detailStmt = $db->prepare($detailSql);
    if ($detailStmt instanceof mysqli_stmt) {
        $detailStmt->bind_param('i', $selectedAnnonsorId);
        $detailStmt->execute();
        $detailResult = $detailStmt->get_result();
        $annRow = $detailResult ? $detailResult->fetch_assoc() : null;
        $detailStmt->close();

        if ($annRow) {
            $displayName = trim((string)($annRow['AnnNavn'] ?? ''));
            if (!empty($annRow['AnnAvd'])) {
                $displayName = trim(trim($displayName . ', ' . $annRow['AnnAvd']), ', ');
            }
            if ($displayName === '') {
                $displayName = 'Annonsør ' . (int)$annRow['AnnID'];
            }

            $post = trim(trim(($annRow['Postnr'] ?? '') . ' ' . ($annRow['Poststed'] ?? '')));

            $selectedAnnonsor = [
                'id' => (int)$annRow['AnnID'],
                'display_name' => $displayName,
                'name' => trim((string)($annRow['AnnNavn'] ?? '')),
                'department' => trim((string)($annRow['AnnAvd'] ?? '')),
                'adresse' => trim((string)($annRow['Adresse'] ?? '')),
                'post' => $post,
                'land' => trim((string)($annRow['Land'] ?? '')),
                'utland' => (int)($annRow['Utland'] ?? 0) === 1,
                'epost' => trim((string)($annRow['EpostAdresse'] ?? '')),
                'web' => trim((string)($annRow['WebSide'] ?? '')),
                'kontaktpers' => trim((string)($annRow['KontaktPers'] ?? '')),
                'kontakt_epost' => trim((string)($annRow['KontaktEpost'] ?? '')),
                'kontakt_telf' => trim((string)($annRow['KontaktTelf'] ?? '')),
                'notater' => trim((string)($annRow['Notater'] ?? '')),
            ];
        }
    }

    if ($selectedAnnonsor !== null) {
        $adsSql = "
          SELECT
            a.PRID,
            COALESCE(NULLIF(TRIM(a.AnnonseInnh), ''), CONCAT('Annonse ', a.PRID)) AS title,
            NULLIF(TRIM(a.AnnonseSize), '') AS size,
            a.AvtaltPris
          FROM tblAnnonse a
          WHERE a.AnnID = ?
          ORDER BY a.PRID DESC
        ";
        $adsStmt = $db->prepare($adsSql);
        if ($adsStmt instanceof mysqli_stmt) {
            $adsStmt->bind_param('i', $selectedAnnonsorId);
            $adsStmt->execute();
            $adsResult = $adsStmt->get_result();
            if ($adsResult instanceof mysqli_result) {
                while ($row = $adsResult->fetch_assoc()) {
                    $annonser[] = [
                        'id' => (int)$row['PRID'],
                        'title' => trim((string)($row['title'] ?? '')),
                        'size' => trim((string)($row['size'] ?? '')),
                        'price' => $row['AvtaltPris'],
                    ];
                }
                $adsResult->free();
            }
            $adsStmt->close();
        }

        $refSql = "
          SELECT
            b.Year,
            b.BladNr,
            xb.Side,
            b.Lenke
          FROM tblAnnonse a
          INNER JOIN tblxAnnonseBlad xb ON xb.PRID = a.PRID
          LEFT JOIN tblBlad b ON b.BladID = xb.BladID
          WHERE a.AnnID = ?
          ORDER BY
            COALESCE(b.Year, 0) DESC,
            COALESCE(b.BladNr, 0) ASC,
            COALESCE(xb.Side, 0) ASC
        ";
        $refStmt = $db->prepare($refSql);
        if ($refStmt instanceof mysqli_stmt) {
            $refStmt->bind_param('i', $selectedAnnonsorId);
            $refStmt->execute();
            $refResult = $refStmt->get_result();
            if ($refResult instanceof mysqli_result) {
                while ($row = $refResult->fetch_assoc()) {
                    $link = trim((string)($row['Lenke'] ?? ''));
                    $linkHref = '';
                    if ($link !== '') {
                        if (preg_match('~^https?://~i', $link)) {
                            $linkHref = $link;
                        } else {
                            $linkHref = rtrim($pdfBaseUrl, '/') . '/' . ltrim($link, '/');
                        }
                    }

                    $linkText = '';
                    if ($link !== '') {
                        $basename = basename($link);
                        $linkText = $basename !== '' ? $basename : $link;
                    }

                    $bladReferanser[] = [
                        'year' => $row['Year'],
                        'number' => $row['BladNr'],
                        'side' => $row['Side'],
                        'link' => $link,
                        'link_href' => $linkHref,
                        'link_text' => $linkText !== '' ? $linkText : 'Åpne',
                    ];
                }
                $refResult->free();
            }
            $refStmt->close();
        }
    }
}

include('../includes/header.php');
?>
<main class="page-main">
  <div class="container">
    <div class="card">
      <div class="card-content">
        <h1 class="page-title"><?php echo h($page_title); ?></h1>

        <div class="dual-table-layout">
          <section class="dual-table-panel dual-table-panel--primary">
            <div class="table-wrap" data-table-wrap>
              <div class="table-pagination" data-pagination="top"></div>
              <div class="table-scroll">
                <table id="table-annonsorer" class="data-table" data-list-table data-rows-per-page="25" data-empty-message="Ingen treff.">
                  <thead>
                    <tr>
                      <th>Annonsør</th>
                      <th>Poststed</th>
                      <th>Web</th>
                    </tr>
                    <tr class="filter-row">
                      <th><input type="search" class="column-filter" data-filter-column="0" data-filter-mode="contains" placeholder="Søk navn" aria-label="Søk i annonsører" /></th>
                      <th><input type="search" class="column-filter" data-filter-column="1" data-filter-mode="contains" placeholder="Søk poststed" aria-label="Søk i poststed" /></th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php if (empty($annonsorer)): ?>
                    <tr data-empty-row="true"><td colspan="3">Ingen annonsører funnet.</td></tr>
                  <?php else: ?>
                    <?php foreach ($annonsorer as $entry): ?>
                      <?php
                        $rowId = 'annonsør-' . $entry['id'];
                        $isSelected = ($entry['id'] === $selectedAnnonsorId);
                        $queryParams = $_GET;
                        $queryParams['annonsor_id'] = $entry['id'];
                        $selectUrl = url('openfil/annonsor.php');
                        if (!empty($queryParams)) {
                            $selectUrl .= '?' . http_build_query($queryParams);
                        }
                        $selectUrl .= '#' . $rowId;
                      ?>
                      <tr id="<?php echo h($rowId); ?>" class="<?php echo $isSelected ? 'is-selected' : ''; ?>">
                        <td>
                          <a class="navigable-link"
                            href="<?php echo h($selectUrl); ?>"<?php echo $isSelected ? ' aria-current="page"' : ''; ?>>
                            <?php echo h($entry['name']); ?>
                          </a>
                        </td>
                        <td><?php echo h($entry['poststed'] !== '' ? $entry['poststed'] : '-'); ?></td>
                        <td>
                          <?php if ($entry['web'] !== ''): ?>
                            <a href="<?php echo h($entry['web']); ?>" target="_blank" rel="noopener">Nettside</a>
                          <?php else: ?>
                            -
                          <?php endif; ?>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                  </tbody>
                </table>
              </div>
              <div class="table-pagination" data-pagination="bottom"></div>
            </div>
          </section>

          <section class="dual-table-panel dual-table-panel--secondary">
            <div class="secondary-card">
              <?php if ($selectedAnnonsor === null): ?>
                <h2 class="secondary-title">Detaljer</h2>
                <p class="muted">Velg en annonsør for å se detaljer.</p>
              <?php else: ?>
                <h2 class="secondary-title">Detaljer for <?php echo h($selectedAnnonsor['display_name']); ?></h2>

                <div class="selection-summary">
                  <div>
                    <strong>Kontakt</strong>
                    <?php echo h($selectedAnnonsor['kontaktpers'] !== '' ? $selectedAnnonsor['kontaktpers'] : '-'); ?>
                  </div>
                  <div>
                    <strong>Telefon</strong>
                    <?php echo h($selectedAnnonsor['kontakt_telf'] !== '' ? $selectedAnnonsor['kontakt_telf'] : '-'); ?>
                  </div>
                  <div>
                    <strong>Web</strong>
                    <?php if ($selectedAnnonsor['web'] !== ''): ?>
                      <a href="<?php echo h($selectedAnnonsor['web']); ?>" target="_blank" rel="noopener">Nettside</a>
                    <?php else: ?>
                      -
                    <?php endif; ?>
                  </div>
                </div>

                <dl class="detail-list">
                  <dt>Navn</dt>
                  <dd><?php echo h($selectedAnnonsor['name'] !== '' ? $selectedAnnonsor['name'] : '-'); ?></dd>
                  <dt>Avdeling</dt>
                  <dd><?php echo h($selectedAnnonsor['department'] !== '' ? $selectedAnnonsor['department'] : '-'); ?></dd>
                  <dt>Adresse</dt>
                  <dd><?php echo h($selectedAnnonsor['adresse'] !== '' ? $selectedAnnonsor['adresse'] : '-'); ?></dd>
                  <dt>Post</dt>
                  <dd><?php echo h($selectedAnnonsor['post'] !== '' ? $selectedAnnonsor['post'] : '-'); ?></dd>
                  <dt>Land</dt>
                  <dd><?php echo h($selectedAnnonsor['land'] !== '' ? $selectedAnnonsor['land'] : '-'); ?><?php echo $selectedAnnonsor['utland'] ? ' (utland)' : ''; ?></dd>
                  <dt>E-post</dt>
                  <dd>
                    <?php if ($selectedAnnonsor['epost'] !== ''): ?>
                      <a href="mailto:<?php echo h($selectedAnnonsor['epost']); ?>"><?php echo h($selectedAnnonsor['epost']); ?></a>
                    <?php else: ?>
                      -
                    <?php endif; ?>
                  </dd>
                  <dt>Kontakt e-post</dt>
                  <dd>
                    <?php if ($selectedAnnonsor['kontakt_epost'] !== ''): ?>
                      <a href="mailto:<?php echo h($selectedAnnonsor['kontakt_epost']); ?>"><?php echo h($selectedAnnonsor['kontakt_epost']); ?></a>
                    <?php else: ?>
                      -
                    <?php endif; ?>
                  </dd>
                </dl>

                <?php if ($selectedAnnonsor['notater'] !== ''): ?>
                  <h3>Notater</h3>
                  <p><?php echo nl2br(h($selectedAnnonsor['notater'])); ?></p>
                <?php endif; ?>

                <h3>Annonser</h3>
                <div class="table-wrap table-wrap--static">
                  <div class="table-scroll">
                    <table class="data-table data-table--compact">
                      <thead>
                        <tr>
                          <th>ID</th>
                          <th>Tittel</th>
                          <th>Størrelse</th>
                          <th>Pris</th>
                        </tr>
                      </thead>
                      <tbody>
                      <?php if (empty($annonser)): ?>
                        <tr><td colspan="4">Ingen annonser registrert.</td></tr>
                      <?php else: ?>
                        <?php foreach ($annonser as $annonse): ?>
                          <tr>
                            <td><?php echo h((string)$annonse['id']); ?></td>
                            <td><?php echo h($annonse['title'] !== '' ? $annonse['title'] : '-'); ?></td>
                            <td><?php echo h($annonse['size'] !== '' ? $annonse['size'] : '-'); ?></td>
                            <td>
                              <?php
                                if ($annonse['price'] === null || $annonse['price'] === '') {
                                    echo '-';
                                } else {
                                    echo h(number_format((float)$annonse['price'], 2, ',', ' '));
                                }
                              ?>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      <?php endif; ?>
                      </tbody>
                    </table>
                  </div>
                </div>

                <h3>Bladhenvisninger</h3>
                <div class="table-wrap table-wrap--static">
                  <div class="table-scroll">
                    <table class="data-table data-table--compact">
                      <thead>
                        <tr>
                          <th>År</th>
                          <th>Bladnr</th>
                          <th>Side</th>
                          <th>Lenke</th>
                        </tr>
                      </thead>
                      <tbody>
                      <?php if (empty($bladReferanser)): ?>
                        <tr><td colspan="4">Ingen koblinger funnet.</td></tr>
                      <?php else: ?>
                        <?php foreach ($bladReferanser as $referanse): ?>
                          <tr>
                            <td><?php echo h($referanse['year'] !== null ? (string)$referanse['year'] : '-'); ?></td>
                            <td><?php echo h($referanse['number'] !== null ? (string)$referanse['number'] : '-'); ?></td>
                            <td><?php echo h($referanse['side'] !== null ? (string)$referanse['side'] : '-'); ?></td>
                            <td>
                              <?php if ($referanse['link_href'] !== ''): ?>
                                <a href="<?php echo h($referanse['link_href']); ?>" target="_blank" rel="noopener">
                                  <?php echo h($referanse['link_text']); ?>
                                </a>
                              <?php else: ?>
                                -
                              <?php endif; ?>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      <?php endif; ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              <?php endif; ?>
            </div>
          </section>
        </div>
      </div>
    </div>
  </div>
</main>

<?php include('../includes/footer.php'); ?>



