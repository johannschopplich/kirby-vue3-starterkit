<?php

use Kirby\Cms\Page;

class AlbumPage extends Page
{
    public function cover()
    {
        return $this->content()->get('cover')->toFile() ?? $this->image();
    }
}
