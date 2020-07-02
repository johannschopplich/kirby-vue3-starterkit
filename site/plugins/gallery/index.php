<?php

/**
 * Plugins extend Kirby's core functionality.
 * You can extend/replace almost any system-relevant part.
 * This plugin uses a hook to replace the `{{ gallery }}` placeholders used in the note pages
 * with images from the selected album page that servers as gallery provider
 * More about plugins: https://getkirby.com/docs/guide/plugins/plugin-basics
 */

Kirby::plugin('starterkit/gallery', [
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
