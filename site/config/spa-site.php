<?php

use Kirby\Http\Url;
use Kirby\Toolkit\Str;

$languageCode = kirby()->languageCode();

// Parse language code from URL in development environment
$path = Url::current();
if (Str::endsWith($path, '__site.json')) {
    $languageCode = basename(dirname($path));
}

return [
    // (1)
    // The following data is mandatory for the frontend router to initialize:
    'children' => array_values(site()->children()->published()->map(fn($child) => [
        'uri' => $child->uri(),
        'title' => $child->content($languageCode)->title()->value(),
        'isListed' => $child->isListed(),
        'template' => $child->intendedTemplate()->name(),
        'childTemplate' => $child->hasChildren() ? $child->children()->first()->intendedTemplate()->name() : null
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
