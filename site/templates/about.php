<?php

$data = [
  'title' => $page->title()->value(),
  'metaTitle' => $page->customTitle()->or($page->title() . ' – ' . $site->title())->value(),
  'email' => $page->email()->value(),
  'phone' => $page->phone()->value(),
  'address' => ['html' => $page->address()->kt()->value()],
  'text' => ['html' => $page->text()->kt()->value()],
  'social' => array_values($page->social()->toStructure()->map(function ($social) {
    return [
      'url' => $social->url()->value(),
      'platform' => $social->platform()->value()
    ];
  })->data())
];

echo json_encode($data);
