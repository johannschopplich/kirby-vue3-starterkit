<?php

namespace Kirby\Editor;

use PHPHtmlParser\Dom;

class Parser
{
    public static $parent = null;
    public static $parsers = [];

    public static function __callStatic(string $name, array $args = [])
    {
        $parser = static::$parsers[$name] ?? static::$parsers['unknown'];
        return $parser(...$args);
    }

    public static function parse($dom, bool $markdown = false, $parent = null)
    {
        static::$parent = $parent ?? static::$parent;

        // convert html to dom element if a string is being passed
        if (is_string($dom) === true) {
            $dom = static::dom($dom);
        }

        if (empty($dom) === true) {
            return [];
        }

        // don't even work with those guys
        $skip   = ['meta', 'style', 'script', 'noscript', 'title'];

        $result = [];
        $inline = [];

        foreach ($dom->getChildren() as $element) {
            $tag  = $element->tag;
            $name = $tag->name();

            // kill all style attributes
            $element->removeAttribute('style');

            // skip empty text nodes
            if ($name === 'text' && $element->innerHtml == '') {
                continue;
            }

            // ignore unwanted elements
            if (in_array($name, $skip)) {
                continue;
            }

            // add all inline elements to the inline array
            if (static::isInline($name)) {
                if ($name !== 'text') {
                    // check for nested non-inline blocks
                    $blocks = static::parse($element);

                    // check if there are any non-paragraphs in the list
                    $types = array_unique(array_column($blocks, 'type'));

                    // it's going to end up as a single block
                    if (empty($blocks) === true || $types === ['paragraph']) {
                        $inline[] = $element->outerHtml;

                    // split different block types
                    } else {
                        foreach ($blocks as $childBlock) {
                            if ($childBlock['type'] !== 'paragraph') {
                                static::inlineEnd($inline, $result, $markdown);
                                $result[] = $childBlock;
                            } else {
                                $inline[] = $childBlock['content'];
                            }
                        }
                    }
                } else {
                    $inline[] = $element->outerHtml;
                }
            } else {
                static::inlineEnd($inline, $result, $markdown);

                $blocks = static::$name($element);

                if (empty($blocks) === false && is_array($blocks)) {
                    $result = array_merge($result, $blocks);
                }
            }
        }

        static::inlineEnd($inline, $result, $markdown);

        // trim the content of each block
        foreach ($result as $index => $block) {
            if (isset($block['content'])) {
                $content = trim($block['content'] ?? '');
                $result[$index]['content'] = $content;

                // remove empty paragraphs
                if ($block['type'] === 'paragraph' && empty($content) === true) {
                    unset($result[$index]);
                }
            }
        }

        return $result;
    }

    public static function dom($html)
    {
        $dom = new Dom();
        $dom->loadStr($html, [
            'whitespaceTextNode' => true,
            'preserveLineBreaks' => true
        ]);

        return $dom;
    }

    public static function sanitize(string $html, bool $trim = true, string $tags = '<a><b><em><i><strong><code><br><del>')
    {
        if ($trim === true) {
            $html = trim($html);
        }

        $html = strip_tags($html, $tags);
        $dom  = static::dom($html);

        static::sanitizeAttributes($dom);

        // rude replacements
        $replace = [
            '<b>'  => '<strong>',
            '</b>' => '</strong>',
            '<i>'  => '<em>',
            '</i>' => '</em>',
        ];

        $html = $dom->outerHTML;
        $html = str_replace(array_keys($replace), array_values($replace), $html);

        return $html;
    }

    public static function sanitizeAttributes($element)
    {
        $keep = ['href', 'target', 'title'];

        if (method_exists($element, 'getAttributes') === true) {
            foreach ($element->getAttributes() as $key => $value) {
                if (in_array($key, $keep) === false) {
                    $element->removeAttribute($key);
                }
            }
        }

        if (method_exists($element, 'getChildren') === true) {
            foreach ($element->getChildren() as $child) {
                static::sanitizeAttributes($child);
            }
        }
    }

    public static function removeStyles($element)
    {
        if (method_exists($element, 'removeAttribute') === true) {
            $element->removeAttribute('style');
        }

        if (method_exists($element, 'getChildren') === true) {
            foreach ($element->getChildren() as $child) {
                static::removeStyles($child);
            }
        }
    }

    public static function markdown($text)
    {
        return kirbytext($text, [
            'parent' => static::$parent
        ]);
    }

    public static function isInline($tag): bool
    {
        $inline = [
            'a',
            'abbr',
            'acronym',
            'b',
            'bdo',
            'br',
            'button',
            'cite',
            'code',
            'del',
            'dfn',
            'em',
            'font',
            'i',
            // 'img', => can be converted to its own block
            'input',
            'kbd',
            'label',
            'map',
            'object',
            'q',
            'samp',
            'script',
            'select',
            'small',
            'span',
            'strong',
            'sub',
            'sup',
            'text',
            'textarea',
            'tt',
            'var',
        ];

        return in_array($tag, $inline) === true;
    }

    public static function inlineEnd(&$inline, &$result, $markdown = false)
    {
        // convert all previous inline elements
        // to a new block
        if (empty($inline) === false) {
            if ($markdown === true) {
                $html   = static::markdown(implode($inline));
                $result = array_merge($result, static::parse($html));
            } elseif ($paragraph = static::inlineToParagraph($inline)) {
                $result[] = $paragraph;
            }

            $inline = [];
        }
    }

    public static function inlineToParagraph(array $inline)
    {
        $html = static::sanitize(implode($inline), false);
        $skip = ['<br>', '<br/>', '<br />', ''];

        if (in_array($html, $skip)) {
            return false;
        }

        return [
            'type'    => 'paragraph',
            'content' => $html,
        ];
    }
}
