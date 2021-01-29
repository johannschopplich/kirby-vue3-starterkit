<?php

use Kirby\Data\Json;
use KirbyExtended\SpaAdapter;

?>
<!DOCTYPE html>
<html lang="<?= kirby()->languageCode() ?? 'en' ?>">
<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php snippet('meta', compact('page', 'site')) ?>

  <?= SpaAdapter::jsonPreloadLink($page->uri()) ?>
  <?= SpaAdapter::modulePreloadLink($page->intendedTemplate()->name()) ?>
  <?= SpaAdapter::css() ?>

</head>
<body>

  <div id="app"></div>
  <script id="site-data" type="application/json"><?= Json::encode(SpaAdapter::useSite()) ?></script>
  <?= SpaAdapter::js() ?>

</body>
</html>
