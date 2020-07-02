<?php

namespace Kirby\Editor;

class BlockquoteBlock extends Block
{
    public function markdown(): string
    {
        return '> ' . $this->content() . PHP_EOL . PHP_EOL;
    }
}
