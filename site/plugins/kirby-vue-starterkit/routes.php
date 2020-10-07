<?php

use Kirby\Cms\Url;
use Kirby\Toolkit\Tpl;

$apiLocation = Url::path(env('KIRBY_API_LOCATION', ''), false, true);

return [
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
            $cachingActive = env('KIRBY_CACHE', false) && kirby()->user() === null;

            if ($cachingActive === true) {
                $cacheBucket = kirby()->cache('johannschopplich.kirby-vue-starterkit');
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

            $renderedPage = Tpl::load(__DIR__ . '/templates/index.php', compact('page', 'site'));
            if ($cachingActive === true) {
                $cacheBucket->set($pageId, $renderedPage);
            }

            return $renderedPage;
        }
    ]
];
