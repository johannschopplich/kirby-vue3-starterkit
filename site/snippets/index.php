<?php

$pathToAsset = function ($pattern, $root = '/assets/') {
  return $root . basename(glob(kirby()->roots()->index() . $root . $pattern)[0]);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php snippet('meta', compact('page', 'site')) ?>

  <link rel="stylesheet" href="<?= $pathToAsset('style.*.css') ?>">

</head>
<body>

  <div id="app"></div>
  <script type="module" src="<?= $pathToAsset('index.*.js') ?>"></script>

</body>
</html>
