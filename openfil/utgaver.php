<?php
require_once('../includes/bootstrap.php');

$page_title = 'Utgaver';
$activeNav = 'issues';
$db = db();
$db->query('SET SESSION group_concat_max_len = 2048');

$issues = [];
$pdfBaseUrl = 'https://ihlen.net/installs/DPfilerPDF/';
$issueSql = "
  SELECT
    BladID,
    BladNr,
    Year,
    YearNr,
    BladFormat,
    Opplag,
    Lenke,
    AntArt,
    AntBilde,
    AntNot,
    AntSpalte,
    AntProg,
    AntSider,
    Farger,
    Redaktor,
    Trykkeri
  FROM tblBlad
  WHERE BladID IS NOT NULL
  ORDER BY BladNr DESC, Year DESC, BladID DESC
";

$issueResult = $db->query($issueSql);

if ($issueResult instanceof mysqli_result) {
    while ($row = $issueResult->fetch_assoc()) {
        $issues[] = [
            'id' => (int)$row['BladID'],
            'blad_nr' => $row['BladNr'] !== null ? (int)$row['BladNr'] : null,
            'year' => $row['Year'] !== null ? (int)$row['Year'] : null,
            'year_nr' => $row['YearNr'] !== null ? (int)$row['YearNr'] : null,
            'format' => trim((string)($row['BladFormat'] ?? '')),
            'opplag' => $row['Opplag'] !== null ? (int)$row['Opplag'] : null,
            'lenke' => trim((string)($row['Lenke'] ?? '')),
            'ant_art' => $row['AntArt'] !== null ? (int)$row['AntArt'] : null,
            'ant_bilde' => $row['AntBilde'] !== null ? (int)$row['AntBilde'] : null,
            'ant_not' => $row['AntNot'] !== null ? (int)$row['AntNot'] : null,
            'ant_spalte' => $row['AntSpalte'] !== null ? (int)$row['AntSpalte'] : null,
            'ant_prog' => $row['AntProg'] !== null ? (int)$row['AntProg'] : null,
            'ant_sider' => $row['AntSider'] !== null ? (int)$row['AntSider'] : null,
            'farger' => $row['Farger'] !== null ? (int)$row['Farger'] : null,
            'redaktor' => trim((string)($row['Redaktor'] ?? '')),
            'trykkeri' => trim((string)($row['Trykkeri'] ?? '')),
        ];
    }
    $issueResult->free();
}

$selectedIssueId = filter_input(INPUT_GET, 'blad_id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$selectedIssue = null;

if (!empty($issues)) {
    if ($selectedIssueId === null) {
        $selectedIssue = $issues[0];
        $selectedIssueId = $selectedIssue['id'];
    } else {
        foreach ($issues as $issue) {
            if ($issue['id'] === $selectedIssueId) {
                $selectedIssue = $issue;
                break;
            }
        }
        if ($selectedIssue === null) {
            $selectedIssue = $issues[0];
            $selectedIssueId = $selectedIssue['id'];
        }
    }
}

$articles = [];

if ($selectedIssueId !== null) {
    $articleSql = "
      SELECT
        a.ArtID AS article_id,
        a.ArtTittel,
        a.Side,
        GROUP_CONCAT(
          DISTINCT NULLIF(
            TRIM(
              COALESCE(
                NULLIF(TRIM(f.ForfNavn), ''),
                CONCAT_WS(' ',
                  NULLIF(TRIM(f.FNavn), ''),
                  NULLIF(TRIM(f.ENavn), '')
                )
              )
            ),
            ''
          )
          ORDER BY f.ENavn, f.FNavn
          SEPARATOR ', '
        ) AS authors
      FROM tblArtikkel a
      LEFT JOIN tblxArtForf af ON af.ArtID = a.ArtID
      LEFT JOIN tblForfatter f ON f.ForfID = af.ForfID
      WHERE a.BladID = ?
      GROUP BY a.ArtID, a.ArtTittel, a.Side
      ORDER BY a.Side ASC, a.ArtTittel ASC
    ";

    $articleStmt = $db->prepare($articleSql);
    if ($articleStmt instanceof mysqli_stmt) {
        $articleStmt->bind_param('i', $selectedIssueId);
        $articleStmt->execute();
        $articleResult = $articleStmt->get_result();

        if ($articleResult instanceof mysqli_result) {
            while ($row = $articleResult->fetch_assoc()) {
                $title = trim((string)($row['ArtTittel'] ?? ''));
                if ($title === '') {
                    $title = 'Artikkel ' . (int)$row['article_id'];
                }

                $authors = trim((string)($row['authors'] ?? ''));
                if ($authors === '') {
                    $authors = 'Ukjent forfatter';
                }

                $articles[] = [
                    'id' => (int)$row['article_id'],
                    'title' => $title,
                    'side' => $row['Side'] !== null ? (int)$row['Side'] : null,
                    'authors' => $authors,
                ];
            }
            $articleResult->free();
        }

        $articleStmt->close();
    }
}

$breadcrumbs = [
    ['label' => 'Hjem', 'url' => url('index.php')],
    ['label' => 'Utgaver'],
];

include('../includes/header.php');
?>
<main class="page-main">
  <div class="container">
    <div class="card">
      <div class="card-content">
        <h1 class="page-title"><?php echo h($page_title); ?></h1>

        <div class="utgaver-layout">
          <section class="utgaver-section utgaver-section--list">
            <h2>Tabell 1: Utgaver</h2>
            <div class="table-wrap table-wrap--static">
              <div class="table-scroll">
                <table class="data-table utgaver-table">
                  <thead>
                    <tr>
                      <th>BladNr</th><th>&Aring;r</th><th>Bladformat</th><th>Opplag</th><th>Lenke</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php if (empty($issues)): ?>
                    <tr data-empty-row="true"><td colspan="5">Ingen utgaver registrert.</td></tr>
                  <?php else: ?>
                    <?php foreach ($issues as $issue): ?>
                      <?php
                        $isSelected = ($selectedIssue !== null && $issue['id'] === $selectedIssue['id']);
                        $rowClass = $isSelected ? ' class="is-selected"' : '';
                        $issueUrl = url('openfil/utgaver.php?blad_id=' . $issue['id']);
                        $lenke = $issue['lenke'];
                        $lenkeHref = '';
                        if ($lenke !== '') {
                            if (preg_match('~^https?://~i', $lenke)) {
                                $lenkeHref = $lenke;
                            } else {
                                $lenkeHref = rtrim($pdfBaseUrl, '/') . '/' . ltrim($lenke, '/');
                            }
                        }
                      ?>
                      <tr<?php echo $rowClass; ?>>
                        <td>
                          <a href="<?php echo h($issueUrl); ?>">
                            <?php echo h($issue['blad_nr'] ?? ''); ?>
                          </a>
                        </td>
                        <td><?php echo h($issue['year'] ?? ''); ?></td>
                        <td><?php echo h($issue['format']); ?></td>
                        <td><?php echo h($issue['opplag'] ?? ''); ?></td>
                        <td>
                          <?php if ($lenkeHref !== ''): ?>
                            <a href="<?php echo h($lenkeHref); ?>" target="_blank" rel="noopener">
                              <?php echo h($issue['lenke']); ?>
                            </a>
                          <?php else: ?>
                            &ndash;
                          <?php endif; ?>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </section>

          <section class="utgaver-section utgaver-section--detail">
            <h2>Detaljer</h2>
            <?php if ($selectedIssue === null): ?>
              <p>Velg en utgave fra tabellen for &aring; se detaljer.</p>
            <?php else: ?>
              <?php
                $detailItems = [];
                if ($selectedIssue['year'] !== null) {
                    $detailItems[] = ['label' => '&Aring;r', 'value' => (string)$selectedIssue['year']];
                }
                if ($selectedIssue['blad_nr'] !== null) {
                    $detailItems[] = ['label' => 'Nummer', 'value' => (string)$selectedIssue['blad_nr']];
                }
                if ($selectedIssue['year_nr'] !== null) {
                    $detailItems[] = ['label' => '&Aring;rgang', 'value' => (string)$selectedIssue['year_nr']];
                }
                if ($selectedIssue['format'] !== '') {
                    $detailItems[] = ['label' => 'Bladformat', 'value' => $selectedIssue['format']];
                }
                if ($selectedIssue['ant_sider'] !== null) {
                    $detailItems[] = ['label' => 'Antall sider', 'value' => (string)$selectedIssue['ant_sider']];
                }
                if ($selectedIssue['ant_art'] !== null) {
                    $detailItems[] = ['label' => 'Antall artikler', 'value' => (string)$selectedIssue['ant_art']];
                }
                if ($selectedIssue['ant_bilde'] !== null) {
                    $detailItems[] = ['label' => 'Antall bilder', 'value' => (string)$selectedIssue['ant_bilde']];
                }
                if ($selectedIssue['ant_not'] !== null) {
                    $detailItems[] = ['label' => 'Antall notiser', 'value' => (string)$selectedIssue['ant_not']];
                }
                if ($selectedIssue['ant_spalte'] !== null) {
                    $detailItems[] = ['label' => 'Antall spalter', 'value' => (string)$selectedIssue['ant_spalte']];
                }
                if ($selectedIssue['ant_prog'] !== null) {
                    $detailItems[] = ['label' => 'Antall program', 'value' => (string)$selectedIssue['ant_prog']];
                }
                if ($selectedIssue['opplag'] !== null) {
                    $detailItems[] = ['label' => 'Opplag', 'value' => (string)$selectedIssue['opplag']];
                }
                if ($selectedIssue['farger'] !== null) {
                    $detailItems[] = ['label' => 'Farger', 'value' => $selectedIssue['farger'] === 0 ? 'Nei' : 'Ja'];
                }
                if ($selectedIssue['trykkeri'] !== '') {
                    $detailItems[] = ['label' => 'Trykkeri', 'value' => $selectedIssue['trykkeri']];
                }
                if ($selectedIssue['redaktor'] !== '') {
                    $detailItems[] = ['label' => 'Redakt&oslash;r', 'value' => $selectedIssue['redaktor']];
                }
              ?>

              <?php if (!empty($detailItems)): ?>
              <dl class="detail-list">
                <?php foreach ($detailItems as $item): ?>
                  <dt><?php echo $item['label']; ?></dt>
                  <dd><?php echo h($item['value']); ?></dd>
                <?php endforeach; ?>
              </dl>
              <?php else: ?>
              <p>Ingen detaljinformasjon registrert for denne utgaven.</p>
              <?php endif; ?>

              <h3>Tabell 2: Artikler med forfatter</h3>
              <div class="table-wrap table-wrap--static">
                <div class="table-scroll">
                  <table class="data-table data-table--compact">
                    <thead>
                      <tr>
                        <th>Side</th><th>Tittel</th><th>Forfatter(e)</th>
                      </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($articles)): ?>
                      <tr data-empty-row="true"><td colspan="3">Ingen artikler registrert for denne utgaven.</td></tr>
                    <?php else: ?>
                      <?php foreach ($articles as $article): ?>
                        <tr>
                          <td><?php echo h($article['side'] !== null ? (string)$article['side'] : ''); ?></td>
                          <td><?php echo h($article['title']); ?></td>
                          <td><?php echo h($article['authors']); ?></td>
                        </tr>
                      <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                  </table>
                </div>
              </div>

              <div class="utgaver-actions">
                <a class="btn" href="<?php echo h(url('index.php')); ?>">Tilbake</a>
              </div>
            <?php endif; ?>
          </section>
        </div>

      </div>
    </div>
  </div>
</main>

<?php include('../includes/footer.php'); ?>
