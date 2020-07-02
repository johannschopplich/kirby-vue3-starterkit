<?php

namespace Kirby\Editor;

class OlBlock extends UlBlock
{
    public function prefix()
    {
        $prev = $this->prev();

        if ($prev && $prev->type() === 'ol') {
            return (int)($prev->prefix()) + 1 . '.';
        }

        return '1.';
    }
}
