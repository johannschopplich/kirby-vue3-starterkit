<?php

class AlbumPage extends \Kirby\Cms\Page
{
    public function cover()
    {
        return $this->content()->get('cover')->toFile() ?? $this->image();
    }
}
