<?php

declare(strict_types=1);

namespace voku\helper;

/**
 * Class HtmlMin
 *
 * Inspired by:
 * - JS: https://github.com/kangax/html-minifier/blob/gh-pages/src/htmlminifier.js
 * - PHP: https://github.com/searchturbine/phpwee-php-minifier
 * - PHP: https://github.com/WyriHaximus/HtmlCompress
 * - PHP: https://github.com/zaininnari/html-minifier
 * - PHP: https://github.com/ampaze/PHP-HTML-Minifier
 * - Java: https://code.google.com/archive/p/htmlcompressor/
 *
 * Ideas:
 * - http://perfectionkills.com/optimizing-html/
 */
class HtmlMin implements HtmlMinInterface
{
    /**
     * @var string
     */
    private static $regExSpace = "/[[:space:]]{2,}|[\r\n]/u";

    /**
     * @var string[]
     *
     * @psalm-var list<string>
     */
    private static $optional_end_tags = [
        'html',
        'head',
        'body',
    ];

    /**
     * @var string[]
     *
     * @psalm-var list<string>
     */
    private static $selfClosingTags = [
        'area',
        'base',
        'basefont',
        'br',
        'col',
        'command',
        'embed',
        'frame',
        'hr',
        'img',
        'input',
        'isindex',
        'keygen',
        'link',
        'meta',
        'param',
        'source',
        'track',
        'wbr',
    ];

    /**
     * @var string[]
     *
     * @psalm-var array<string, string>
     */
    private static $trimWhitespaceFromTags = [
        'article' => '',
        'br'      => '',
        'div'     => '',
        'footer'  => '',
        'hr'      => '',
        'nav'     => '',
        'p'       => '',
        'script'  => '',
    ];

    /**
     * @var array
     */
    private static $booleanAttributes = [
        'allowfullscreen' => '',
        'async'           => '',
        'autofocus'       => '',
        'autoplay'        => '',
        'checked'         => '',
        'compact'         => '',
        'controls'        => '',
        'declare'         => '',
        'default'         => '',
        'defaultchecked'  => '',
        'defaultmuted'    => '',
        'defaultselected' => '',
        'defer'           => '',
        'disabled'        => '',
        'enabled'         => '',
        'formnovalidate'  => '',
        'hidden'          => '',
        'indeterminate'   => '',
        'inert'           => '',
        'ismap'           => '',
        'itemscope'       => '',
        'loop'            => '',
        'multiple'        => '',
        'muted'           => '',
        'nohref'          => '',
        'noresize'        => '',
        'noshade'         => '',
        'novalidate'      => '',
        'nowrap'          => '',
        'open'            => '',
        'pauseonexit'     => '',
        'readonly'        => '',
        'required'        => '',
        'reversed'        => '',
        'scoped'          => '',
        'seamless'        => '',
        'selected'        => '',
        'sortable'        => '',
        'truespeed'       => '',
        'typemustmatch'   => '',
        'visible'         => '',
    ];

    /**
     * @var array
     */
    private static $skipTagsForRemoveWhitespace = [
        'code',
        'pre',
        'script',
        'style',
        'textarea',
    ];

    /**
     * @var array
     */
    private $protectedChildNodes = [];

    /**
     * @var string
     */
    private $protectedChildNodesHelper = 'html-min--voku--saved-content';

    /**
     * @var bool
     */
    private $doOptimizeViaHtmlDomParser = true;

    /**
     * @var bool
     */
    private $doOptimizeAttributes = true;

    /**
     * @var bool
     */
    private $doRemoveComments = true;

    /**
     * @var bool
     */
    private $doRemoveWhitespaceAroundTags = false;

    /**
     * @var bool
     */
    private $doRemoveOmittedQuotes = true;

    /**
     * @var bool
     */
    private $doRemoveOmittedHtmlTags = true;

    /**
     * @var bool
     */
    private $doRemoveHttpPrefixFromAttributes = false;

    /**
     * @var bool
     */
    private $doRemoveHttpsPrefixFromAttributes = false;

    /**
     * @var bool
     */
    private $doKeepHttpAndHttpsPrefixOnExternalAttributes = false;

    /**
     * @var bool
     */
    private $doMakeSameDomainsLinksRelative = false;

    /**
     * @var string[]
     */
    private $localDomains = [];

    /**
     * @var string[]
     */
    private $domainsToRemoveHttpPrefixFromAttributes = [
        'google.com',
        'google.de',
    ];

    /**
     * @var string[]
     */
    private $specialHtmlCommentsStaringWith = [];

    /**
     * @var string[]
     */
    private $specialHtmlCommentsEndingWith = [];

    /**
     * @var bool
     */
    private $doSortCssClassNames = true;

    /**
     * @var bool
     */
    private $doSortHtmlAttributes = true;

    /**
     * @var bool
     */
    private $doRemoveDeprecatedScriptCharsetAttribute = true;

    /**
     * @var bool
     */
    private $doRemoveDefaultAttributes = false;

    /**
     * @var bool
     */
    private $doRemoveDeprecatedAnchorName = true;

    /**
     * @var bool
     */
    private $doRemoveDeprecatedTypeFromStylesheetLink = true;

    /**
     * @var bool
     */
    private $doRemoveDeprecatedTypeFromStyleAndLinkTag = true;

    /**
     * @var bool
     */
    private $doRemoveDefaultMediaTypeFromStyleAndLinkTag = true;

    /**
     * @var bool
     */
    private $doRemoveDefaultTypeFromButton = false;

    /**
     * @var bool
     */
    private $doRemoveDeprecatedTypeFromScriptTag = true;

    /**
     * @var bool
     */
    private $doRemoveValueFromEmptyInput = true;

    /**
     * @var bool
     */
    private $doRemoveEmptyAttributes = true;

    /**
     * @var bool
     */
    private $doSumUpWhitespace = true;

    /**
     * @var bool
     */
    private $doRemoveSpacesBetweenTags = false;

    /**
     * @var bool
     */
    private $keepBrokenHtml = false;

    /**
     * @var bool
     */
    private $withDocType = false;

    /**
     * @var HtmlMinDomObserverInterface[]|\SplObjectStorage
     *
     * @psalm-var \SplObjectStorage<HtmlMinDomObserverInterface>
     */
    private $domLoopObservers;

    /**
     * @var int
     */
    private $protected_tags_counter = 0;

    /**
     * @var bool
     */
    private $isHTML4 = false;

    /**
     * @var bool
     */
    private $isXHTML = false;

    /**
     * @var string[]|null
     */
    private $templateLogicSyntaxInSpecialScriptTags;

    /**
     * HtmlMin constructor.
     */
    public function __construct()
    {
        $this->domLoopObservers = new \SplObjectStorage();

        $this->attachObserverToTheDomLoop(new HtmlMinDomObserverOptimizeAttributes());
    }

    /**
     * @param HtmlMinDomObserverInterface $observer
     *
     * @return void
     */
    public function attachObserverToTheDomLoop(HtmlMinDomObserverInterface $observer)
    {
        $this->domLoopObservers->attach($observer);
    }

    /**
     * @param bool $doOptimizeAttributes
     *
     * @return $this
     */
    public function doOptimizeAttributes(bool $doOptimizeAttributes = true): self
    {
        $this->doOptimizeAttributes = $doOptimizeAttributes;

        return $this;
    }

    /**
     * @param bool $doOptimizeViaHtmlDomParser
     *
     * @return $this
     */
    public function doOptimizeViaHtmlDomParser(bool $doOptimizeViaHtmlDomParser = true): self
    {
        $this->doOptimizeViaHtmlDomParser = $doOptimizeViaHtmlDomParser;

        return $this;
    }

    /**
     * @param bool $doRemoveComments
     *
     * @return $this
     */
    public function doRemoveComments(bool $doRemoveComments = true): self
    {
        $this->doRemoveComments = $doRemoveComments;

        return $this;
    }

    /**
     * @param bool $doRemoveDefaultAttributes
     *
     * @return $this
     */
    public function doRemoveDefaultAttributes(bool $doRemoveDefaultAttributes = true): self
    {
        $this->doRemoveDefaultAttributes = $doRemoveDefaultAttributes;

        return $this;
    }

    /**
     * @param bool $doRemoveDeprecatedAnchorName
     *
     * @return $this
     */
    public function doRemoveDeprecatedAnchorName(bool $doRemoveDeprecatedAnchorName = true): self
    {
        $this->doRemoveDeprecatedAnchorName = $doRemoveDeprecatedAnchorName;

        return $this;
    }

    /**
     * @param bool $doRemoveDeprecatedScriptCharsetAttribute
     *
     * @return $this
     */
    public function doRemoveDeprecatedScriptCharsetAttribute(bool $doRemoveDeprecatedScriptCharsetAttribute = true): self
    {
        $this->doRemoveDeprecatedScriptCharsetAttribute = $doRemoveDeprecatedScriptCharsetAttribute;

        return $this;
    }

    /**
     * @param bool $doRemoveDeprecatedTypeFromScriptTag
     *
     * @return $this
     */
    public function doRemoveDeprecatedTypeFromScriptTag(bool $doRemoveDeprecatedTypeFromScriptTag = true): self
    {
        $this->doRemoveDeprecatedTypeFromScriptTag = $doRemoveDeprecatedTypeFromScriptTag;

        return $this;
    }

    /**
     * @param bool $doRemoveDeprecatedTypeFromStylesheetLink
     *
     * @return $this
     */
    public function doRemoveDeprecatedTypeFromStylesheetLink(bool $doRemoveDeprecatedTypeFromStylesheetLink = true): self
    {
        $this->doRemoveDeprecatedTypeFromStylesheetLink = $doRemoveDeprecatedTypeFromStylesheetLink;

        return $this;
    }

    /**
     * @param bool $doRemoveDeprecatedTypeFromStyleAndLinkTag
     *
     * @return $this
     */
    public function doRemoveDeprecatedTypeFromStyleAndLinkTag(bool $doRemoveDeprecatedTypeFromStyleAndLinkTag = true): self
    {
        $this->doRemoveDeprecatedTypeFromStyleAndLinkTag = $doRemoveDeprecatedTypeFromStyleAndLinkTag;

        return $this;
    }

    /**
     * @param bool $doRemoveDefaultMediaTypeFromStyleAndLinkTag
     *
     * @return $this
     */
    public function doRemoveDefaultMediaTypeFromStyleAndLinkTag(bool $doRemoveDefaultMediaTypeFromStyleAndLinkTag = true): self
    {
        $this->doRemoveDefaultMediaTypeFromStyleAndLinkTag = $doRemoveDefaultMediaTypeFromStyleAndLinkTag;

        return $this;
    }

    /**
     * @param bool $doRemoveDefaultTypeFromButton
     *
     * @return $this
     */
    public function doRemoveDefaultTypeFromButton(bool $doRemoveDefaultTypeFromButton = true): self
    {
        $this->doRemoveDefaultTypeFromButton = $doRemoveDefaultTypeFromButton;

        return $this;
    }

    /**
     * @param bool $doRemoveEmptyAttributes
     *
     * @return $this
     */
    public function doRemoveEmptyAttributes(bool $doRemoveEmptyAttributes = true): self
    {
        $this->doRemoveEmptyAttributes = $doRemoveEmptyAttributes;

        return $this;
    }

    /**
     * @param bool $doRemoveHttpPrefixFromAttributes
     *
     * @return $this
     */
    public function doRemoveHttpPrefixFromAttributes(bool $doRemoveHttpPrefixFromAttributes = true): self
    {
        $this->doRemoveHttpPrefixFromAttributes = $doRemoveHttpPrefixFromAttributes;

        return $this;
    }

    /**
     * @param bool $doRemoveHttpsPrefixFromAttributes
     *
     * @return $this
     */
    public function doRemoveHttpsPrefixFromAttributes(bool $doRemoveHttpsPrefixFromAttributes = true): self
    {
        $this->doRemoveHttpsPrefixFromAttributes = $doRemoveHttpsPrefixFromAttributes;

        return $this;
    }

    /**
     * @param bool $doKeepHttpAndHttpsPrefixOnExternalAttributes
     *
     * @return $this
     */
    public function doKeepHttpAndHttpsPrefixOnExternalAttributes(bool $doKeepHttpAndHttpsPrefixOnExternalAttributes = true): self
    {
        $this->doKeepHttpAndHttpsPrefixOnExternalAttributes = $doKeepHttpAndHttpsPrefixOnExternalAttributes;

        return $this;
    }

    /**
     * @param string[] $localDomains
     *
     * @return $this
     */
    public function doMakeSameDomainsLinksRelative(array $localDomains): self
    {
        /** @noinspection AlterInForeachInspection */
        foreach ($localDomains as &$localDomain) {
            $localDomain = \rtrim((string) \preg_replace('/(?:https?:)?\/\//i', '', $localDomain), '/');
        }

        $this->localDomains = $localDomains;
        $this->doMakeSameDomainsLinksRelative = \count($this->localDomains) > 0;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getLocalDomains(): array
    {
        return $this->localDomains;
    }

    /**
     * @param bool $doRemoveOmittedHtmlTags
     *
     * @return $this
     */
    public function doRemoveOmittedHtmlTags(bool $doRemoveOmittedHtmlTags = true): self
    {
        $this->doRemoveOmittedHtmlTags = $doRemoveOmittedHtmlTags;

        return $this;
    }

    /**
     * @param bool $doRemoveOmittedQuotes
     *
     * @return $this
     */
    public function doRemoveOmittedQuotes(bool $doRemoveOmittedQuotes = true): self
    {
        $this->doRemoveOmittedQuotes = $doRemoveOmittedQuotes;

        return $this;
    }

    /**
     * @param bool $doRemoveSpacesBetweenTags
     *
     * @return $this
     */
    public function doRemoveSpacesBetweenTags(bool $doRemoveSpacesBetweenTags = true): self
    {
        $this->doRemoveSpacesBetweenTags = $doRemoveSpacesBetweenTags;

        return $this;
    }

    /**
     * @param bool $doRemoveValueFromEmptyInput
     *
     * @return $this
     */
    public function doRemoveValueFromEmptyInput(bool $doRemoveValueFromEmptyInput = true): self
    {
        $this->doRemoveValueFromEmptyInput = $doRemoveValueFromEmptyInput;

        return $this;
    }

    /**
     * @param bool $doRemoveWhitespaceAroundTags
     *
     * @return $this
     */
    public function doRemoveWhitespaceAroundTags(bool $doRemoveWhitespaceAroundTags = true): self
    {
        $this->doRemoveWhitespaceAroundTags = $doRemoveWhitespaceAroundTags;

        return $this;
    }

    /**
     * @param bool $doSortCssClassNames
     *
     * @return $this
     */
    public function doSortCssClassNames(bool $doSortCssClassNames = true): self
    {
        $this->doSortCssClassNames = $doSortCssClassNames;

        return $this;
    }

    /**
     * @param bool $doSortHtmlAttributes
     *
     * @return $this
     */
    public function doSortHtmlAttributes(bool $doSortHtmlAttributes = true): self
    {
        $this->doSortHtmlAttributes = $doSortHtmlAttributes;

        return $this;
    }

    /**
     * @param bool $doSumUpWhitespace
     *
     * @return $this
     */
    public function doSumUpWhitespace(bool $doSumUpWhitespace = true): self
    {
        $this->doSumUpWhitespace = $doSumUpWhitespace;

        return $this;
    }

    private function domNodeAttributesToString(\DOMNode $node): string
    {
        // Remove quotes around attribute values, when allowed (<p class="foo"> → <p class=foo>)
        $attr_str = '';
        if ($node->attributes !== null) {
            foreach ($node->attributes as $attribute) {
                $attr_str .= $attribute->name;

                if (
                    $this->doOptimizeAttributes
                    &&
                    isset(self::$booleanAttributes[$attribute->name])
                ) {
                    $attr_str .= ' ';

                    continue;
                }

                $attr_str .= '=';

                // http://www.whatwg.org/specs/web-apps/current-work/multipage/syntax.html#attributes-0
                $omit_quotes = $this->doRemoveOmittedQuotes
                               &&
                               $attribute->value !== ''
                               &&
                               \strpos($attribute->name, '____SIMPLE_HTML_DOM__VOKU') !== 0
                               &&
                               \strpos($attribute->name, ' ') === false
                               &&
                               \preg_match('/["\'=<>` \t\r\n\f]/', $attribute->value) === 0;

                $quoteTmp = '"';
                if (
                    !$omit_quotes
                    &&
                    \strpos($attribute->value, '"') !== false
                ) {
                    $quoteTmp = "'";
                }

                if (
                    $this->doOptimizeAttributes
                    &&
                    (
                        $attribute->name === 'srcset'
                        ||
                        $attribute->name === 'sizes'
                    )
                ) {
                    $attr_val = \preg_replace(self::$regExSpace, ' ', $attribute->value);
                } else {
                    $attr_val = $attribute->value;
                }

                $attr_str .= ($omit_quotes ? '' : $quoteTmp) . $attr_val . ($omit_quotes ? '' : $quoteTmp);
                $attr_str .= ' ';
            }
        }

        return \trim($attr_str);
    }

    /**
     * @param \DOMNode $node
     *
     * @return bool
     */
    private function domNodeClosingTagOptional(\DOMNode $node): bool
    {
        $tag_name = $node->nodeName;

        /** @var \DOMNode|null $parent_node - false-positive error from phpstan */
        $parent_node = $node->parentNode;

        if ($parent_node) {
            $parent_tag_name = $parent_node->nodeName;
        } else {
            $parent_tag_name = null;
        }

        $nextSibling = $this->getNextSiblingOfTypeDOMElement($node);

        // https://html.spec.whatwg.org/multipage/syntax.html#syntax-tag-omission

        // Implemented:
        //
        // A <p> element's end tag may be omitted if the p element is immediately followed by an address, article, aside, blockquote, details, div, dl, fieldset, figcaption, figure, footer, form, h1, h2, h3, h4, h5, h6, header, hgroup, hr, main, menu, nav, ol, p, pre, section, table, or ul element, or if there is no more content in the parent element and the parent element is an HTML element that is not an a, audio, del, ins, map, noscript, or video element, or an autonomous custom element.
        // An <li> element's end tag may be omitted if the li element is immediately followed by another li element or if there is no more content in the parent element.
        // A <td> element's end tag may be omitted if the td element is immediately followed by a td or th element, or if there is no more content in the parent element.
        // An <option> element's end tag may be omitted if the option element is immediately followed by another option element, or if it is immediately followed by an optgroup element, or if there is no more content in the parent element.
        // A <tr> element's end tag may be omitted if the tr element is immediately followed by another tr element, or if there is no more content in the parent element.
        // A <th> element's end tag may be omitted if the th element is immediately followed by a td or th element, or if there is no more content in the parent element.
        // A <dt> element's end tag may be omitted if the dt element is immediately followed by another dt element or a dd element.
        // A <dd> element's end tag may be omitted if the dd element is immediately followed by another dd element or a dt element, or if there is no more content in the parent element.
        // An <rp> element's end tag may be omitted if the rp element is immediately followed by an rt or rp element, or if there is no more content in the parent element.
        // An <optgroup> element's end tag may be omitted if the optgroup element is immediately followed by another optgroup element, or if there is no more content in the parent element.

        /**
         * @noinspection TodoComment
         *
         * TODO: Not Implemented
         */
        //
        // <html> may be omitted if first thing inside is not comment
        // <head> may be omitted if first thing inside is an element
        // <body> may be omitted if first thing inside is not space, comment, <meta>, <link>, <script>, <style> or <template>
        // <colgroup> may be omitted if first thing inside is <col>
        // <tbody> may be omitted if first thing inside is <tr>
        // A <colgroup> element's start tag may be omitted if the first thing inside the colgroup element is a col element, and if the element is not immediately preceded by another colgroup element whose end tag has been omitted. (It can't be omitted if the element is empty.)
        // A <colgroup> element's end tag may be omitted if the colgroup element is not immediately followed by ASCII whitespace or a comment.
        // A <caption> element's end tag may be omitted if the caption element is not immediately followed by ASCII whitespace or a comment.
        // A <thead> element's end tag may be omitted if the thead element is immediately followed by a tbody or tfoot element.
        // A <tbody> element's start tag may be omitted if the first thing inside the tbody element is a tr element, and if the element is not immediately preceded by a tbody, thead, or tfoot element whose end tag has been omitted. (It can't be omitted if the element is empty.)
        // A <tbody> element's end tag may be omitted if the tbody element is immediately followed by a tbody or tfoot element, or if there is no more content in the parent element.
        // A <tfoot> element's end tag may be omitted if there is no more content in the parent element.
        //
        // <-- However, a start tag must never be omitted if it has any attributes.

        /** @noinspection InArrayCanBeUsedInspection */
        return \in_array($tag_name, self::$optional_end_tags, true)
               ||
               (
                   $tag_name === 'li'
                   &&
                   (
                       $nextSibling === null
                       ||
                       (
                           $nextSibling instanceof \DOMElement
                           &&
                           $nextSibling->tagName === 'li'
                       )
                   )
               )
               ||
               (
                   $tag_name === 'optgroup'
                   &&
                   (
                       $nextSibling === null
                       ||
                       (
                           $nextSibling instanceof \DOMElement
                           &&
                           $nextSibling->tagName === 'optgroup'
                       )
                   )
               )
               ||
               (
                   $tag_name === 'rp'
                   &&
                   (
                       $nextSibling === null
                       ||
                       (
                           $nextSibling instanceof \DOMElement
                           &&
                           (
                               $nextSibling->tagName === 'rp'
                               ||
                               $nextSibling->tagName === 'rt'
                           )
                       )
                   )
               )
               ||
               (
                   $tag_name === 'tr'
                   &&
                   (
                       $nextSibling === null
                       ||
                       (
                           $nextSibling instanceof \DOMElement
                           &&
                           $nextSibling->tagName === 'tr'
                       )
                   )
               )
               ||
               (
                   $tag_name === 'source'
                   &&
                   (
                       $parent_tag_name === 'audio'
                       ||
                       $parent_tag_name === 'video'
                       ||
                       $parent_tag_name === 'picture'
                       ||
                       $parent_tag_name === 'source'
                   )
                   &&
                   (
                       $nextSibling === null
                       ||
                       (
                           $nextSibling instanceof \DOMElement
                           &&
                           $nextSibling->tagName === 'source'
                       )
                   )
               )
               ||
               (
                   (
                       $tag_name === 'td'
                       ||
                       $tag_name === 'th'
                   )
                   &&
                   (
                       $nextSibling === null
                       ||
                       (
                           $nextSibling instanceof \DOMElement
                           &&
                           (
                               $nextSibling->tagName === 'td'
                               ||
                               $nextSibling->tagName === 'th'
                           )
                       )
                   )
               )
               ||
               (
                   (
                       $tag_name === 'dd'
                       ||
                       $tag_name === 'dt'
                   )
                   &&
                   (
                       $nextSibling === null
                       ||
                       (
                           $nextSibling instanceof \DOMElement
                           &&
                           (
                               $nextSibling->tagName === 'dd'
                               ||
                               $nextSibling->tagName === 'dt'
                           )
                       )
                   )
               )
               ||
               (
                   $tag_name === 'option'
                   &&
                   (
                       $nextSibling === null
                       ||
                       (
                           $nextSibling instanceof \DOMElement
                           &&
                           (
                               $nextSibling->tagName === 'option'
                               ||
                               $nextSibling->tagName === 'optgroup'
                           )
                       )
                   )
               )
               ||
               (
                   $tag_name === 'p'
                   &&
                   (
                       (
                           $nextSibling === null
                           &&
                           $node->parentNode !== null
                           &&
                           !\in_array(
                               $node->parentNode->nodeName,
                               [
                                   'a',
                                   'audio',
                                   'del',
                                   'ins',
                                   'map',
                                   'noscript',
                                   'video',
                               ],
                               true
                           )
                       )
                       ||
                       (
                           $nextSibling instanceof \DOMElement
                           &&
                           \in_array(
                               $nextSibling->tagName,
                               [
                                   'address',
                                   'article',
                                   'aside',
                                   'blockquote',
                                   'dir',
                                   'div',
                                   'dl',
                                   'fieldset',
                                   'footer',
                                   'form',
                                   'h1',
                                   'h2',
                                   'h3',
                                   'h4',
                                   'h5',
                                   'h6',
                                   'header',
                                   'hgroup',
                                   'hr',
                                   'menu',
                                   'nav',
                                   'ol',
                                   'p',
                                   'pre',
                                   'section',
                                   'table',
                                   'ul',
                               ],
                               true
                           )
                       )
                   )
               );
    }

    protected function domNodeToString(\DOMNode $node): string
    {
        // init
        $html = '';
        $emptyStringTmp = '';

        foreach ($node->childNodes as $child) {
            if ($emptyStringTmp === 'is_empty') {
                $emptyStringTmp = 'last_was_empty';
            } else {
                $emptyStringTmp = '';
            }

            if ($child instanceof \DOMElement) {
                $html .= \rtrim('<' . $child->tagName . ' ' . $this->domNodeAttributesToString($child));
                $html .= '>' . $this->domNodeToString($child);

                if (
                    !(
                        $this->doRemoveOmittedHtmlTags
                        &&
                        !$this->isHTML4
                        &&
                        !$this->isXHTML
                        &&
                        $this->domNodeClosingTagOptional($child)
                    )
                ) {
                    $html .= '</' . $child->tagName . '>';
                }

                if (!$this->doRemoveWhitespaceAroundTags) {
                    /** @var \DOMText|null $nextSiblingTmp - false-positive error from phpstan */
                    $nextSiblingTmp = $child->nextSibling;
                    if (
                        $nextSiblingTmp instanceof \DOMText
                        &&
                        $nextSiblingTmp->wholeText === ' '
                    ) {
                        if (
                            $emptyStringTmp !== 'last_was_empty'
                            &&
                            \substr($html, -1) !== ' '
                        ) {
                            $html = \rtrim($html);

                            if (
                                $child->parentNode
                                &&
                                $child->parentNode->nodeName !== 'head'
                            ) {
                                $html .= ' ';
                            }
                        }
                        $emptyStringTmp = 'is_empty';
                    }
                }
            } elseif ($child instanceof \DOMText) {
                if ($child->isElementContentWhitespace()) {
                    if (
                        $child->previousSibling !== null
                        &&
                        $child->nextSibling !== null
                    ) {
                        if (
                            (
                                $child->wholeText
                                &&
                                \strpos($child->wholeText, ' ') !== false
                            )
                            ||
                            (
                                $emptyStringTmp !== 'last_was_empty'
                                &&
                                \substr($html, -1) !== ' '
                            )
                        ) {
                            $html = \rtrim($html);

                            if (
                                $child->parentNode
                                &&
                                $child->parentNode->nodeName !== 'head'
                            ) {
                                $html .= ' ';
                            }
                        }
                        $emptyStringTmp = 'is_empty';
                    }
                } else {
                    $html .= $child->wholeText;
                }
            } elseif ($child instanceof \DOMComment) {
                $html .= '<!--' . $child->textContent . '-->';
            }
        }

        return $html;
    }

    /**
     * @param \DOMNode $node
     *
     * @return string
     */
    private function getDoctype(\DOMNode $node): string
    {
        // check the doc-type only if it wasn't generated by DomDocument itself
        if (!$this->withDocType) {
            return '';
        }

        foreach ($node->childNodes as $child) {
            if (
                $child instanceof \DOMDocumentType
                &&
                $child->name
            ) {
                if (!$child->publicId && $child->systemId) {
                    $tmpTypeSystem = 'SYSTEM';
                    $tmpTypePublic = '';
                } else {
                    $tmpTypeSystem = '';
                    $tmpTypePublic = 'PUBLIC';
                }

                return '<!DOCTYPE ' . $child->name . ''
                       . ($child->publicId ? ' ' . $tmpTypePublic . ' "' . $child->publicId . '"' : '')
                       . ($child->systemId ? ' ' . $tmpTypeSystem . ' "' . $child->systemId . '"' : '')
                       . '>';
            }
        }

        return '';
    }

    /**
     * @return array
     */
    public function getDomainsToRemoveHttpPrefixFromAttributes(): array
    {
        return $this->domainsToRemoveHttpPrefixFromAttributes;
    }

    /**
     * @return bool
     */
    public function isDoOptimizeAttributes(): bool
    {
        return $this->doOptimizeAttributes;
    }

    /**
     * @return bool
     */
    public function isDoOptimizeViaHtmlDomParser(): bool
    {
        return $this->doOptimizeViaHtmlDomParser;
    }

    /**
     * @return bool
     */
    public function isDoRemoveComments(): bool
    {
        return $this->doRemoveComments;
    }

    /**
     * @return bool
     */
    public function isDoRemoveDefaultAttributes(): bool
    {
        return $this->doRemoveDefaultAttributes;
    }

    /**
     * @return bool
     */
    public function isDoRemoveDeprecatedAnchorName(): bool
    {
        return $this->doRemoveDeprecatedAnchorName;
    }

    /**
     * @return bool
     */
    public function isDoRemoveDeprecatedScriptCharsetAttribute(): bool
    {
        return $this->doRemoveDeprecatedScriptCharsetAttribute;
    }

    /**
     * @return bool
     */
    public function isDoRemoveDeprecatedTypeFromScriptTag(): bool
    {
        return $this->doRemoveDeprecatedTypeFromScriptTag;
    }

    /**
     * @return bool
     */
    public function isDoRemoveDeprecatedTypeFromStylesheetLink(): bool
    {
        return $this->doRemoveDeprecatedTypeFromStylesheetLink;
    }

    /**
     * @return bool
     */
    public function isDoRemoveDeprecatedTypeFromStyleAndLinkTag(): bool
    {
        return $this->doRemoveDeprecatedTypeFromStyleAndLinkTag;
    }

    /**
     * @return bool
     */
    public function isDoRemoveDefaultMediaTypeFromStyleAndLinkTag(): bool
    {
        return $this->doRemoveDefaultMediaTypeFromStyleAndLinkTag;
    }

    /**
     * @return bool
     */
    public function isDoRemoveDefaultTypeFromButton(): bool
    {
        return $this->doRemoveDefaultTypeFromButton;
    }

    /**
     * @return bool
     */
    public function isDoRemoveEmptyAttributes(): bool
    {
        return $this->doRemoveEmptyAttributes;
    }

    /**
     * @return bool
     */
    public function isDoRemoveHttpPrefixFromAttributes(): bool
    {
        return $this->doRemoveHttpPrefixFromAttributes;
    }

    /**
     * @return bool
     */
    public function isDoRemoveHttpsPrefixFromAttributes(): bool
    {
        return $this->doRemoveHttpsPrefixFromAttributes;
    }

    /**
     * @return bool
     */
    public function isdoKeepHttpAndHttpsPrefixOnExternalAttributes(): bool
    {
        return $this->doKeepHttpAndHttpsPrefixOnExternalAttributes;
    }

    /**
     * @return bool
     */
    public function isDoMakeSameDomainsLinksRelative(): bool
    {
        return $this->doMakeSameDomainsLinksRelative;
    }

    /**
     * @return bool
     */
    public function isDoRemoveOmittedHtmlTags(): bool
    {
        return $this->doRemoveOmittedHtmlTags;
    }

    /**
     * @return bool
     */
    public function isDoRemoveOmittedQuotes(): bool
    {
        return $this->doRemoveOmittedQuotes;
    }

    /**
     * @return bool
     */
    public function isDoRemoveSpacesBetweenTags(): bool
    {
        return $this->doRemoveSpacesBetweenTags;
    }

    /**
     * @return bool
     */
    public function isDoRemoveValueFromEmptyInput(): bool
    {
        return $this->doRemoveValueFromEmptyInput;
    }

    /**
     * @return bool
     */
    public function isDoRemoveWhitespaceAroundTags(): bool
    {
        return $this->doRemoveWhitespaceAroundTags;
    }

    /**
     * @return bool
     */
    public function isDoSortCssClassNames(): bool
    {
        return $this->doSortCssClassNames;
    }

    /**
     * @return bool
     */
    public function isDoSortHtmlAttributes(): bool
    {
        return $this->doSortHtmlAttributes;
    }

    /**
     * @return bool
     */
    public function isDoSumUpWhitespace(): bool
    {
        return $this->doSumUpWhitespace;
    }

    /**
     * @return bool
     */
    public function isHTML4(): bool
    {
        return $this->isHTML4;
    }

    /**
     * @return bool
     */
    public function isXHTML(): bool
    {
        return $this->isXHTML;
    }

    /**
     * @param string $html
     * @param bool   $multiDecodeNewHtmlEntity
     *
     * @return string
     */
    public function minify($html, $multiDecodeNewHtmlEntity = false): string
    {
        $html = (string) $html;
        if (!isset($html[0])) {
            return '';
        }

        $html = \trim($html);
        if (!$html) {
            return '';
        }

        // reset
        $this->protectedChildNodes = [];

        // save old content
        $origHtml = $html;
        $origHtmlLength = \strlen($html);

        // -------------------------------------------------------------------------
        // Minify the HTML via "HtmlDomParser"
        // -------------------------------------------------------------------------

        if ($this->doOptimizeViaHtmlDomParser) {
            $html = $this->minifyHtmlDom($html, $multiDecodeNewHtmlEntity);
        }

        // -------------------------------------------------------------------------
        // Trim whitespace from html-string. [protected html is still protected]
        // -------------------------------------------------------------------------

        // Remove extra white-space(s) between HTML attribute(s)
        if (\strpos($html, ' ') !== false) {
            $html = (string) \preg_replace_callback(
                '#<([^/\s<>!]+)(?:\s+([^<>]*?)\s*|\s*)(/?)>#',
                static function ($matches) {
                    return '<' . $matches[1] . \preg_replace('#([^\s=]+)(=([\'"]?)(.*?)\3)?(\s+|$)#su', ' $1$2', $matches[2]) . $matches[3] . '>';
                },
                $html
            );
        }

        if ($this->doRemoveSpacesBetweenTags) {
            /** @noinspection NestedPositiveIfStatementsInspection */
            if (\strpos($html, ' ') !== false) {
                // Remove spaces that are between > and <
                $html = (string) \preg_replace('#(>)\s(<)#', '>$2', $html);
            }
        }

        // -------------------------------------------------------------------------
        // Restore protected HTML-code.
        // -------------------------------------------------------------------------

        if (\strpos($html, $this->protectedChildNodesHelper) !== false) {
            $html = (string) \preg_replace_callback(
                '/<(?<element>' . $this->protectedChildNodesHelper . ')(?<attributes> [^>]*)?>(?<value>.*?)<\/' . $this->protectedChildNodesHelper . '>/',
                [$this, 'restoreProtectedHtml'],
                $html
            );
        }

        // -------------------------------------------------------------------------
        // Restore protected HTML-entities.
        // -------------------------------------------------------------------------

        if ($this->doOptimizeViaHtmlDomParser) {
            $html = HtmlDomParser::putReplacedBackToPreserveHtmlEntities($html);
        }

        // ------------------------------------
        // Final clean-up
        // ------------------------------------

        $html = \str_replace(
            [
                'html>' . "\n",
                "\n" . '<html',
                'html/>' . "\n",
                "\n" . '</html',
                'head>' . "\n",
                "\n" . '<head',
                'head/>' . "\n",
                "\n" . '</head',
            ],
            [
                'html>',
                '<html',
                'html/>',
                '</html',
                'head>',
                '<head',
                'head/>',
                '</head',
            ],
            $html
        );

        // self closing tags, don't need a trailing slash ...
        $replace = [];
        $replacement = [];
        foreach (self::$selfClosingTags as $selfClosingTag) {
            $replace[] = '<' . $selfClosingTag . '/>';
            $replacement[] = '<' . $selfClosingTag . '>';
            $replace[] = '<' . $selfClosingTag . ' />';
            $replacement[] = '<' . $selfClosingTag . '>';
            $replace[] = '></' . $selfClosingTag . '>';
            $replacement[] = '>';
        }
        $html = \str_replace(
            $replace,
            $replacement,
            $html
        );

        // ------------------------------------
        // check if compression worked
        // ------------------------------------

        if ($origHtmlLength < \strlen($html)) {
            $html = $origHtml;
        }

        return $html;
    }

    /**
     * @param \DOMNode $node
     *
     * @return \DOMNode|null
     */
    protected function getNextSiblingOfTypeDOMElement(\DOMNode $node)
    {
        do {
            /** @var \DOMElement|\DOMText|null $nodeTmp - false-positive error from phpstan */
            $nodeTmp = $node->nextSibling;

            if ($nodeTmp instanceof \DOMText) {
                if (
                    \trim($nodeTmp->textContent) !== ''
                    &&
                    \strpos($nodeTmp->textContent, '<') === false
                ) {
                    $node = $nodeTmp;
                } else {
                    $node = $nodeTmp->nextSibling;
                }
            } else {
                $node = $nodeTmp;
            }
        } while (!($node === null || $node instanceof \DOMElement || $node instanceof \DOMText));

        return $node;
    }

    /**
     * Check if the current string is an conditional comment.
     *
     * INFO: since IE >= 10 conditional comment are not working anymore
     *
     * <!--[if expression]> HTML <![endif]-->
     * <![if expression]> HTML <![endif]>
     *
     * @param string $comment
     *
     * @return bool
     */
    private function isConditionalComment($comment): bool
    {
        if (\strpos($comment, '[if ') !== false) {
            /** @noinspection RegExpRedundantEscape */
            /** @noinspection NestedPositiveIfStatementsInspection */
            if (\preg_match('/^\[if [^\]]+\]/', $comment)) {
                return true;
            }
        }

        if (\strpos($comment, '[endif]') !== false) {
            /** @noinspection RegExpRedundantEscape */
            /** @noinspection NestedPositiveIfStatementsInspection */
            if (\preg_match('/\[endif\]$/', $comment)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the current string is an special comment.
     *
     * @param string $comment
     *
     * @return bool
     */
    private function isSpecialComment($comment): bool
    {
        foreach ($this->specialHtmlCommentsStaringWith as $search) {
            if (\strpos($comment, $search) === 0) {
                return true;
            }
        }

        foreach ($this->specialHtmlCommentsEndingWith as $search) {
            if (\substr($comment, -\strlen($search)) === $search) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $html
     * @param bool   $multiDecodeNewHtmlEntity
     *
     * @return string
     */
    private function minifyHtmlDom($html, $multiDecodeNewHtmlEntity): string
    {
        // init dom
        $dom = new HtmlDomParser();
        $dom->useKeepBrokenHtml($this->keepBrokenHtml);

        if ($this->templateLogicSyntaxInSpecialScriptTags !== null) {
            $dom->overwriteTemplateLogicSyntaxInSpecialScriptTags($this->templateLogicSyntaxInSpecialScriptTags);
        }

        $dom->getDocument()->preserveWhiteSpace = false; // remove redundant white space
        $dom->getDocument()->formatOutput = false; // do not formats output with indentation

        // Remove content before <!DOCTYPE.*> because otherwise the DOMDocument can not handle the input.
        if (\stripos($html, '<!DOCTYPE') !== false) {
            /** @noinspection NestedPositiveIfStatementsInspection */
            if (
                \preg_match('/(^.*?)<!(?:DOCTYPE)(?: [^>]*)?>/sui', $html, $matches_before_doctype)
                &&
                \trim($matches_before_doctype[1])
            ) {
                $html = \str_replace($matches_before_doctype[1], '', $html);
            }
        }

        // load dom
        /** @noinspection UnusedFunctionResultInspection */
        $dom->loadHtml($html);

        $this->withDocType = (\stripos($html, '<!DOCTYPE') === 0);

        $doctypeStr = $this->getDoctype($dom->getDocument());

        if ($doctypeStr) {
            $this->isHTML4 = \strpos($doctypeStr, 'html4') !== false;
            $this->isXHTML = \strpos($doctypeStr, 'xhtml1') !== false;
        }

        // -------------------------------------------------------------------------
        // Protect <nocompress> HTML tags first.
        // -------------------------------------------------------------------------

        $dom = $this->protectTagHelper($dom, 'nocompress');

        // -------------------------------------------------------------------------
        // Notify the Observer before the minification.
        // -------------------------------------------------------------------------

        foreach ($dom->find('*') as $element) {
            $this->notifyObserversAboutDomElementBeforeMinification($element);
        }

        // -------------------------------------------------------------------------
        // Protect HTML tags and conditional comments.
        // -------------------------------------------------------------------------

        $dom = $this->protectTags($dom);

        // -------------------------------------------------------------------------
        // Remove default HTML comments. [protected html is still protected]
        // -------------------------------------------------------------------------

        if ($this->doRemoveComments) {
            $dom = $this->removeComments($dom);
        }

        // -------------------------------------------------------------------------
        // Sum-Up extra whitespace from the Dom. [protected html is still protected]
        // -------------------------------------------------------------------------

        if ($this->doSumUpWhitespace) {
            $dom = $this->sumUpWhitespace($dom);
        }

        foreach ($dom->find('*') as $element) {

            // -------------------------------------------------------------------------
            // Remove whitespace around tags. [protected html is still protected]
            // -------------------------------------------------------------------------

            if ($this->doRemoveWhitespaceAroundTags) {
                $this->removeWhitespaceAroundTags($element);
            }

            // -------------------------------------------------------------------------
            // Notify the Observer after the minification.
            // -------------------------------------------------------------------------

            $this->notifyObserversAboutDomElementAfterMinification($element);
        }

        // -------------------------------------------------------------------------
        // Convert the Dom into a string.
        // -------------------------------------------------------------------------

        return $dom->fixHtmlOutput(
            $doctypeStr . $this->domNodeToString($dom->getDocument()),
            $multiDecodeNewHtmlEntity
        );
    }

    /**
     * @param SimpleHtmlDomInterface $domElement
     *
     * @return void
     */
    private function notifyObserversAboutDomElementAfterMinification(SimpleHtmlDomInterface $domElement)
    {
        foreach ($this->domLoopObservers as $observer) {
            $observer->domElementAfterMinification($domElement, $this);
        }
    }

    /**
     * @param SimpleHtmlDomInterface $domElement
     *
     * @return void
     */
    private function notifyObserversAboutDomElementBeforeMinification(SimpleHtmlDomInterface $domElement)
    {
        foreach ($this->domLoopObservers as $observer) {
            $observer->domElementBeforeMinification($domElement, $this);
        }
    }

    /**
     * @param HtmlDomParser $dom
     * @param string        $selector
     *
     * @return HtmlDomParser
     */
    private function protectTagHelper(HtmlDomParser $dom, string $selector): HtmlDomParser
    {
        foreach ($dom->find($selector) as $element) {
            if ($element->isRemoved()) {
                continue;
            }

            $this->protectedChildNodes[$this->protected_tags_counter] = $element->parentNode()->innerHtml();
            $parentNode = $element->getNode()->parentNode;
            if ($parentNode !== null) {
                $parentNode->nodeValue = '<' . $this->protectedChildNodesHelper . ' data-' . $this->protectedChildNodesHelper . '="' . $this->protected_tags_counter . '"></' . $this->protectedChildNodesHelper . '>';
            }

            ++$this->protected_tags_counter;
        }

        return $dom;
    }

    /**
     * Prevent changes of inline "styles" and "scripts".
     *
     * @param HtmlDomParser $dom
     *
     * @return HtmlDomParser
     */
    private function protectTags(HtmlDomParser $dom): HtmlDomParser
    {
        $this->protectTagHelper($dom, 'code');

        foreach ($dom->find('script, style') as $element) {
            if ($element->isRemoved()) {
                continue;
            }

            if ($element->tag === 'script' || $element->tag === 'style') {
                $attributes = $element->getAllAttributes();
                // skip external links
                if (isset($attributes['src'])) {
                    continue;
                }
            }

            $this->protectedChildNodes[$this->protected_tags_counter] = $element->innerhtml;
            $element->getNode()->nodeValue = '<' . $this->protectedChildNodesHelper . ' data-' . $this->protectedChildNodesHelper . '="' . $this->protected_tags_counter . '"></' . $this->protectedChildNodesHelper . '>';

            ++$this->protected_tags_counter;
        }

        foreach ($dom->find('//comment()') as $element) {
            if ($element->isRemoved()) {
                continue;
            }

            $text = $element->text();

            if (
                !$this->isConditionalComment($text)
                &&
                !$this->isSpecialComment($text)
            ) {
                continue;
            }

            $this->protectedChildNodes[$this->protected_tags_counter] = '<!--' . $text . '-->';

            /* @var $node \DOMComment */
            $node = $element->getNode();
            $child = new \DOMText('<' . $this->protectedChildNodesHelper . ' data-' . $this->protectedChildNodesHelper . '="' . $this->protected_tags_counter . '"></' . $this->protectedChildNodesHelper . '>');
            $parentNode = $element->getNode()->parentNode;
            if ($parentNode !== null) {
                $parentNode->replaceChild($child, $node);
            }

            ++$this->protected_tags_counter;
        }

        return $dom;
    }

    /**
     * Remove comments in the dom.
     *
     * @param HtmlDomParser $dom
     *
     * @return HtmlDomParser
     */
    private function removeComments(HtmlDomParser $dom): HtmlDomParser
    {
        foreach ($dom->find('//comment()') as $commentWrapper) {
            $comment = $commentWrapper->getNode();
            $val = $comment->nodeValue;
            if (\strpos($val, '[') === false) {
                $parentNode = $comment->parentNode;
                if ($parentNode !== null) {
                    $parentNode->removeChild($comment);
                }
            }
        }

        $dom->getDocument()->normalizeDocument();

        return $dom;
    }

    /**
     * Trim tags in the dom.
     *
     * @param SimpleHtmlDomInterface $element
     *
     * @return void
     */
    private function removeWhitespaceAroundTags(SimpleHtmlDomInterface $element)
    {
        if (isset(self::$trimWhitespaceFromTags[$element->tag])) {
            $node = $element->getNode();

            /** @var \DOMNode[] $candidates */
            $candidates = [];
            if ($node->childNodes->length > 0) {
                $candidates[] = $node->firstChild;
                $candidates[] = $node->lastChild;
                $candidates[] = $node->previousSibling;
                $candidates[] = $node->nextSibling;
            }

            /** @var mixed $candidate - false-positive error from phpstan */
            foreach ($candidates as &$candidate) {
                if ($candidate === null) {
                    continue;
                }

                if ($candidate->nodeType === \XML_TEXT_NODE) {
                    $nodeValueTmp = \preg_replace(self::$regExSpace, ' ', $candidate->nodeValue);
                    if ($nodeValueTmp !== null) {
                        $candidate->nodeValue = $nodeValueTmp;
                    }
                }
            }
        }
    }

    /**
     * Callback function for preg_replace_callback use.
     *
     * @param array $matches PREG matches
     *
     * @return string
     */
    private function restoreProtectedHtml($matches): string
    {
        \preg_match('/.*"(?<id>\d*)"/', $matches['attributes'], $matchesInner);

        return $this->protectedChildNodes[$matchesInner['id']] ?? '';
    }

    /**
     * @param string[] $domainsToRemoveHttpPrefixFromAttributes
     *
     * @return $this
     */
    public function setDomainsToRemoveHttpPrefixFromAttributes($domainsToRemoveHttpPrefixFromAttributes): self
    {
        $this->domainsToRemoveHttpPrefixFromAttributes = $domainsToRemoveHttpPrefixFromAttributes;

        return $this;
    }

    /**
     * @param string[] $startingWith
     * @param string[] $endingWith
     *
     * @return $this
     */
    public function setSpecialHtmlComments(array $startingWith, array $endingWith = []): self
    {
        $this->specialHtmlCommentsStaringWith = $startingWith;
        $this->specialHtmlCommentsEndingWith = $endingWith;

        return $this;
    }

    /**
     * Sum-up extra whitespace from dom-nodes.
     *
     * @param HtmlDomParser $dom
     *
     * @return HtmlDomParser
     */
    private function sumUpWhitespace(HtmlDomParser $dom): HtmlDomParser
    {
        $text_nodes = $dom->find('//text()');
        foreach ($text_nodes as $text_node_wrapper) {
            /* @var $text_node \DOMNode */
            $text_node = $text_node_wrapper->getNode();
            $xp = $text_node->getNodePath();
            if ($xp === null) {
                continue;
            }

            $doSkip = false;
            foreach (self::$skipTagsForRemoveWhitespace as $pattern) {
                if (\strpos($xp, "/${pattern}") !== false) {
                    $doSkip = true;

                    break;
                }
            }
            if ($doSkip) {
                continue;
            }

            $nodeValueTmp = \preg_replace(self::$regExSpace, ' ', $text_node->nodeValue);
            if ($nodeValueTmp !== null) {
                $text_node->nodeValue = $nodeValueTmp;
            }
        }

        $dom->getDocument()->normalizeDocument();

        return $dom;
    }

    /**
     * WARNING: maybe bad for performance ...
     *
     * @param bool $keepBrokenHtml
     *
     * @return HtmlMin
     */
    public function useKeepBrokenHtml(bool $keepBrokenHtml): self
    {
        $this->keepBrokenHtml = $keepBrokenHtml;

        return $this;
    }

    /**
     * @param string[] $templateLogicSyntaxInSpecialScriptTags
     *
     * @return HtmlMin
     */
    public function overwriteTemplateLogicSyntaxInSpecialScriptTags(array $templateLogicSyntaxInSpecialScriptTags): self
    {
        foreach ($templateLogicSyntaxInSpecialScriptTags as $tmp) {
            if (!\is_string($tmp)) {
                throw new \InvalidArgumentException('setTemplateLogicSyntaxInSpecialScriptTags only allows string[]');
            }
        }

        $this->templateLogicSyntaxInSpecialScriptTags = $templateLogicSyntaxInSpecialScriptTags;

        return $this;
    }
}
