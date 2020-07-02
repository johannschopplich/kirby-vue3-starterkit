<?php

return [
    [
        'type'    => 'h1',
        'content' => 'Heading 1',
        'attrs'   => [
            'id' => null
        ]
    ],
    [
        'type'    => 'paragraph',
        'content' => 'Morbi leo <strong>risus</strong>, porta ac <a href="/test">consectetur</a> ac, vestibulum at eros.',
    ],
    [
        'type'    => 'h2',
        'content' => 'Heading 2',
    ],
    [
        'type'    => 'paragraph',
        'content' => 'Maecenas sed diam eget risus varius blandit sit amet <em>non</em> magna.',
    ],
    [
        'type'    => 'ol',
        'content' => 'Item A',
    ],
    [
        'type'    => 'ol',
        'content' => 'Item B',
    ],
    [
        'type'    => 'ol',
        'content' => 'Item C',
    ],
    [
        'type'  => 'image',
        'attrs' => [
            'src'     => '/ryan-cheng.jpg',
            'alt'     => '',
            'link'    => null,
            'caption' => null
        ],
    ],
    [
        'type'    => 'h3',
        'content' => 'Heading 3',
    ],
    [
        'type'    => 'paragraph',
        'content' => '<code>Praesent commodo cursus</code> magna, vel scelerisque nisl consectetur et.',
    ],
    [
        'type'    => 'ul',
        'content' => 'Item A',
    ],
    [
        'type'    => 'ul',
        'content' => 'Item B',
    ],
    [
        'type'    => 'ul',
        'content' => 'Item C',
    ],
    [
        'type'    => 'code',
        'content' => 'if ($page->isHomePage()) {' . PHP_EOL . ' echo "Home sweet home";' . PHP_EOL . '}',
        'attrs'   => [
            'language' => 'php'
        ]
    ],
    [
        'type' => 'hr',
    ],
    [
        'type'    => 'blockquote',
        'content' => 'Curabitur blandit tempus porttitor.',
    ],
];
