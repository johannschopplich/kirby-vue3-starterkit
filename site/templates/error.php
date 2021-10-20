<?php

$data = [
  '__isErrorPage' => true,
  'title' => $page->title()->value(),
  'metaTitle' => $page->customTitle()->or($page->title() . ' – ' . $site->title())->value(),
  'text' => $page->text()->kt()->value()
];

echo vite()->json($data);
