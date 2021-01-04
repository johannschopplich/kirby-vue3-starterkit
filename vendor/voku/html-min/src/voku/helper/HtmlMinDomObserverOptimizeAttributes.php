<?php

declare(strict_types=1);

namespace voku\helper;

/**
 * HtmlMinDomObserverOptimizeAttributes: Optimize html attributes. [protected html is still protected]
 *
 * Sort HTML-Attributes, so that gzip can do better work and remove some default attributes...
 */
final class HtmlMinDomObserverOptimizeAttributes implements HtmlMinDomObserverInterface
{
    /**
     * // https://mathiasbynens.be/demo/javascript-mime-type
     * // https://developer.mozilla.org/en/docs/Web/HTML/Element/script#attr-type
     *
     * @var string[]
     *
     * @psalm-var array<string, string>
     */
    private static $executableScriptsMimeTypes = [
        'text/javascript'          => '',
        'text/ecmascript'          => '',
        'text/jscript'             => '',
        'application/javascript'   => '',
        'application/x-javascript' => '',
        'application/ecmascript'   => '',
    ];

    /**
     * Receive dom elements before the minification.
     *
     * @param SimpleHtmlDomInterface $element
     * @param HtmlMinInterface       $htmlMin
     *
     * @return void
     */
    public function domElementBeforeMinification(SimpleHtmlDomInterface $element, HtmlMinInterface $htmlMin)
    {
    }

    /**
     * Receive dom elements after the minification.
     *
     * @param SimpleHtmlDomInterface $element
     * @param HtmlMinInterface       $htmlMin
     *
     * @return void
     */
    public function domElementAfterMinification(SimpleHtmlDomInterface $element, HtmlMinInterface $htmlMin)
    {
        $attributes = $element->getAllAttributes();
        if ($attributes === null) {
            return;
        }

        $tagName = $element->getNode()->nodeName;
        $attrs = [];
        foreach ((array) $attributes as $attrName => $attrValue) {

            // -------------------------------------------------------------------------
            // Remove local domains from attributes.
            // -------------------------------------------------------------------------

            if ($htmlMin->isDoMakeSameDomainsLinksRelative()) {
                $localDomains = $htmlMin->getLocalDomains();
                foreach ($localDomains as $localDomain) {
                    /** @noinspection InArrayCanBeUsedInspection */
                    if (
                        (
                            $attrName === 'href'
                            ||
                            $attrName === 'src'
                            ||
                            $attrName === 'srcset'
                            ||
                            $attrName === 'action'
                        )
                        &&
                        !(isset($attributes['rel']) && $attributes['rel'] === 'external')
                        &&
                        !(isset($attributes['target']) && $attributes['target'] === '_blank')
                        &&
                        \stripos($attrValue, $localDomain) !== false
                    ) {
                        $localDomainEscaped = \preg_quote($localDomain, '/');

                        $attrValue = (string) \preg_replace("/^(?:(?:https?:)?\/\/)?{$localDomainEscaped}(?!\w)(?:\/?)/i", '/', $attrValue);
                    }
                }
            }

            // -------------------------------------------------------------------------
            // Remove optional "http:"-prefix from attributes.
            // -------------------------------------------------------------------------

            if ($htmlMin->isDoRemoveHttpPrefixFromAttributes()) {
                $attrValue = $this->removeUrlSchemeHelper(
                    $attrValue,
                    $attrName,
                    'http',
                    $attributes,
                    $tagName,
                    $htmlMin
                );
            }

            if ($htmlMin->isDoRemoveHttpsPrefixFromAttributes()) {
                $attrValue = $this->removeUrlSchemeHelper(
                    $attrValue,
                    $attrName,
                    'https',
                    $attributes,
                    $tagName,
                    $htmlMin
                );
            }

            // -------------------------------------------------------------------------
            // Remove some special attributes.
            // -------------------------------------------------------------------------

            if ($this->removeAttributeHelper(
                $element->tag,
                $attrName,
                $attrValue,
                $attributes,
                $htmlMin
            )) {
                $element->{$attrName} = null;

                continue;
            }

            // -------------------------------------------------------------------------
            // Sort css-class-names, for better gzip results.
            // -------------------------------------------------------------------------

            if ($htmlMin->isDoSortCssClassNames()) {
                $attrValue = $this->sortCssClassNames($attrName, $attrValue);
            }

            if ($htmlMin->isDoSortHtmlAttributes()) {
                $attrs[$attrName] = $attrValue;
                $element->{$attrName} = null;
            }
        }

        // -------------------------------------------------------------------------
        // Sort html-attributes, for better gzip results.
        // -------------------------------------------------------------------------

        if ($htmlMin->isDoSortHtmlAttributes()) {
            \ksort($attrs);
            foreach ($attrs as $attrName => $attrValue) {
                $attrValue = HtmlDomParser::replaceToPreserveHtmlEntities($attrValue);
                $element->setAttribute((string) $attrName, $attrValue, true);
            }
        }
    }

    /**
     * Check if the attribute can be removed.
     *
     * @param string           $tag
     * @param string           $attrName
     * @param string           $attrValue
     * @param array            $allAttr
     * @param HtmlMinInterface $htmlMin
     *
     * @return bool
     */
    private function removeAttributeHelper($tag, $attrName, $attrValue, $allAttr, HtmlMinInterface $htmlMin): bool
    {
        // remove defaults
        if ($htmlMin->isDoRemoveDefaultAttributes()) {
            if ($tag === 'script' && $attrName === 'language' && $attrValue === 'javascript') {
                return true;
            }

            if ($tag === 'form' && $attrName === 'method' && $attrValue === 'get') {
                return true;
            }

            if ($tag === 'form' && $attrName === 'autocomplete' && $attrValue === 'on') {
                return true;
            }

            if ($tag === 'form' && $attrName === 'enctype' && $attrValue === 'application/x-www-form-urlencoded') {
                return true;
            }

            if ($tag === 'input' && $attrName === 'type' && $attrValue === 'text') {
                return true;
            }

            if ($tag === 'textarea' && $attrName === 'wrap' && $attrValue === 'soft') {
                return true;
            }

            if ($tag === 'area' && $attrName === 'shape' && $attrValue === 'rect') {
                return true;
            }

            if ($tag === 'th' && $attrName === 'scope' && $attrValue === 'auto') {
                return true;
            }

            if ($tag === 'ol' && $attrName === 'type' && $attrValue === 'decimal') {
                return true;
            }

            if ($tag === 'ol' && $attrName === 'start' && $attrValue === '1') {
                return true;
            }

            if ($tag === 'track' && $attrName === 'kind' && $attrValue === 'subtitles') {
                return true;
            }

            if ($attrName === 'spellcheck' && $attrValue === 'default') {
                return true;
            }

            if ($attrName === 'draggable' && $attrValue === 'auto') {
                return true;
            }
        }

        // remove deprecated charset-attribute (the browser will use the charset from the HTTP-Header, anyway)
        if ($htmlMin->isDoRemoveDeprecatedScriptCharsetAttribute()) {
            /** @noinspection NestedPositiveIfStatementsInspection */
            if ($tag === 'script' && $attrName === 'charset' && !isset($allAttr['src'])) {
                return true;
            }
        }

        // remove deprecated anchor-jump
        if ($htmlMin->isDoRemoveDeprecatedAnchorName()) {
            /** @noinspection NestedPositiveIfStatementsInspection */
            if ($tag === 'a' && $attrName === 'name' && isset($allAttr['id']) && $allAttr['id'] === $attrValue) {
                return true;
            }
        }

        if ($htmlMin->isDoRemoveDefaultMediaTypeFromStyleAndLinkTag()) {
            /** @noinspection NestedPositiveIfStatementsInspection */
            if (($tag === 'link' || $tag === 'style') && $attrName === 'media' && $attrValue === 'all') {
                return true;
            }
        }

        // remove "type=text/css" for css "stylesheet"-links
        if ($htmlMin->isDoRemoveDeprecatedTypeFromStylesheetLink()) {
            /** @noinspection NestedPositiveIfStatementsInspection */
            if ($tag === 'link' && $attrName === 'type' && $attrValue === 'text/css' && isset($allAttr['rel']) && $allAttr['rel'] === 'stylesheet' && $htmlMin->isXHTML() === false && $htmlMin->isHTML4() === false) {
                return true;
            }
        }
        // remove deprecated css-mime-types
        if ($htmlMin->isDoRemoveDeprecatedTypeFromStyleAndLinkTag()) {
            /** @noinspection NestedPositiveIfStatementsInspection */
            if (($tag === 'link' || $tag === 'style') && $attrName === 'type' && $attrValue === 'text/css' && $htmlMin->isXHTML() === false && $htmlMin->isHTML4() === false) {
                return true;
            }
        }

        // remove deprecated script-mime-types
        if ($htmlMin->isDoRemoveDeprecatedTypeFromScriptTag()) {
            /** @noinspection NestedPositiveIfStatementsInspection */
            if ($tag === 'script' && $attrName === 'type' && isset(self::$executableScriptsMimeTypes[$attrValue]) && $htmlMin->isXHTML() === false && $htmlMin->isHTML4() === false) {
                return true;
            }
        }

        // remove 'type=submit' from <button type="submit">
        if ($htmlMin->isDoRemoveDefaultTypeFromButton()) {
            /** @noinspection NestedPositiveIfStatementsInspection */
            if ($tag === 'button' && $attrName === 'type' && $attrValue === 'submit') {
                return true;
            }
        }

        // remove 'value=""' from <input type="text">
        if ($htmlMin->isDoRemoveValueFromEmptyInput()) {
            /** @noinspection NestedPositiveIfStatementsInspection */
            if ($tag === 'input' && $attrName === 'value' && $attrValue === '' && isset($allAttr['type']) && $allAttr['type'] === 'text') {
                return true;
            }
        }

        // remove some empty attributes
        if ($htmlMin->isDoRemoveEmptyAttributes()) {
            /** @noinspection NestedPositiveIfStatementsInspection */
            if (\trim($attrValue) === '' && \preg_match('/^(?:class|id|style|title|lang|dir|on(?:focus|blur|change|click|dblclick|mouse(?:down|up|over|move|out)|key(?:press|down|up)))$/', $attrName)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string           $attrValue
     * @param string           $attrName
     * @param string           $scheme
     * @param string[]         $attributes
     * @param string           $tagName
     * @param HtmlMinInterface $htmlMin
     *
     * @return string
     *
     * @noinspection PhpTooManyParametersInspection
     */
    private function removeUrlSchemeHelper(
        string $attrValue,
        string $attrName,
        string $scheme,
        array $attributes,
        string $tagName,
        HtmlMinInterface $htmlMin
    ): string {
        /** @noinspection InArrayCanBeUsedInspection */
        if (
            !(isset($attributes['rel']) && $attributes['rel'] === 'external')
            &&
            !(isset($attributes['target']) && $attributes['target'] === '_blank')
            &&
            (
                (
                    $attrName === 'href'
                    &&
                    (
                        !$htmlMin->isdoKeepHttpAndHttpsPrefixOnExternalAttributes()
                        ||
                        $tagName === 'link'
                    )
                )
                ||
                $attrName === 'src'
                ||
                $attrName === 'srcset'
                ||
                $attrName === 'action'
            )
        ) {
            $attrValue = \str_replace($scheme . '://', '//', $attrValue);
        }

        return $attrValue;
    }

    /**
     * @param string $attrName
     * @param string $attrValue
     *
     * @return string
     */
    private function sortCssClassNames($attrName, $attrValue): string
    {
        if ($attrName !== 'class' || !$attrValue) {
            return $attrValue;
        }

        $classes = \array_unique(
            \explode(' ', $attrValue)
        );
        \sort($classes);

        $attrValue = '';
        foreach ($classes as $class) {
            if (!$class) {
                continue;
            }

            $attrValue .= \trim($class) . ' ';
        }

        return \trim($attrValue);
    }
}
