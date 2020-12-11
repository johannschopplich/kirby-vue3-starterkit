<?php

use Kirby\Cms\Url;
use Kirby\Http\Response;
use Kirby\Toolkit\Tpl;
use KirbyExtended\SpaAdapter;

$apiLocation = Url::path(env('CONTENT_API_SLUG', ''), false, true);

return [
    /**
     * Return the global `site` object, used singly in development environment
     */
    [
        'pattern' => "{$apiLocation}__site.json",
        'language' => '*',
        'action' => function () {
            $data = SpaAdapter::useSite();
            return Response::json($data);
        }
    ],

    /**
     * Respond with JSON-encoded page data for any given URL ending with `.json`
     */
    [
        'pattern' => "{$apiLocation}(:all).json",
        'language' => '*',
        'action' => function (...$args) {
            if (kirby()->multilang()) {
                [$language, $pageId] = $args;
            } else {
                [$pageId] = $args;
            }

            $page = page($pageId) ?? site()->errorPage();

            // Get page object for specified language
            if (kirby()->multilang()) {
                $page = site()->visit($page, $language);
            }

            // Prerender the page to prevent Kirby from using the error page's
            // HTTP status code, otherwise the service worker fails installing
            $data = $page->render();

            return Response::json($data);
        }
    ],

    /**
     * Serve the index snippet to all non-JSON templates
     */
    [
        'pattern' => '(:all)',
        'language' => '*',
        'action' => function (...$args) {
            if (kirby()->multilang()) {
                [$language, $pageId] = $args;
            } else {
                [$pageId] = $args;
            }

            $site = site();
            $enableCache = env('KIRBY_CACHE', false) === true && kirby()->user() === null;

            if ($enableCache) {
                $cachePrefix = isset($language) ? "{$language}/" : '';
                $cacheBucket = kirby()->cache('kirby-extended.spa-adapter');
                $pageProxy = $cacheBucket->get($cachePrefix . ($pageId ?? $site->homePageId()));

                if ($pageProxy !== null) {
                    return $pageProxy;
                }
            }

            if (empty($pageId)) {
                $page = $site->homePage();
            } else {
                $page = page($pageId) ?? $site->errorPage();
            }

            // Get page object for specified language
            if (kirby()->multilang()) {
                $page = site()->visit($page, $language);
            }

            $renderedPage = Tpl::load(kirby()->root('snippets') . '/spa-index.php', compact('page', 'site'));

            if ($enableCache && !$page->isErrorPage()) {
                $cacheBucket->set($cachePrefix . $page->uri(), $renderedPage);
            }

            return $renderedPage;
        }
    ]
];
