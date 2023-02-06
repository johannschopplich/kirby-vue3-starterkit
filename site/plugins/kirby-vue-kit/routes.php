<?php

use JohannSchopplich\VueKit\Page;
use Kirby\Cms\Url;
use Kirby\Filesystem\F;
use Kirby\Http\Response;

$apiLocation = Url::path(env('KIRBY_CONTENT_API_SLUG', ''), false, true);

return [
    /**
     * Return JSON-encoded page data for the frontend
     */
    [
        'pattern' => "{$apiLocation}(:all).json",
        'language' => '*',
        'action' => function (...$args) {
            $kirby = kirby();

            if ($kirby->multilang()) {
                [$languageCode, $pageId] = $args;
            } else {
                [$pageId] = $args;
            }

            $page = $kirby->page($pageId);

            if (!$page || !$page->isVerified(get('token'))) {
                $page = $kirby->site()->errorPage();
            }

            $json = Page::render($page, 'json');
            return Response::json($json);
        }
    ],

    /**
     * Serve the index template on every other route
     */
    [
        'pattern' => '(:all)',
        'language' => '*',
        'action' => function (...$args) {
            $kirby = kirby();

            if ($kirby->multilang()) {
                [$languageCode, $path] = $args;
            } else {
                [$path] = $args;
            }

            $extension = F::extension($path);

            // Try to resolve page and site files
            if (!empty($extension)) {
                $id = dirname($path);
                $filename = basename($path);

                // Try to resolve image urls for pages and drafts
                if ($page = $kirby->site()->findPageOrDraft($id)) {
                    return $page->file($filename);
                }

                // Try to resolve site files at last
                if ($file = $kirby->site()->file($filename)) {
                    return $file;
                }
            }

            // Fall back to homepage id
            if (empty($path)) {
                $path = site()->homePageId();
            }

            $page = $kirby->page($path);

            if (!$page || !$page->isVerified(get('token'))) {
                $page = $kirby->site()->errorPage();
            }

            $html = Page::render($page, 'html');
            return $html;
        }
    ]
];
