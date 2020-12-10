<?php

use Kirby\Cms\Url;
use Kirby\Http\Response;
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
            if ($pageId === '__site') {
                // Return the global `site` object, used singly in development environment
                $data = SpaAdapter::useSite();
            } else {
                // Prerender the page to prevent Kirby from using the error page's
                // HTTP status code, otherwise the service worker fails installing
                $data = (page($pageId) ?? site()->errorPage())->render();
            }

            return Response::json($data);
        }
    ],

    /**
     * Serve the index snippet to all non-JSON templates
     */
    [
        'pattern' => '(:all)',
        'action'  => function ($pageId) {
            $site = site();
            $enableCache = env('KIRBY_CACHE', false) === true && kirby()->user() === null;

            if ($enableCache) {
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

            if ($enableCache && !$page->isErrorPage()) {
                $cacheBucket->set($page->uri(), $renderedPage);
            }

            return $renderedPage;
        }
    ]
];
