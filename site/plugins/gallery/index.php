<?php

\Kirby\Cms\App::plugin('starterkit/gallery', [
    'snippets' => [
        'gallery' => __DIR__ . '/snippets/gallery.php'
    ],
    'hooks' => [
        'kirbytags:after' => function ($text, $data, $options) {
            if ($page = $data['parent']->gallery()->toPage()) {
                $gallery = snippet('gallery', ['gallery' => $page], true);
            } else {
                $gallery = '';
            }

            return str_replace('{{ gallery }}', $gallery, $text);
        }
    ]
]);
