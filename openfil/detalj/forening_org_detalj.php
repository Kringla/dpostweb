<?php
require_once('../../includes/bootstrap.php');

if (!isset($_GET['id']) || !is_valid_id($_GET['id'])) {
    http_response_code(400);
    $page_title = "Ugyldig forespørsel";
    include('../../includes/header.php');
    ?>
    <main class="page-main">
      <div class="container">
        <div class="card">
          <div class="card-content">
            <h1 class="page-title"><?= h($page_title) ?></h1>
            <p>Forespørselen mangler et gyldig organisasjons-id.</p>
            <a class="btn" href="<?= h(url('openfil/forening_org.php')) ?>">Tilbake</a>
          </div>
        </div>
      </div>
    </main>
    <?php
    include('../../includes/footer.php');
    return;
}

$orgId = (int) $_GET['id'];
$db = db();

$sql = "
  SELECT
    o.OrgID,
    COALESCE(NULLIF(TRIM(o.OrgNavn), ''), CONCAT('Organisasjon ', o.OrgID)) AS name,
    NULLIF(TRIM(o.Adresse), '') AS address,
    NULLIF(TRIM(o.Postnr), '') AS postal_code,
    NULLIF(TRIM(o.Poststed), '') AS city,
    NULLIF(TRIM(o.EpostAdresse), '') AS email,
    NULLIF(TRIM(o.WebSide), '') AS website,
    NULLIF(TRIM(o.FB), '') AS facebook,
    NULLIF(TRIM(o.Notater), '') AS notes,
    COALESCE(o.AntDP, 0) AS mention_total,
    COALESCE(o.AntArt, 0) AS article_count,
    COALESCE(o.AntNot, 0) AS notice_count,
    COALESCE(o.AntSpalte, 0) AS column_count,
    COALESCE(o.AntBilde, 0) AS image_count,
    COALESCE(o.AntHoved, 0) AS main_count,
    COALESCE(o.AntOmtalt, 0) AS cited_count,
    o.StatOrg,
    o.IkkeOrg,
    o.Nedlagt
  FROM vwOrgDposten o
  WHERE o.OrgID = ?
  LIMIT 1
";

$stmt = $db->prepare($sql);
$stmt->bind_param('i', $orgId);
$stmt->execute();
$result = $stmt->get_result();
$detail = $result->fetch_assoc();
$stmt->close();

if (!$detail) {
    http_response_code(404);
    $page_title = 'Organisasjon ikke funnet';
    include('../../includes/header.php');
    ?>
    <main class="page-main">
      <div class="container">
        <div class="card">
          <div class="card-content">
            <h1 class="page-title"><?= h($page_title) ?></h1>
            <p>Vi fant ikke organisasjon #<?= h($orgId) ?>.</p>
            <a class="btn" href="<?= h(url('openfil/forening_org.php')) ?>">Tilbake</a>
          </div>
        </div>
      </div>
    </main>
    <?php
    include('../../includes/footer.php');
    return;
}

$locationParts = array_filter([
    $detail['postal_code'] ?? null,
    $detail['city'] ?? null,
]);
$location = trim(implode(' ', $locationParts));

$page_title = 'Organisasjon: ' . $detail['name'];
include('../../includes/header.php');
?>
<main class="page-main">
  <div class="container">
    <div class="card">
      <div class="card-content">
        <h1 class="page-title"><?= h($page_title) ?></h1>

        <dl class="detail-list">
          <dt>Navn</dt><dd><?= h($detail['name']) ?></dd>
          <dt>Adresse</dt><dd><?= h($detail['address'] ?? '') ?></dd>
          <dt>Lokasjon</dt><dd><?= h($location) ?></dd>
          <dt>E-post</dt><dd><?= h($detail['email'] ?? '') ?></dd>
          <dt>Nettside</dt><dd><?php if (!empty($detail['website'])): ?><a href="<?= h($detail['website']) ?>" target="_blank" rel="noopener"><?= h($detail['website']) ?></a><?php endif; ?></dd>
          <dt>Facebook</dt><dd><?php if (!empty($detail['facebook'])): ?><a href="<?= h($detail['facebook']) ?>" target="_blank" rel="noopener"><?= h($detail['facebook']) ?></a><?php endif; ?></dd>
          <dt>Antall omtaler</dt><dd><?= h($detail['mention_total']) ?></dd>
          <dt>Artikler</dt><dd><?= h($detail['article_count']) ?></dd>
          <dt>Notiser</dt><dd><?= h($detail['notice_count']) ?></dd>
          <dt>Spalter</dt><dd><?= h($detail['column_count']) ?></dd>
          <dt>Bilder</dt><dd><?= h($detail['image_count']) ?></dd>
          <dt>Hovedsaker</dt><dd><?= h($detail['main_count']) ?></dd>
          <dt>Sitert</dt><dd><?= h($detail['cited_count']) ?></dd>
          <dt>Status</dt><dd><?= $detail['Nedlagt'] ? 'Nedlagt' : 'Aktiv / ukjent' ?></dd>
          <dt>Notater</dt><dd><?= h($detail['notes'] ?? '') ?></dd>
        </dl>

        <a href="<?= h(url('openfil/forening_org.php')) ?>" class="btn">Tilbake</a>
      </div>
    </div>
  </div>
</main>
<?php include('../../includes/footer.php'); ?>


