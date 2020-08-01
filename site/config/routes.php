<?php

use Kirby\Toolkit\Tpl;
use Kirby\Toolkit\Str;

$apiLocation = env('KIRBY_API_LOCATION', '');
if (!empty($apiLocation)) {
    $apiLocation = Str::replace($apiLocation, '/', '') . '/';
}

return [
    /**
     * Redirect robots.txt to instruct robots (typically search engine robots)
     * how to crawl & index pages on this website
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
     * Respond with JSON-encoded page data for any given URL ending with `.json`
     */
    [
        'pattern' => [$apiLocation . '(:all).json'],
        'action'  => function ($pageId) {
            kirby()->response()->json();
            // Prevent Kirby from falling back automatically to the error
            // page, otherwise the service worker would fail installing
            // due to the response's http status code
            return (page($pageId) ?? site()->errorPage())->render();
        }
    ],

    /**
     * Redirect all non-JSON templates to the index snippet
     */
    [
        'pattern' => ['(:all)'],
        'action'  => function ($pageId) {
            $site = site();

            if (empty($pageId)) {
                $page = $site->homePage();
            } else {
                $page = page($pageId) ?? $site->errorPage();
            }

            return Tpl::load(kirby()->roots()->snippets() . '/index.php', compact('page', 'site'));
        }
    ]
];
