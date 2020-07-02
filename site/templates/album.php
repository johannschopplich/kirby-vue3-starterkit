<?php

$data = [
  'title' => $page->title()->value(),
  'metaTitle' => $page->customTitle()->or($page->title() . ' – ' . $site->title())->value(),
  'headline' => $page->headline()->or($page->title())->value(),
  'description' => ['html' => $page->description()->kt()->value()],
  'tags' => $page->tags()->isNotEmpty() ? $page->tags()->value() : null,
  'cover' => $page->cover() === null ? null : [
    'url' => $page->cover()->crop(1024, 768)->url(),
    'alt' => $page->cover()->alt()->value()
  ],
  'gallery' => array_values($page->images()->sortBy('sort')->map(function ($image) {
    return [
      'link' => $image->link()->or($image->url())->value(),
      'url' => $image->crop(800, 1000)->url(),
      'alt' => $image->alt()->value()
    ];
  })->data())
];

echo json_encode($data);
