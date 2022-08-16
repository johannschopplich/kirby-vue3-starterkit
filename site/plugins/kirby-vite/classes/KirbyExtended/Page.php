<?php

namespace KirbyExtended;

use Kirby\Cms\Template;
use Kirby\Exception\NotFoundException;

class Page
{
    protected static \Kirby\Cms\Page $page;

    /**
     * Renders a page with the given data
     *
     * Almost identical to Kirby's internal
     * page render function
     *
     * @throws \Kirby\Exception\NotFoundException If the default template cannot be found
     */
    public static function render(\Kirby\Cms\Page $page, string $contentType): string
    {
        static::$page = $page;
        $kirby = kirby();
        $cache = $cacheId = $data = null;

        // Try to get the page from cache
        if ($page->isCacheable()) {
            $cache    = $kirby->cache('pages');
            $cacheId  = static::cacheId($contentType);
            $result   = $cache->get($cacheId);
            $data     = $result['data'] ?? null;
            $response = $result['response'] ?? [];

            // Reconstruct the response configuration
            if (!empty($data) && !empty($response)) {
                $kirby->response()->fromArray($response);
            }
        }

        // Fetch the page regularly
        if ($data === null) {
            // Return the index template for all HTML pages
            if ($contentType === 'html') {
                $template = new Template('_app-index');
                $data = $template->render([
                    'kirby' => $kirby,
                    'site'  => $kirby->site(),
                    'page'  => static::$page
                ]);
            }

            if ($contentType === 'json') {
                $template = $page->template();

                if (!$template->exists()) {
                    throw new NotFoundException([
                        'key' => 'template.default.notFound'
                    ]);
                }

                $kirby->data = $page->controller();
                $data = $template->render($kirby->data);
            }

            // Convert the response configuration to an array
            $response = $kirby->response()->toArray();

            // Cache the result
            if ($cache !== null) {
                $cache->set($cacheId, [
                    'data' => $data,
                    'response' => $response
                ]);
            }
        }

        return $data;
    }

    /**
     * Builds the cache id for a page
     */
    protected static function cacheId(string $contentType): string
    {
        $cacheId = [static::$page->id()];

        if (static::$page->kirby()->multilang()) {
            $cacheId[] = static::$page->kirby()->language()->code();
        }

        $cacheId[] = $contentType;

        return implode('.', $cacheId);
    }
}
