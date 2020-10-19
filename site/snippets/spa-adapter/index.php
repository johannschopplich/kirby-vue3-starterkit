<?php

use KirbyExtended\SpaAdapter;

?>
<!DOCTYPE html>
<html lang="en">
<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php snippet('meta', compact('page', 'site')) ?>

  <?= SpaAdapter::jsonPreloadLink($page->id()) ?>
  <?= SpaAdapter::modulePreloadLink($page->intendedTemplate()->name()) ?>
  <link rel="stylesheet" href="<?= SpaAdapter::pathToAsset('style.*.css') ?>">

</head>
<body>

  <div id="app" data-site="<?= htmlspecialchars(SpaAdapter::useSite(), ENT_QUOTES) ?>"></div>
  <script type="module" src="<?= SpaAdapter::pathToAsset('index.*.js') ?>"></script>

</body>
</html>
