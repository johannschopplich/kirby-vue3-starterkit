<?php

namespace Kirby\Editor;

class H3Block extends H2Block
{
    public function level(): int
    {
        return parent::level() + 1;
    }
}
