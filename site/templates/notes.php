<?php

$data = [
  'title' => $page->title()->value(),
  'metaTitle' => $page->customTitle()->or($page->title() . ' – ' . $site->title())->value(),
  'children' => array_values($page->children()->listed()->sortBy('date', 'desc')->map(function ($note) {
    return [
      'id' => $note->id(),
      'title' => $note->title()->value(),
      'date' => $note->date()->toDate('d F Y')
    ];
  })->data())
];

echo json_encode($data);
