<?php

return [
    // (1)
    // The following data is mandatory for the frontend router to initialize:
    'children' => array_values(site()->children()->published()->map(fn($child) => [
        'uri' => $child->uri(),
        'title' => $child->content()->title()->value(),
        'template' => $child->intendedTemplate()->name(),
        'isListed' => $child->isListed(),
        'hasChildren' => $child->hasChildren(),
        'childTemplate' => $child->hasChildren() ? $child->children()->published()->first()->intendedTemplate()->name() : null
    ])->data()),
    // (2)
    // The following data is required for multi-language setups:
    'languages' => array_values(kirby()->languages()->map(fn($language) => [
        'code' => $language->code(),
        'name' => $language->name(),
        'isDefault' => $language->isDefault()
    ])->data()),
    // (3)
    // You can add custom commonly used data, as done for this starterkit:
    'title' => site()->title()->value(),
    'social' => array_values(page('about')->social()->toStructure()->map(fn($social) => [
        'url' => $social->url()->value(),
        'platform' => $social->platform()->value()
    ])->data())
];
