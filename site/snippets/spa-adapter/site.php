<?php

$data = [
  'title' => site()->title()->value(),
  // The following data is mandatory for the frontend router to initialize
  'children' => array_values(site()->children()->published()->map(fn($child) => [
    'id' => $child->id(),
    'title' => $child->content()->title()->value(),
    'template' => $child->intendedTemplate()->name(),
    'isListed' => $child->isListed(),
    'hasChildren' => $child->hasChildren(),
    'childTemplate' => $child->hasChildren() ? $child->children()->published()->first()->intendedTemplate()->name() : null
  ])->data()),
  // You can add custom commonly used data, as done for this starterkit:
  'social' => array_values(page('about')->social()->toStructure()->map(fn($social) => [
    'url' => $social->url()->value(),
    'platform' => $social->platform()->value()
  ])->data())
];

return \Kirby\Data\Data::encode($data, 'json');
