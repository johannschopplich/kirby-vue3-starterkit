<?php

include_once __DIR__ . '/helpers.php';

$flush = function() {
    kirby()->cache('lukaskleinschmidt.resolve')->flush();
};

Kirby::plugin('lukaskleinschmidt/resolve', [
    'options' => [
        'cache' => true,
    ],
    'hooks' => [
        'page.changeNum:before' => $flush,
        'page.changeSlug:before' => $flush,
        'page.changeStatus:before' => $flush,
        'route:after' => function ($route, $path, $method, $result) {
            if (option('lukaskleinschmidt.resolve.cache') === false) {
                return $result;
            }

            if (is_a($result, 'Kirby\Cms\Page') === false) {
                return $result;
            }

            $kirby = kirby();
            $cache = $kirby->cache('lukaskleinschmidt.resolve');
            $proxy = $cache->get($path, false);

            if ($proxy !== false) {
                $kirby->setCurrentTranslation($proxy['lang']);
                $kirby->setCurrentLanguage($proxy['lang']);
            }

            if ($proxy === false && $result->isResolvable()) {
                $cache->set($path, [
                    'dir'  => $result->diruri(),
                    'lang' => $kirby->languageCode(),
                ]);
            }

            return $result;
        },
        'route:before' => function ($route, $path, $method) {
            if (option('lukaskleinschmidt.resolve.cache') === false) {
                return;
            }

            $kirby = kirby();
            $cache = $kirby->cache('lukaskleinschmidt.resolve');
            $proxy = $cache->get($path, false);

            if ($proxy === false) {
                return;
            }

            if ($page = resolveDir($proxy['dir'])) {
                return $kirby->extend([
                    'pages' => [$path => $page]
                ]);
            }
            
            $cache->remove($path);
        },
    ],
    'pageMethods' => [
        'isResolvable' => function () {
            $path = $this->kirby()->path();
            $id   = $this->id();

            if ($path && $path !== $id) {
                return true;
            }

            if (substr_count($id, '/')) {
                return true;
            }

            return false;
        }
    ]
]);
