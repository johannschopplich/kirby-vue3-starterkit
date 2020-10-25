<?php

$data = [
  'title' => $page->title()->value(),
  'metaTitle' => $page->customTitle()->or($page->title() . ' – ' . $site->title())->value(),
  'date' => $page->date()->toDate('d F Y'),
  'tags' => $page->tags()->isNotEmpty() ? $page->tags()->value() : null,
  'text' => $page->text()->kt()->value()
];

echo \Kirby\Data\Json::encode($data);
