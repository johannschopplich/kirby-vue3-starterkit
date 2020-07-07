<?php

use Kirby\Toolkit\Tpl;
use Kirby\Toolkit\Str;

$apiLocation = env('KIRBY_API_LOCATION', '');

if (!empty($apiLocation)) {
    // Remove leading slash if present
    if (Str::startsWith($apiLocation, '/')) {
        Str::rtrim($apiLocation, '/');
    }
    // Add trailing slash if not given
    if (!Str::endsWith($apiLocation, '/')) {
        $apiLocation .= '/';
    }
}

return [
    /**
     * Redirect robots.txt to instruct robots (typically search engine robots)
     * how to crawl & index pages on this website.
     */
    [
        'pattern' => 'robots.txt',
        'method'  => 'ALL',
        'action'  => function () {
            $robots = 'User-agent: *' . PHP_EOL;
            $robots .= 'Allow: /' . PHP_EOL;
            $robots .= 'Sitemap: ' . url('sitemap.xml');
            return kirby()
                ->response()
                ->type('text')
                ->body($robots);
        }
    ],
    /**
     * Respond with JSON-encoded page data for any given URL ending with `.json`.
     */
    [
        'pattern' => [$apiLocation . '(:all).json'],
        'action'  => function ($pageId) {
            kirby()->response()->json();
            return (page($pageId) ?? page('error'))->render();
        }
    ],
    /**
     * Redirect all non-JSON templates to the index snippet.
     */
    [
        'pattern' => ['(:all)'],
        'action'  => function ($pageId) {
            $site = site();

            if (empty($pageId) === true || $pageId === 'index.html') {
                $pageId = $site->homePage()->id();
            }

            $page = page($pageId) ?? page('error');

            return Tpl::load(kirby()->roots()->snippets() . '/index.php', compact('page', 'site'));
        }
    ]
];
