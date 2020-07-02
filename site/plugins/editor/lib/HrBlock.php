<?php

namespace Kirby\Editor;

class HrBlock extends Block
{
    public function isEmpty(): bool
    {
        return false;
    }

    public function markdown(): string
    {
        return '****' . PHP_EOL . PHP_EOL;
    }
}
