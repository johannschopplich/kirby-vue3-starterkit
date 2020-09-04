<?php

use Kirby\Cms\Url;

$assetsDir = '/assets/';
$apiLocation = Url::path(env('KIRBY_API_LOCATION', ''), true);

$pathToAsset = function (string $pattern) use ($assetsDir) {
  $filename = glob(kirby()->roots()->index() . $assetsDir . $pattern)[0] ?? null;
  if ($filename === null) throw new Exception('No production assets found. You have to bundle the app first. Run `npm run build`.');
  return $assetsDir . basename($filename);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php snippet('meta', compact('page', 'site')) ?>

  <link rel="preload" href="<?= $apiLocation . '/home.json' ?>" as="fetch" crossorigin>
  <link rel="stylesheet" href="<?= $pathToAsset('style.*.css') ?>">

</head>
<body>

  <div id="app"></div>
  <script type="module" src="<?= $pathToAsset('index.*.js') ?>"></script>

</body>
</html>
