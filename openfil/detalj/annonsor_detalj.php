<?php
require_once('../../includes/bootstrap.php');

$page_title = 'Detaljer - Annonsor';
$activeNav = 'advertisers';
$db = db();

$pdfBaseUrl = 'https://ihlen.net/installs/DPfilerPDF/';

$annonsorId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$annonsorId = $annonsorId !== false ? $annonsorId : null;

$annonsor = null;
$annonser = [];
$bladReferanser = [];
$error = null;

if ($annonsorId === null) {
    http_response_code(400);
    $error = 'Foresporselen mangler et gyldig annonsor-id.';
} else {
    $detailSql = "
      SELECT AnnID, AnnNavn, AnnAvd, Adresse, Postnr, Poststed, Land, Utland,
             EpostAdresse, WebSide, KontaktPers, KontaktEpost, KontaktTelf, Notater
      FROM tblAnnonsor
      WHERE AnnID = ?
      LIMIT 1
    ";
    $detailStmt = $db->prepare($detailSql);
    if ($detailStmt instanceof mysqli_stmt) {
        $detailStmt->bind_param('i', $annonsorId);
        $detailStmt->execute();
        $detailResult = $detailStmt->get_result();
        $row = $detailResult ? $detailResult->fetch_assoc() : null;
        $detailStmt->close();

        if (!$row) {
            http_response_code(404);
            $error = 'Annonsor ikke funnet.';
        } else {
            $displayName = trim((string)($row['AnnNavn'] ?? ''));
            if (!empty($row['AnnAvd'])) {
                $displayName = trim(trim($displayName . ', ' . $row['AnnAvd']), ', ');
            }
            if ($displayName === '') {
                $displayName = 'Annonsor ' . (int)$row['AnnID'];
            }

            $post = trim(trim(($row['Postnr'] ?? '') . ' ' . ($row['Poststed'] ?? '')));

            $annonsor = [
                'id' => (int)$row['AnnID'],
                'display_name' => $displayName,
                'name' => trim((string)($row['AnnNavn'] ?? '')),
                'department' => trim((string)($row['AnnAvd'] ?? '')),
                'adresse' => trim((string)($row['Adresse'] ?? '')),
                'post' => $post,
                'land' => trim((string)($row['Land'] ?? '')),
                'utland' => (int)($row['Utland'] ?? 0) === 1,
                'epost' => trim((string)($row['EpostAdresse'] ?? '')),
                'web' => trim((string)($row['WebSide'] ?? '')),
                'kontaktpers' => trim((string)($row['KontaktPers'] ?? '')),
                'kontakt_epost' => trim((string)($row['KontaktEpost'] ?? '')),
                'kontakt_telf' => trim((string)($row['KontaktTelf'] ?? '')),
                'notater' => trim((string)($row['Notater'] ?? '')),
            ];

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
                $adsStmt->bind_param('i', $annonsorId);
                $adsStmt->execute();
                $adsResult = $adsStmt->get_result();
                if ($adsResult instanceof mysqli_result) {
                    while ($adRow = $adsResult->fetch_assoc()) {
                        $annonser[] = [
                            'id' => (int)$adRow['PRID'],
                            'title' => trim((string)($adRow['title'] ?? '')),
                            'size' => trim((string)($adRow['size'] ?? '')),
                            'price' => $adRow['AvtaltPris'],
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
                $refStmt->bind_param('i', $annonsorId);
                $refStmt->execute();
                $refResult = $refStmt->get_result();
                if ($refResult instanceof mysqli_result) {
                    while ($refRow = $refResult->fetch_assoc()) {
                        $link = trim((string)($refRow['Lenke'] ?? ''));
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
                            'year' => $refRow['Year'],
                            'number' => $refRow['BladNr'],
                            'side' => $refRow['Side'],
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
}

include('../../includes/header.php');
?>
<main class="page-main">
  <div class="container">
    <div class="card">
      <div class="card-content">
        <h1 class="page-title"><?php echo h($page_title); ?></h1>

        <?php if ($error !== null): ?>
          <p><?php echo h($error); ?></p>
        <?php elseif ($annonsor === null): ?>
          <p>Ingen detaljer tilgjengelige.</p>
        <?php else: ?>
          <h2><?php echo h($annonsor['display_name']); ?></h2>

          <div class="selection-summary">
            <div>
              <strong>Kontakt</strong>
              <?php echo h($annonsor['kontaktpers'] !== '' ? $annonsor['kontaktpers'] : '-'); ?>
            </div>
            <div>
              <strong>Telefon</strong>
              <?php echo h($annonsor['kontakt_telf'] !== '' ? $annonsor['kontakt_telf'] : '-'); ?>
            </div>
            <div>
              <strong>Web</strong>
              <?php if ($annonsor['web'] !== ''): ?>
                <a href="<?php echo h($annonsor['web']); ?>" target="_blank" rel="noopener">Nettside</a>
              <?php else: ?>
                -
              <?php endif; ?>
            </div>
          </div>

          <dl class="detail-list">
            <dt>Navn</dt><dd><?php echo h($annonsor['name'] !== '' ? $annonsor['name'] : '-'); ?></dd>
            <dt>Avdeling</dt><dd><?php echo h($annonsor['department'] !== '' ? $annonsor['department'] : '-'); ?></dd>
            <dt>Adresse</dt><dd><?php echo h($annonsor['adresse'] !== '' ? $annonsor['adresse'] : '-'); ?></dd>
            <dt>Post</dt><dd><?php echo h($annonsor['post'] !== '' ? $annonsor['post'] : '-'); ?></dd>
            <dt>Land</dt><dd><?php echo h($annonsor['land'] !== '' ? $annonsor['land'] : '-'); ?><?php echo $annonsor['utland'] ? ' (utland)' : ''; ?></dd>
            <dt>E-post</dt>
            <dd>
              <?php if ($annonsor['epost'] !== ''): ?>
                <a href="mailto:<?php echo h($annonsor['epost']); ?>"><?php echo h($annonsor['epost']); ?></a>
              <?php else: ?>
                -
              <?php endif; ?>
            </dd>
            <dt>Kontakt e-post</dt>
            <dd>
              <?php if ($annonsor['kontakt_epost'] !== ''): ?>
                <a href="mailto:<?php echo h($annonsor['kontakt_epost']); ?>"><?php echo h($annonsor['kontakt_epost']); ?></a>
              <?php else: ?>
                -
              <?php endif; ?>
            </dd>
          </dl>

          <?php if ($annonsor['notater'] !== ''): ?>
            <h3>Notater</h3>
            <p><?php echo nl2br(h($annonsor['notater'])); ?></p>
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
    </div>
  </div>
</main>

<?php include('../../includes/footer.php'); ?>
