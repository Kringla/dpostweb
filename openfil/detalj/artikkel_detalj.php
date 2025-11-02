<?php
require_once('../../includes/bootstrap.php');

$page_title = 'Detaljer - Artikkel';
$activeNav = 'articles';
$article = null;
$error = null;
$articleId = null;
$detailItems = [];
$title = '';

if (!isset($_GET['id']) || !is_valid_id($_GET['id'])) {
    http_response_code(400);
    $error = [
        'title' => 'Ugyldig foresporsel',
        'message' => 'Foresporselen mangler et gyldig artikkel-id.',
    ];
    $page_title = $error['title'];
} else {
    $articleId = (int) $_GET['id'];
    $db = db();

    $detailSql = <<<'SQL'
      SELECT
        a.ArtID,
        a.ArtTittel,
        a.ArtUnderTittel,
        a.ArtType,
        a.Sprog,
        a.Abstrakt,
        a.Kapitler,
        a.EksternRef,
        a.ArtAntBilde,
        a.SpID,
        a.Merknad,
        a.Side,
        b.BladNr,
        b.Year,
        b.YearNr,
        b.Lenke,
        s.SpTittel
      FROM tblArtikkel a
      LEFT JOIN tblBlad b ON b.BladID = a.BladID
      LEFT JOIN tblzSpalte s ON s.SpID = a.SpID
      WHERE a.ArtID = ?
      LIMIT 1
    SQL;

    $stmt = $db->prepare($detailSql);
    $stmt->bind_param('i', $articleId);
    $stmt->execute();
    $article = $stmt->get_result()->fetch_assoc() ?: null;
    $stmt->close();

    if (!$article) {
        http_response_code(404);
        $error = [
            'title' => 'Artikkel ikke funnet',
            'message' => 'Vi fant ikke artikkel #' . $articleId . '.',
        ];
        $page_title = $error['title'];
    } else {
        $authorSql = <<<'SQL'
          SELECT DISTINCT
            COALESCE(
              NULLIF(TRIM(f.ForfNavn), ''),
              NULLIF(TRIM(CONCAT_WS(' ', f.FNavn, f.ENavn)), '')
            ) AS author_name
          FROM tblxArtForf af
          LEFT JOIN tblForfatter f ON f.ForfID = af.ForfID
          WHERE af.ArtID = ?
          ORDER BY f.ENavn, f.FNavn
        SQL;

        $authorStmt = $db->prepare($authorSql);
        $authorStmt->bind_param('i', $articleId);
        $authorStmt->execute();
        $authorResult = $authorStmt->get_result();
        $authorList = [];
        if ($authorResult instanceof mysqli_result) {
            while ($row = $authorResult->fetch_assoc()) {
                $name = trim((string)($row['author_name'] ?? ''));
                if ($name !== '' && !in_array($name, $authorList, true)) {
                    $authorList[] = $name;
                }
            }
            $authorResult->free();
        }
        $authorStmt->close();

        $title = trim((string)($article['ArtTittel'] ?? ''));
        if ($title === '') {
            $title = 'Artikkel ' . $articleId;
        }

        $subtitle = trim((string)($article['ArtUnderTittel'] ?? ''));
        if (empty($authorList)) {
            $authorList[] = 'Ukjent forfatter';
        }

        $artType = trim((string)($article['ArtType'] ?? ''));
        $language = trim((string)($article['Sprog'] ?? ''));
        $abstract = trim((string)($article['Abstrakt'] ?? ''));
        $chapters = trim((string)($article['Kapitler'] ?? ''));
        $externalRef = trim((string)($article['EksternRef'] ?? ''));
        $imageCount = $article['ArtAntBilde'];
        $spId = $article['SpID'];
        $notes = trim((string)($article['Merknad'] ?? ''));
        $pageNumber = $article['Side'];
        $issueNumber = $article['BladNr'];
        $publishedYear = $article['Year'];
        $issueYearNr = $article['YearNr'];
        $issueLink = trim((string)($article['Lenke'] ?? ''));
        $spalteTitle = trim((string)($article['SpTittel'] ?? ''));
        $typeNormalized = strtoupper(trim(rtrim($artType, '.')));
        $isSpalteType = $typeNormalized === 'S';

        $externalRefHref = '';
        if ($externalRef !== '' && filter_var($externalRef, FILTER_VALIDATE_URL)) {
            $externalRefHref = $externalRef;
        }

        $externalArchiveBase = 'https://ihlen.net/installs/DPfilerPDF';
        $issueLinkHref = '';
        if ($issueLink !== '') {
            if (filter_var($issueLink, FILTER_VALIDATE_URL)) {
                $issueLinkHref = $issueLink;
            } else {
                $issueLinkHref = rtrim($externalArchiveBase, '/') . '/' . ltrim($issueLink, '/');
            }
        }

        $page_title = 'Artikkel: ' . $title;

        $detailItems = [
            ['label' => 'Artikkel-ID', 'text' => (string)$articleId, 'allow_zero' => true],
            ['label' => 'Tittel', 'text' => $title],
        ];

        if ($subtitle !== '') {
            $detailItems[] = ['label' => 'Undertittel', 'text' => $subtitle];
        }

        if ($abstract !== '') {
            $detailItems[] = ['label' => 'Abstrakt', 'text' => $abstract, 'multiline' => true];
        }

        if ($chapters !== '') {
            $detailItems[] = ['label' => 'Kapitler', 'text' => $chapters, 'multiline' => true];
        }

        $detailItems[] = ['label' => 'Forfatter(e)', 'text' => implode("\n", $authorList), 'multiline' => true];

        if ($publishedYear !== null) {
            $detailItems[] = ['label' => 'År', 'text' => (string)$publishedYear, 'allow_zero' => true];
        }

        if ($issueNumber !== null) {
            $detailItems[] = ['label' => 'Utgave', 'text' => (string)$issueNumber, 'allow_zero' => true];
        }

        if ($issueYearNr !== null) {
            $detailItems[] = ['label' => 'Årgang', 'text' => (string)$issueYearNr, 'allow_zero' => true];
        }

        if ($pageNumber !== null) {
            $detailItems[] = ['label' => 'Side', 'text' => (string)$pageNumber, 'allow_zero' => true];
        }

        if ($artType !== '') {
            $detailItems[] = ['label' => 'Type', 'text' => $artType];
        }

        if ($isSpalteType && $spId !== null) {
            $spalteText = $spalteTitle !== '' ? $spalteTitle : 'Spalte ' . (string)$spId;
            $detailItems[] = ['label' => 'Fast spalte type', 'text' => $spalteText, 'allow_zero' => true];
        }

        if ($language !== '') {
            $detailItems[] = ['label' => 'Språk', 'text' => $language];
        }

        if ($imageCount !== null) {
            $detailItems[] = ['label' => 'Antall bilder', 'text' => (string)$imageCount, 'allow_zero' => true];
        }


        if ($externalRef !== '') {
            $detailItems[] = [
                'label' => 'Ekstern referanse',
                'text' => $externalRef,
                'href' => $externalRefHref,
            ];
        }

        if ($issueLinkHref !== '') {
            $detailItems[] = [
                'label' => 'Utgave PDF',
                'text' => $issueLink !== '' ? $issueLink : 'Apne utgave',
                'href' => $issueLinkHref,
            ];
        }


        if ($notes !== '') {
            $detailItems[] = ['label' => 'Merknader', 'text' => $notes, 'multiline' => true];
        }

        $dbReferences = [];
        $tableCheck = $db->query("SHOW TABLES LIKE 'tblxDbRef'");
        if ($tableCheck instanceof mysqli_result) {
            $hasDbRefTable = $tableCheck->num_rows > 0;
            $tableCheck->free();
        } else {
            $hasDbRefTable = false;
        }

        if ($hasDbRefTable) {
            $dbRefSql = "SELECT * FROM tblxDbRef WHERE ArtID = ?";
            $dbRefStmt = $db->prepare($dbRefSql);
            if ($dbRefStmt instanceof mysqli_stmt) {
                $dbRefStmt->bind_param('i', $articleId);
                $dbRefStmt->execute();
                $dbRefResult = $dbRefStmt->get_result();
                if ($dbRefResult instanceof mysqli_result) {
                    while ($refRow = $dbRefResult->fetch_assoc()) {
                        $parts = [];
                        foreach ($refRow as $column => $value) {
                            if ($value === null) {
                                continue;
                            }
                            if (strcasecmp($column, 'id') === 0 || strcasecmp($column, 'artid') === 0) {
                                continue;
                            }
                            $textValue = trim((string)$value);
                            if ($textValue === '') {
                                continue;
                            }
                            $parts[] = $textValue;
                        }
                        if (!empty($parts)) {
                            $dbReferences[] = implode(' - ', $parts);
                        }
                    }
                    $dbRefResult->free();
                }
                $dbRefStmt->close();
            }
        }

        if (!empty($dbReferences)) {
            $detailItems[] = ['label' => 'DB-referanser', 'text' => implode("\n", $dbReferences), 'multiline' => true];
        }
    }
}

$detailLabel = 'Artikkel';
if ($article) {
    if ($title !== '') {
        $detailLabel = $title;
    } elseif ($articleId !== null) {
        $detailLabel = 'Artikkel ' . $articleId;
    }
} elseif ($articleId !== null) {
    $detailLabel = 'Artikkel ' . $articleId;
}

$breadcrumbs = [
    ['label' => 'Hjem', 'url' => url('index.php')],
    ['label' => 'Artikler', 'url' => url('openfil/artikler.php')],
    ['label' => $detailLabel],
];

include('../../includes/header.php');
?>
<main class="page-main">
  <div class="container">
    <div class="card">
      <div class="card-content">
        <h1><?php echo h($page_title); ?></h1>

        <?php if ($error): ?>
          <p><?php echo h($error['message']); ?></p>
          <a href="<?php echo h(url('openfil/artikler.php')); ?>" class="btn">Tilbake</a>
        <?php else: ?>
          <dl class="detail-list">
            <?php foreach ($detailItems as $item): ?>
              <?php
                $text = $item['text'] ?? '';
                $allowZero = $item['allow_zero'] ?? false;
                if ($text === '' && !$allowZero) {
                    continue;
                }
              ?>
              <dt><?php echo $item['label']; ?></dt>
              <dd>
                <?php if (!empty($item['href'])): ?>
                  <a href="<?php echo h($item['href']); ?>" target="_blank" rel="noopener noreferrer">
                    <?php echo h($text); ?>
                  </a>
                <?php elseif (!empty($item['multiline'])): ?>
                  <?php echo nl2br(h($text)); ?>
                <?php else: ?>
                  <?php echo h($text); ?>
                <?php endif; ?>
              </dd>
            <?php endforeach; ?>
          </dl>

          <a href="<?php echo h(url('openfil/artikler.php')); ?>" class="btn">Tilbake</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</main>
<?php include('../../includes/footer.php'); ?>
