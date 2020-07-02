<?php

@include_once __DIR__ . '/vendor/autoload.php';
@include_once __DIR__ . '/helpers.php';

load([
    // base classes
    'kirby\\editor\\block'  => __DIR__ . '/lib/Block.php',
    'kirby\\editor\\blocks' => __DIR__ . '/lib/Blocks.php',
    'kirby\\editor\\parser' => __DIR__ . '/lib/Parser.php',

    // block extensions
    'kirby\\editor\\blockquoteblock' => __DIR__ . '/lib/BlockquoteBlock.php',
    'kirby\\editor\\codeblock'       => __DIR__ . '/lib/CodeBlock.php',
    'kirby\\editor\\h1block'         => __DIR__ . '/lib/H1Block.php',
    'kirby\\editor\\h2block'         => __DIR__ . '/lib/H2Block.php',
    'kirby\\editor\\h3block'         => __DIR__ . '/lib/H3Block.php',
    'kirby\\editor\\hrblock'         => __DIR__ . '/lib/HrBlock.php',
    'kirby\\editor\\imageblock'      => __DIR__ . '/lib/ImageBlock.php',
    'kirby\\editor\\olblock'         => __DIR__ . '/lib/OlBlock.php',
    'kirby\\editor\\ulblock'         => __DIR__ . '/lib/UlBlock.php',
    'kirby\\editor\\videoblock'      => __DIR__ . '/lib/VideoBlock.php',
]);

// load all parsers
Kirby\Editor\Parser::$parsers = @include_once __DIR__ . '/parsers.php';

Kirby::plugin('getkirby/editor', [
    'fieldMethods' => [
        'blocks'   => $method = @include_once __DIR__ . '/method.php',
        'toBlocks' => $method,
    ],
    'fields' => [
        'editor' => @include_once __DIR__ . '/field.php'
    ],
    'snippets' => [
        'editor/blockquote' => __DIR__ . '/snippets/blockquote.php',
        'editor/code'       => __DIR__ . '/snippets/code.php',
        'editor/h1'         => __DIR__ . '/snippets/h1.php',
        'editor/h2'         => __DIR__ . '/snippets/h2.php',
        'editor/h3'         => __DIR__ . '/snippets/h3.php',
        'editor/hr'         => __DIR__ . '/snippets/hr.php',
        'editor/image'      => __DIR__ . '/snippets/image.php',
        'editor/kirbytext'  => __DIR__ . '/snippets/kirbytext.php',
        'editor/ol'         => __DIR__ . '/snippets/ol.php',
        'editor/paragraph'  => __DIR__ . '/snippets/paragraph.php',
        'editor/ul'         => __DIR__ . '/snippets/ul.php',
        'editor/video'      => __DIR__ . '/snippets/video.php',
    ],
    'translations' => [
        'de'    => @include_once __DIR__ . '/i18n/de.php',
        'en'    => @include_once __DIR__ . '/i18n/en.php',
        'es'    => @include_once __DIR__ . '/i18n/es.php',
        'fr'    => @include_once __DIR__ . '/i18n/fr.php',
        'it'    => @include_once __DIR__ . '/i18n/it.php',
        'lt'    => @include_once __DIR__ . '/i18n/lt.php',
        'nl'    => @include_once __DIR__ . '/i18n/nl.php',
        'pt_BR' => @include_once __DIR__ . '/i18n/pt_BR.php',
        'pt_PT' => @include_once __DIR__ . '/i18n/pt_PT.php',
        'ru'    => @include_once __DIR__ . '/i18n/ru.php',
        'sv_SE' => @include_once __DIR__ . '/i18n/sv_SE.php',
        'tr'    => @include_once __DIR__ . '/i18n/tr.php',
    ]
]);
