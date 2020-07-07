<?php

$data = [
  'title' => $page->title()->value(),
  'metaTitle' => $page->customTitle()->or($page->title() . ' – ' . $site->title())->value(),

  // Create `$site` analogy available through homepage
  'site' => [
    'title' => $site->title()->value(),
    'children' => array_values($site->children()->published()->map(fn($child) => [
      'id' => $child->id(),
      'title' => $child->content()->title()->value(),
      'template' => $child->intendedTemplate()->name(),
      'isListed' => $child->isListed(),
      'modified' => $child->modified(),
      'children' => array_values($child->children()->published()->map(fn($grandChild) => [
        'id' => $grandChild->id(),
        'template' => $grandChild->intendedTemplate()->name(),
        'modified' => $grandChild->modified(),
      ])->data())
    ])->data()),
    'social' => array_values(page('about')->social()->toStructure()->map(fn($social) => [
      'url' => $social->url()->value(),
      'platform' => $social->platform()->value()
    ])->data())
  ]
];

echo json_encode($data);
