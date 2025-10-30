<?php
// footer.php - LASTES ETTER alt innhold
$BASE = defined('BASE_URL') ? rtrim(BASE_URL, '/') : '';
?>
<footer class="site-footer">
  <div class="container">
    <div class="muted">c <?= date('Y') ?> DpostWeb</div>
  </div>
</footer>

<script src="<?= h($BASE . '/js/table-filters.js') ?>"></script>

</body>
</html>
