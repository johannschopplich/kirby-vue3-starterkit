<?php

use Kirby\Toolkit\Tpl;

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
     * Redirect all non-JSON templates to Vue index (https://router.vuejs.org/guide/essentials/history-mode.html).
     * Taken from https://getkirby.com/docs/guide/routing#defining-your-own-routes
     */
    [
        'pattern' => ['(:all).json'],
        'action'  => function ($pageId) {
            if (option('debug') === true) header('Access-Control-Allow-Origin: *');

            kirby()->response()->json();
            return (page($pageId) ?? page('error'))->render();
        }
    ],
    [
        'pattern' => ['(:all)'],
        'action'  => function ($pageId) {
            $site = site();

            if (empty($pageId) === true || $pageId === 'index.html') {
                $pageId = $site->homePage()->id();
            }

            $page = page($pageId) ?? page('error');

            return Tpl::load(kirby()->roots()->snippets() . '/index.php', compact('page', 'site'), false);
        }
    ]
];
