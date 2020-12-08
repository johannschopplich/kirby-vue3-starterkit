<?php

return [
    // (1)
    // The following data is mandatory for the frontend router to initialize:
    'children' => array_values(site()->children()->published()->map(fn($child) => [
        'uri' => $child->uri(),
        'title' => $child->content()->title()->value(),
        'isListed' => $child->isListed(),
        'template' => $child->intendedTemplate()->name(),
        'childTemplate' => $child->hasChildren() ? $child->children()->first()->intendedTemplate()->name() : null
    ])->data()),
    // (2)
    // You can add custom commonly used data, as done for this starterkit:
    'title' => site()->title()->value(),
    'social' => array_values(page('about')->social()->toStructure()->map(fn($social) => [
        'url' => $social->url()->value(),
        'platform' => $social->platform()->value()
    ])->data())
];
