<?php

namespace KirbyExtended;

use Kirby\Cms\Template;
use Kirby\Toolkit\Tpl;
use voku\helper\HtmlMin;

class HtmlMinTemplate extends Template
{
    public function render(array $data = []): string
    {
        $html = Tpl::load($this->file(), $data);

        if (option('debug', false)) {
            return $html;
        }

        if (!option('kirby-extended.html-minify.enable', false)) {
            return $html;
        }

        $htmlMin = new HtmlMin();
        $options = option('kirby-extended.html-minify.options', []);

        foreach ($options as $option => $status) {
            if (method_exists($htmlMin, $option)) {
                $htmlMin->{$option}((bool) $status);
            }
        }

        return $htmlMin->minify($html);
    }
}
