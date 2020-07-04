<?php

$data = [
  'title' => $page->title()->value(),
  'metaTitle' => $page->customTitle()->or($page->title() . ' – ' . $site->title())->value(),
  'modified' => $page->modified(),

  // Make `site` object available through homepage
  'site' => [
    'title' => $site->title()->value(),
    'children' => array_values($site->children()->published()->map(function ($child) {
        return [
            'id' => $child->id(),
            'title' => $child->content()->title()->value(),
            'template' => $child->intendedTemplate()->name(),
            'isListed' => $child->isListed(),
            'children' => array_values($child->children()->published()->map(function ($grandChild) {
                return [
                    'id' => $grandChild->id(),
                    'template' => $grandChild->intendedTemplate()->name()
                ];
            })->data())
        ];
    })->data()),
    'modifiedIndex' => array_values($site->index()->map(function ($child) {
      if (!$child->isPublished()) return;
      return [
          'id' => $child->id(),
          'modified' => $child->modified()
      ];
    })->data()),
    'social' => array_values(page('about')->social()->toStructure()->map(function ($social) {
        return [
            'url' => $social->url()->value(),
            'platform' => $social->platform()->value()
        ];
    })->data())
  ]
];

echo json_encode($data);
