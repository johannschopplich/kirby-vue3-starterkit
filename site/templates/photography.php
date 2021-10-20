<?php

$data = [
  'title' => $page->title()->value(),
  'metaTitle' => $page->customTitle()->or($page->title() . ' – ' . $site->title())->value(),
  'children' => $page
    ->children()
    ->listed()
    ->map(fn ($album) => [
      'uri' => $album->uri(),
      'title' => $album->title()->value(),
      'cover' => ($image = $album->cover()->toFile()) ? [
        'url' => $image->crop(800, 1000)->url(),
        'urlHome' => $image->resize(1024, 1024)->url(),
        'alt' => $image->alt()->value()
      ] : null
    ])
    ->values()
];

echo vite()->json($data);
