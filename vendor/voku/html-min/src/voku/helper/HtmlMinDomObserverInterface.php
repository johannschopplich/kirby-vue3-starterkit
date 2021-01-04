<?php

declare(strict_types=1);

namespace voku\helper;

interface HtmlMinDomObserverInterface
{
    /**
     * Receive dom elements before the minification.
     *
     * @param SimpleHtmlDomInterface $element
     * @param HtmlMinInterface       $htmlMin
     *
     * @return void
     */
    public function domElementBeforeMinification(SimpleHtmlDomInterface $element, HtmlMinInterface $htmlMin);

    /**
     * Receive dom elements after the minification.
     *
     * @param SimpleHtmlDomInterface $element
     * @param HtmlMinInterface       $htmlMin
     *
     * @return void
     */
    public function domElementAfterMinification(SimpleHtmlDomInterface $element, HtmlMinInterface $htmlMin);
}
