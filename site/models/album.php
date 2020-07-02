<?php

/**
 * Page models extend Kirby's default page object.
 * In page models you can define methods that are then available everywhere in Kirby where you call a page of the extended type.
 * In this example, we define the cover method that either returns an image selected in the cover field
 * or the first image in the folder.
 * You can see the method in use in the `home.php`, `photography.php` and `album.php` templates
 * and in the `site/blueprints/sections/albums.yml` image query
 * More about models: https://getkirby.com/docs/guide/templates/page-models
 */

class AlbumPage extends Page
{
    public function cover()
    {
        return $this->content()->get('cover')->toFile() ?? $this->image();
    }
}
