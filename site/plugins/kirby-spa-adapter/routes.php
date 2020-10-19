<?php

use Kirby\Cms\Url;
use Kirby\Toolkit\Tpl;
use KirbyExtended\SpaAdapter;

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
                return SpaAdapter::useSite();
            }

            // Prerender the page to prevent Kirby from using the error page's
            // HTTP status code, otherwise the service worker fails installing
            return (page($pageId) ?? site()->errorPage())->render();
        }
    ],

    /**
     * Serve the index snippet to all non-JSON templates
     */
    [
        'pattern' => '(:all)',
        'action'  => function ($pageId) {
            $site = site();
            $cachingActive = env('KIRBY_CACHE', false) === true && kirby()->user() === null;

            if ($cachingActive) {
                $cacheBucket = kirby()->cache('kirby-extended.spa-adapter');
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

            $renderedPage = Tpl::load(kirby()->root('snippets') . '/spa-index.php', compact('page', 'site'));

            if ($cachingActive && !$page->isErrorPage()) {
                $cacheBucket->set($page->id(), $renderedPage);
            }

            return $renderedPage;
        }
    ]
];
