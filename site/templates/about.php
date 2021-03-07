<?php

$data = [
  'title' => $page->title()->value(),
  'metaTitle' => $page->customTitle()->or($page->title() . ' – ' . $site->title())->value(),
  'email' => $page->email()->value(),
  'phone' => $page->phone()->value(),
  'address' => $page->address()->kt()->value(),
  'text' => $page->text()->kt()->value(),
  'social' => $page->social()->toStructure()->map(fn($social) => [
    'url' => $social->url()->value(),
    'platform' => $social->platform()->value()
  ])->values()
];

echo \Kirby\Data\Json::encode($data);
