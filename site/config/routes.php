<?php

use Kirby\Cms\Url;
use Kirby\Toolkit\Str;
use Kirby\Toolkit\Tpl;

$apiLocation = Url::path(env('KIRBY_API_LOCATION', ''), false, true);

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
     * The following routes must remain the last ones
     */

    /**
     * Respond with JSON-encoded page data for any given URL ending with `.json`
     */
    [
        'pattern' => $apiLocation . '(:all).json',
        'action'  => function ($pageId) {
            kirby()->response()->json();
            // Prevent Kirby from falling back automatically to the error
            // page, otherwise the service worker would fail installing
            // due to the response's HTTP status code
            return (page($pageId) ?? site()->errorPage())->render();
        }
    ],

    /**
     * Redirect all non-JSON templates to the index snippet
     */
    [
        'pattern' => '(:all)',
        'action'  => function ($pageId) {
            $site = site();

            if (empty($pageId)) {
                $page = $site->homePage();
            } else {
                $page = page($pageId) ?? $site->errorPage();
            }

            return Tpl::load(kirby()->roots()->snippets() . '/vue-index.php', compact('page', 'site'));
        }
    ]
];
