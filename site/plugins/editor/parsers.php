<?php

use Kirby\Editor\Parser;

return [
    'a' => function ($element) {
        return [
            [
                'type' => 'paragraph',
                'content' => $element->outerHtml,
            ]
        ];
    },
    'blockquote' => function ($element) {
        return [
            [
                'type'    => 'blockquote',
                'content' => Parser::sanitize($element->innerHtml),
            ]
        ];
    },
    'figure' => function ($element) {
        $caption = null;

        if ($figcaption = $element->find('figcaption')[0]) {
            $caption = Parser::sanitize($figcaption->innerHTML);
        }

        if ($image = $element->find('img')[0]) {
            return Parser::img($image, [
                'caption' => $caption
            ]);
        } elseif ($iframe = $element->find('iframe')[0]) {
            return Parser::iframe($iframe, [
                'caption' => $caption
            ]);
        }
    },
    'h1' => function ($element) {
        return [
            [
                'type'    => 'h1',
                'content' => Parser::sanitize($element->innerHtml),
                'attrs'   => [
                    'id' => $element->getAttribute('id')
                ]
            ]
        ];
    },
    'h2' => function ($element) {
        return [
            [
                'type'    => 'h2',
                'content' => Parser::sanitize($element->innerHtml),
            ]
        ];
    },
    'h3' => function ($element) {
        return [
            [
                'type'    => 'h3',
                'content' => Parser::sanitize($element->innerHtml),
            ]
        ];
    },
    'hr' => function ($element) {
        return [
            [
                'type'    => 'hr',
            ]
        ];
    },
    'iframe' => function ($iframe, array $attrs = []) {
        $src = $iframe->getAttribute('src');

        if (preg_match('!player.vimeo.com\/video\/([0-9]+)!i', $src, $array) === 1) {
            $src = 'https://vimeo.com/' . $array[1];
        } elseif (preg_match('!youtube.com\/embed\/([a-zA-Z0-9_-]+)!', $src, $array) === 1) {
            $src = 'https://youtube.com/watch?v=' . $array[1];
        } elseif (preg_match('!youtube-nocookie.com\/embed\/([a-zA-Z0-9_-]+)!', $src, $array) === 1) {
            $src = 'https://youtube.com/watch?v=' . $array[1];
        } else {
            return [
                [
                    'type' => 'kirbytext',
                    'content' => $iframe->outerHTML,
                ]
            ];
        }

        return [
            [
                'type' => 'video',
                'attrs' => array_merge([
                    'src' => $src,
                ], $attrs)
            ]
        ];
    },
    'img' => function ($image, array $attrs = []) {
        $parent = $image->getParent();
        $link   = null;

        if ($parent && $parent->tag->name() === 'a') {
            $link = $parent->getAttribute('href');
        }

        $src      = $image->getAttribute('src');
        $mediaUrl = kirby()->url('media');
        $parent   = null;
        $file     = null;

        // find kirby images
        if (preg_match('!^' . preg_quote($mediaUrl) . '\/(site|pages|users)\/(.*)$!', $src, $match)) {
            $types    = ['site' => 'site', 'users' => 'user', 'pages' => 'page'];
            $type     = $types[$match[1]] ?? 'page';
            $path     = dirname($match[2], 2);
            $filename = basename($match[2]);

            if ($parent = kirby()->$type($path)) {
                if ($file = $parent->file($filename)) {
                    $attrs['id']    = $file->id();
                    $attrs['guid']  = $file->panelUrl(true);
                    $attrs['ratio'] = $file->ratio();
                }
            }
        }

        return [
            [
                'type' => 'image',
                'attrs' => array_merge([
                    'src'  => $src,
                    'alt'  => $image->getAttribute('alt'),
                    'link' => $link
                ], $attrs)
            ]
        ];
    },
    'p' => function ($element) {
        return Parser::parse($element);
    },
    'pre' => function ($element) {
        if ($code = $element->find('code')[0]) {
            $class = $code->getAttribute('class');
            $lang  = null;

            foreach (Str::split($class, ' ') as $className) {
                if (Str::startsWith($className, 'language-') === true) {
                    $lang = str_replace('language-', '', $className);
                }
            }

            return [
                [
                    'type'     => 'code',
                    'content'  => Html::decode($code->innerHtml),
                    'attrs'    => ['language' => $lang]
                ]
            ];
        } else {
            return [
                [
                    'type'    => 'code',
                    'content' => Html::decode(strip_tags($element->innerHtml))
                ]
            ];
        }
    },
    'ol' => function ($element) {
        $list = [];

        foreach ($element->getChildren() as $child) {
            if ($child->tag->name() === 'li') {
                $list[] = [
                    'type'    => 'ol',
                    'content' => Parser::sanitize($child->innerHtml),
                ];
            }
        }

        return $list;
    },
    'table' => function ($element) {
        Parser::removeStyles($element);

        return [
            [
                'type'    => 'kirbytext',
                'content' => $element->outerHTML
            ]
        ];
    },
    'ul' => function ($element) {
        $list = [];

        foreach ($element->getChildren() as $child) {
            if ($child->tag->name() === 'li') {
                $list[] = [
                    'type'    => 'ul',
                    'content' => Parser::sanitize($child->innerHtml),
                ];
            }
        }

        return $list;
    },
    'unknown' => function ($element) {
        return Parser::parse($element);
    }
];
