<?php

/**
 * Resolve a page by directory path
 *
 * @param string $path
 * @return \Kirby\Cms\Page|null
 */
function resolveDir(string $path): ?Page
{
    $kirby = kirby();
    $root  = $kirby->root('content');

    if (is_dir($root . '/' . $path) === false) {
        return null;
    }

    $extension = $kirby->contentExtension();
    $parts     = explode('/', $path);
    $draft     = false;
    $parent    = null;
    $page      = null;

    if ($kirby->multilang()) {
        $extension = $kirby->defaultLanguage()->code() . '.' . $extension;
    }

    foreach ($parts as $part) {
        $root .= '/' . $part;

        if ($part === '_drafts') {
            $draft = true;
            continue;
        }

        if (preg_match('/^([0-9]+)_(.*)$/', $part, $match)) {
            $num  = $match[1];
            $slug = $match[2];
        } else {
            $num  = null;
            $slug = $part;
        }

        $params = [
            'dirname' => $part,
            'num'     => $num,
            'parent'  => $parent,
            'root'    => $root,
            'slug'    => $slug,
        ];

        if ($draft === true) {
            $params['isDraft'] = $draft;

            // Only direct subpages of a _drafts folder are marked as drafts
            $draft = false;
        }

        // Check for custom page models
        foreach (array_keys(Page::$models) as $model) {
            if (file_exists($root . '/' . $model . '.' . $extension)) {
                $params['model'] = $model;
                break;
            }
        }

        $parent = $page = Page::factory($params);
    }

    return $page;
}
