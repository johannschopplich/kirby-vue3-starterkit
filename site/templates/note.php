<?php

$data = [
  'title' => $page->title()->value(),
  'metaTitle' => $page->customTitle()->or($page->title() . ' – ' . $site->title())->value(),
  'date' => $page->date()->toDate('d F Y'),
  'tags' => $page->tags()->or(null)->value(),
  'text' => $page->text()->kt()->value()
];

echo vite()->json($data);
