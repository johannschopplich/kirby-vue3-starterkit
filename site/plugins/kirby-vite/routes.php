<?php

use Kirby\Cms\Url;
use Kirby\Http\Response;
use Kirby\Toolkit\Tpl;

$apiLocation = Url::path(env('CONTENT_API_SLUG', ''), false, true);

return [
    /**
     * Return JSON-encoded page data for API requests
     */
    [
        'pattern' => "{$apiLocation}(:all).json",
        'language' => '*',
        'action' => function (...$args) {
            $site = site();
            $isMultilang = kirby()->multilang();

            if ($isMultilang) {
                [$language, $pageId] = $args;
            } else {
                [$pageId] = $args;
            }

            $page = page($pageId) ?? $site->errorPage();

            // Get page object for specified language
            if ($isMultilang) {
                $page = $site->visit($page, $language);
            }

            // Prerender the page to prevent Kirby from using the error page's
            // HTTP status code, otherwise the service worker fails installing
            $data = $page->render();

            return Response::json($data);
        }
    ],

    /**
     * Serve the index template on every route
     */
    [
        'pattern' => '(:all)',
        'language' => '*',
        'action' => function (...$args) {
            $kirby = kirby();
            $site = $kirby->site();
            $isMultilang = $kirby->multilang();

            if ($isMultilang) {
                [$language, $pageId] = $args;
            } else {
                [$pageId] = $args;
            }

            // Fall back to homepage id
            if (empty($pageId)) {
                $pageId = $site->homePageId();
            }

            $cacheActive = env('KIRBY_CACHE', false) === true && $kirby->user() === null;
            $cacheBucket = $kirby->cache('kirby-extended.vite');
            $cachePrefix = $isMultilang ? "{$language}/" : '';

            if ($cacheActive && $cacheBucket->exists($cachePrefix . $pageId)) {
                return $cacheBucket->get($cachePrefix . $pageId);
            }

            $page = page($pageId) ?? $site->errorPage();

            // Get page object for specified language
            if ($isMultilang) {
                $page = $site->visit($page, $language);
            }

            $renderedPage = Tpl::load($kirby->root('templates') . '/_app-index.php', compact('kirby', 'site', 'page'));

            if ($cacheActive && !$page->isErrorPage()) {
                $cacheBucket->set($cachePrefix . $page->uri(), $renderedPage);
            }

            return $renderedPage;
        }
    ]
];
