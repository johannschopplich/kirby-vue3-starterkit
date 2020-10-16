<?php

use Kirby\Cms\Url;
use Kirby\Toolkit\Tpl;

$apiLocation = Url::path(env('CONTENT_API_SLUG', ''), false, true);

return [
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
            $site = site();
            $cachingActive = env('KIRBY_CACHE', false) === true && kirby()->user() === null;

            if ($cachingActive) {
                $cacheBucket = kirby()->cache('kirby-extended.vue-integration');
                $pageProxy = $cacheBucket->get($pageId ?? $site->homePageId());

                if ($pageProxy !== null) {
                    return $pageProxy;
                }
            }

            if (empty($pageId)) {
                $page = $site->homePage();
            } else {
                $page = page($pageId) ?? $site->errorPage();
            }

            $renderedPage = Tpl::load(kirby()->roots()->snippets() . '/vue-index.php', compact('page', 'site'));

            if ($cachingActive && !$page->isErrorPage()) {
                $cacheBucket->set($page->id(), $renderedPage);
            }

            return $renderedPage;
        }
    ]
];
