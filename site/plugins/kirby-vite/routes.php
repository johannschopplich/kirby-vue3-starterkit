<?php

use Kirby\Cms\Url;
use Kirby\Http\Response;
use KirbyExtended\Page;

$apiLocation = Url::path(env('CONTENT_API_SLUG', ''), false, true);

return [
    /**
     * Return JSON-encoded page data for the frontend
     */
    [
        'pattern' => "{$apiLocation}(:all).json",
        'language' => '*',
        'action' => function (...$args) {
            if (kirby()->multilang()) {
                [$languageCode, $pageId] = $args;
            } else {
                [$pageId] = $args;
            }

            $page = kirby()->page($pageId);

            if (!$page || !$page->isVerified(get('token'))) {
                $page = kirby()->site()->errorPage();
            };

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
            if (kirby()->multilang()) {
                [$languageCode, $pageId] = $args;
            } else {
                [$pageId] = $args;
            }

            // Fall back to homepage id
            if (empty($pageId)) {
                $pageId = site()->homePageId();
            }

            $page = kirby()->page($pageId);

            if (!$page || !$page->isVerified(get('token'))) {
                $page = kirby()->site()->errorPage();
            };

            $html = Page::render($page, 'html');
            return $html;
        }
    ]
];
