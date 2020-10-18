<?php

$assetsDir = \Kirby\Cms\Url::path(env('VITE_ASSETS_DIR'), true, true);
$apiLocation = \Kirby\Cms\Url::path(env('CONTENT_API_SLUG'), true);
$siteData = require __DIR__ . '/site.php';

/**
 * Returns the filename for a build asset, e.g. `style.d4814c7a.css`
 */
$assetPath = function ($pattern) use ($assetsDir) {
  $filename = glob(kirby()->roots()->index() . $assetsDir . $pattern)[0] ?? null;
  if ($filename === null) throw new Exception('No production assets found. You have to bundle the app first. Run `npm run build`.');
  return $assetsDir . basename($filename);
};

/**
 * Preloads the JSON-encoded page data for a given page
 */
$dataPreloadLink = function ($name) use ($apiLocation) {
  return '<link rel="preload" href="' . $apiLocation . '/' . $name . '.json" as="fetch" crossorigin>';
};

/**
 * Preloads the view module for a given page, e.g. `Home.e701bdef.js`
 */
$modulePreloadLink = function ($name) use ($assetsDir) {
  $filename = glob(kirby()->roots()->index() . $assetsDir . ucfirst($name) . '.*.js')[0] ?? null;
  if ($filename === null) return;
  return '<link rel="modulepreload" href="' . $assetsDir . basename($filename) . '">';
};

?>
<!DOCTYPE html>
<html lang="en">
<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php snippet('meta', compact('page', 'site')) ?>

  <?= $dataPreloadLink($page->id()) ?>
  <?= $modulePreloadLink($page->intendedTemplate()->name()) ?>
  <link rel="stylesheet" href="<?= $assetPath('style.*.css') ?>">

</head>
<body>

  <div id="app" data-site="<?= htmlspecialchars($siteData, ENT_QUOTES) ?>"></div>
  <script type="module" src="<?= $assetPath('index.*.js') ?>"></script>

</body>
</html>
