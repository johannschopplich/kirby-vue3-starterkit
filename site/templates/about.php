<?php

$data = [
  'title' => $page->title()->value(),
  'metaTitle' => $page->customTitle()->or($page->title() . ' – ' . $site->title())->value(),
  'email' => $page->email()->value(),
  'phone' => $page->phone()->value(),
  'address' => $page->address()->kt()->value(),
  'text' => $page->text()->kt()->value(),
  'social' => $page->social()->toStructure()->toArray()
];

echo vite()->json($data);
