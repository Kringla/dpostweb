<?php
// footer.php - LASTES ETTER alt innhold
$BASE = defined('BASE_URL') ? rtrim(BASE_URL, '/') : '';
?>
<footer class="site-footer">
  <div class="container">
    <div class="site-footer__copyright"><?= h('@Copyright 2025  Gerhard B Ihlen  (gbihlen@gmail.com)') ?></div>
  </div>
</footer>

<script src="<?= h($BASE . '/js/table-filters.js') ?>"></script>

</body>
</html>
