<?php

$data = [
  'title' => $page->title()->value(),
  'metaTitle' => $page->customTitle()->or($page->title() . ' – ' . $site->title())->value(),
  'children' => $page
    ->children()
    ->listed()
    ->sortBy('date', 'desc')
    ->map(fn ($note) => [
      'uri' => $note->uri(),
      'title' => $note->title()->value(),
      'date' => $note->date()->toDate('d F Y')
    ])
    ->values()
];

echo vite()->json($data);
