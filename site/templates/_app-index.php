<?php
/** @var \Kirby\Cms\App $kirby */
/** @var \Kirby\Cms\Page $page */
?>
<!DOCTYPE html>
<html lang="<?= $kirby->languageCode() ?? 'en' ?>">
<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title><?= $page->customTitle()->or($page->title() . ' – ' . $site->title()) ?></title>

  <?php /* See https://github.com/johannschopplich/kirby-extended/blob/main/docs/meta.md */ ?>
  <?php $meta = $page->meta() ?>
  <?= $meta->robots() ?>
  <?= $meta->jsonld() ?>
  <?= $meta->social() ?>

  <meta name="theme-color" content="#41b883">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="default">
  <meta name="apple-mobile-web-app-title" content="<?= $site->title()->escape() ?>">

  <link rel="manifest" href="/manifest.json">
  <link rel="icon" href="/img/favicon.svg" type="image/svg+xml">
  <link rel="apple-touch-icon" href="/img/apple-touch-icon.png" sizes="180x180">

  <?= vite()->preloadJson($page->uri()) ?>
  <?= vite()->preloadModule($page->intendedTemplate()->name()) ?>

  <?= vite()->js() ?>
  <?= vite()->css() ?>

</head>
<body>

  <div id="app"></div>
  <script id="site-data" type="application/json"><?= vite()->json(vite()->useSite()) ?></script>

</body>
</html>
