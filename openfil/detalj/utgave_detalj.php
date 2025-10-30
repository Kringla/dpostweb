<?php
require_once('../../includes/bootstrap.php');
$page_title = 'Detaljer - Utgave';
$activeNav = 'issues';

$db = db();
$issue = null;
$error = null;

$issueId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

if ($issueId === null || $issueId === false) {
    $error = 'Ugyldig eller manglende utgave-ID.';
} else {
    $stmt = $db->prepare('SELECT Year, BladNr, AntArt, AntBilde FROM tblBlad WHERE BladID = ? LIMIT 1');
    if ($stmt instanceof mysqli_stmt) {
        $stmt->bind_param('i', $issueId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result instanceof mysqli_result) {
            $row = $result->fetch_assoc();
            if ($row) {
                $issue = [
                    'year'      => $row['Year'] !== null ? (int)$row['Year'] : null,
                    'number'    => $row['BladNr'] !== null ? (int)$row['BladNr'] : null,
                    'ant_art'   => $row['AntArt'] !== null ? (int)$row['AntArt'] : null,
                    'ant_bilde' => $row['AntBilde'] !== null ? (int)$row['AntBilde'] : null,
                ];
            }
            $result->free();
        }
        $stmt->close();
    }

    if ($issue === null && $error === null) {
        $error = 'Fant ingen utgave med oppgitt ID.';
    }
}

$detailLabel = 'Utgavedetaljer';
if ($issue !== null) {
    $parts = [];
    if ($issue['year'] !== null) {
        $parts[] = (string)$issue['year'];
    }
    if ($issue['number'] !== null) {
        $parts[] = 'Nr ' . $issue['number'];
    }
    if (!empty($parts)) {
        $detailLabel = 'Utgave ' . implode(' ', $parts);
    }
}

$breadcrumbs = [
    ['label' => 'Hjem', 'url' => url('index.php')],
    ['label' => 'Utgaver', 'url' => url('openfil/utgaver.php')],
    ['label' => $detailLabel],
];

include('../../includes/header.php');
?>
<main class="page-main">
  <div class="container">
    <div class="card">
      <div class="card-content">
        <h1><?php echo h($page_title); ?></h1>
        <?php if ($error !== null): ?>
          <p><?php echo h($error); ?></p>
        <?php else: ?>
          <?php
            $detailRows = [
              ['label' => '&Aring;r', 'value' => $issue['year'] ?? null],
              ['label' => 'Nummer', 'value' => $issue['number'] ?? null],
              ['label' => 'Antall artikler', 'value' => $issue['ant_art'] ?? null],
              ['label' => 'Antall bilder', 'value' => $issue['ant_bilde'] ?? null],
            ];
          ?>
          <dl class="detail-list">
            <?php foreach ($detailRows as $item): ?>
              <dt><?php echo $item['label']; ?></dt>
              <dd><?php
                $value = $item['value'];
                echo ($value === null || $value === '') ? '&ndash;' : h($value);
              ?></dd>
            <?php endforeach; ?>
          </dl>
        <?php endif; ?>

        <a href="<?= h(url('openfil/utgaver.php')) ?>" class="btn">Tilbake</a>
      </div>
    </div>
  </div>
</main>
<?php include('../../includes/footer.php'); ?>


