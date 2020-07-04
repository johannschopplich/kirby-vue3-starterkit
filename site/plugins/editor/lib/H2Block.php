<?php

namespace Kirby\Editor;

class H2Block extends H1Block
{
    public function level(): int
    {
        return parent::level() + 1;
    }
}
