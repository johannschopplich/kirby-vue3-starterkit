<?php

return [
    // (1)
    // The following data is mandatory for the frontend router to initialize:
    'children' => site()
        ->children()
        ->published()
        ->map(fn ($child) => [
            'uri' => $child->uri(),
            'title' => $child->title()->value(),
            'isListed' => $child->isListed(),
            'template' => $child->intendedTemplate()->name(),
            'childTemplate' => $child->hasChildren() ? $child->children()->first()->intendedTemplate()->name() : null
        ])
        ->values(),

    // (2)
    // The following data is required for multi-language setups:
    'languages' => kirby()
        ->languages()
        ->toArray(),

    // (3)
    // You can add custom commonly used data, as done for this starterkit:
    'title' => site()->title()->value(),
    'social' => page('about')
        ->social()
        ->toStructure()
        ->toArray()
];
