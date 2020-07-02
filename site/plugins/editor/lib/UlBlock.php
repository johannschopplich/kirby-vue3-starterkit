<?php

namespace Kirby\Editor;

class UlBlock extends Block
{
    public function prefix()
    {
        return '-';
    }

    public function markdown(): string
    {
        $next  = $this->next();
        $break = PHP_EOL . PHP_EOL;

        if ($next && $this->next()->type() === $this->type()) {
            $break = PHP_EOL;
        }

        return $this->prefix() . ' ' . $this->htmlToMarkdown($this->content()) . $break;
    }
}
