<?php

use Kirby\Cms\Url;
use Kirby\Toolkit\Tpl;

$apiLocation = Url::path(env('CONTENT_API_SLUG', ''), false, true);

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

            // Return the global `site` object, used singly in development environment
            if ($pageId === '__site') {
                return Tpl::load(kirby()->roots()->snippets() . '/vue-site.php', ['site' => site()]);
            }

            // Prerender the page to prevent Kirby from using the error page's
            // HTTP status code, otherwise the service worker fails installing
            return (page($pageId) ?? site()->errorPage())->render();
        }
    ],

    /**
     * Redirect all non-JSON templates to the index snippet
     */
    [
        'pattern' => '(:all)',
        'action'  => function ($pageId) {
            $cachingActive = env('KIRBY_CACHE', false) === true && kirby()->user() === null;

            if ($cachingActive) {
                $cacheBucket = kirby()->cache('templates');
                $pageProxy = $cacheBucket->get($pageId);

                if ($pageProxy !== null) {
                    return $pageProxy;
                }
            }

            $site = site();
            if (empty($pageId)) {
                $page = $site->homePage();
            } else {
                $page = page($pageId) ?? $site->errorPage();
            }

            $renderedPage = Tpl::load(kirby()->roots()->snippets() . '/vue-index.php', compact('page', 'site'));

            if ($cachingActive && !$page->isErrorPage()) {
                $cacheBucket->set($pageId, $renderedPage);
            }

            return $renderedPage;
        }
    ]
];
