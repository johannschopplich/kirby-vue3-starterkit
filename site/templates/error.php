<?php

$data = [
  'title' => $page->title()->value(),
  'metaTitle' => $page->customTitle()->or($page->title() . ' – ' . $site->title())->value(),
  'text' => ['html' => $page->text()->kt()->value()],
  'isErrorPage' => true
];

echo json_encode($data);
