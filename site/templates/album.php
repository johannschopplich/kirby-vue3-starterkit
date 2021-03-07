<?php

$data = [
  'title' => $page->title()->value(),
  'metaTitle' => $page->customTitle()->or($page->title() . ' – ' . $site->title())->value(),
  'headline' => $page->headline()->or($page->title())->value(),
  'description' => $page->description()->kt()->value(),
  'tags' => $page->tags()->isNotEmpty() ? $page->tags()->value() : null,
  'cover' => $page->cover() === null ? null : [
    'url' => $page->cover()->crop(1024, 768)->url(),
    'alt' => $page->cover()->alt()->value()
  ],
  'gallery' => $page->images()->sortBy('sort')->map(fn($image) => [
    'link' => $image->link()->or($image->url())->value(),
    'url' => $image->crop(800, 1000)->url(),
    'alt' => $image->alt()->value()
  ])->values()
];

echo \Kirby\Data\Json::encode($data);
