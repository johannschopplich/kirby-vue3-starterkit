<?php

$title = $page->customTitle()->or($page->title() . ' – ' . $site->title());
$description = $page->description()->or($site->description());
$thumbnail = (function () use ($page, $site) {
  $file = $page->thumbnail()->toFile() ?? $site->thumbnail()->toFile();
  return $file ? $file->resize(1200)->url() : '/img/android-chrome-512x512.png';
})();

?>

<title><?= $title ?></title>
<link rel="canonical" href="<?= $page->url() ?>">

<meta name="description" content="<?= $description ?>">

<meta property="og:url" content="<?= $page->url() ?>">
<meta property="og:type" content="website">
<meta property="og:title" content="<?= $title ?>">
<meta property="og:description" content="<?= $description ?>">
<meta property="og:image" content="<?= $thumbnail ?>">

<meta name="twitter:url" content="<?= $page->url() ?>">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= $title ?>">
<meta name="twitter:description" content="<?= $description ?>">
<meta name="twitter:image" content="<?= $thumbnail ?>">

<meta name="theme-color" content="#41b883">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="<?= $site->title() ?>">

<link rel="manifest" href="/manifest.json">
<link rel="icon" href="/img/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/img/apple-touch-icon.png" sizes="180x180">
