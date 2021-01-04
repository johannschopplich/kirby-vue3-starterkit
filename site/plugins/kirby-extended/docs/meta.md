## Meta

Handles the generation of meta tags for search engines, social networks, browsers and beyond.

## How it works

1. The plugin looks for metadata defaults, set in Kirby's global configuration.
2. If the defaults don't contain the specific key, it looks in the pagel model if it provides a `metadata()` method that returns an array or metadata fields.
3. If the page model doesn't contain the specific key, it will look for a field from a pages content file (e.g. `article.txt`) by the corrsponding key. 
4. If that also fails, it will fall back to default metadata, as stored in the `site.txt` file at the top-level of the content directory.

That way, every page will always be able to serve default values, even if the specific page or its model does not contain information like e.g. a thumbnail or a dedicated description.

## Usage

It's recommended to render the metadata in your `header.php` snippet. You can define the order they will be echoed.

```php
<?php $meta = $page->meta() ?>
// Canonical link and robots meta tag if configured
<?= $meta->robots() ?>
// Schema markup
<?= $meta->jsonld() ?>
// Meta description, OpenGraph and Twitter tags
<?= $meta->social() ?>
```

## Configuration

### Default tags

The `kirby-extended.meta.defaults` option key may be populated by default metadata. It will be used as the base by the plugin. You can overwrite defaults with the `metadata()` method of page models per template.

```php
// config.php
return [
    'kirby-extended.meta' => [
        'defaults' => return function ($kirby, $page, $site) {
            $description = $page->description()->or($site->description())->value();
            return [
                // Available keys
                'robots' => 'nofollow',
                'description' => $description,
                'opengraph' => [],
                'twitter' => [],
                'jsonld' => [
                    'WebSite' => [
                        'url' => $site->url(),
                        'name' => $site->title()->value(),
                        'description' => $description
                    ]
                ]
            ];
        };
    ]
];
```

### Page models for template-specific meta data

You might not want to adapt meta data for specific templates.

The following example adds a `metadata()` method to all article templates, that takes care of generating useful metadata, if an article issue is shared in a social network and also provides an automatically generated description for search engines. All keys returned by the `metadata()` method must be lowercase. Any array item can be a value of a closure, that will be called on the `$page` object, so you can use `$this` within the closure to refer to the current page.

```php
class ArticlePage extends \Kirby\Cms\Page
{
    public function metadata(): array
    {
        $description = $this->description()->or($this->text()->excerpt(140))->value();
        return [
            'description' => $description,
            'thumbnail' => function () {
                return $this->image();
            },
            'opengraph' => [
                'type' => 'article'
                // Open Graph object types can be defined in an array
                // with `type:` as prefix
                'namespace:article' => [
                    'author' => 'Kirby',
                    'published_time' => $this->published()->toDate('Y-m-d')
                ]
            ],
            'jsonld' => [
                'BlogPosting' => [
                    'headline' => $this->title()->value(),
                    'description' => $description
                ]
            ]
        ];
    }
}
```

### Available field keys

**Customtitle:** By default, the metadata plugin will use the page's `title` field. You can override this by defining an `customtitle` field for a specific page. The `customtitle` will then be used for OpenGraph and Twitter metadata instead of the page title.

**Description:** The description field is used for search engines as a plain meta tag and additionally added as an OpenGraph meta tag, which is used by social media networks like e.g. Facebook or Twitter.

**Thumbnail:** The thumbnail for sharing the page in a social network. If defining a custom thumbnail for a page, you should make sure to also add a text file containing an `alt` text for the corresponding image, because it is also used by social networks.

**Robots:** Generates the "robots" meta tag, that gives specifix instructions to crawlers. By default, this tag is not preset, unless a default value is defined in `site.txt`. Use a value, that you would also use if you wrote the markup directly (e.g. `noindex, nofollow`).

**Priority:** The priority for telling search engines about the importance of pages of your site. Must be a float value between 0.0 and 1.0. This value will not fall back to `site.txt`, but rather use 0.5 as default, if not explicit priority was found in the page's content or returned by its model.

**Changefreq:** Optional parameter, telling search engines how often a page changes. Possible values can be found in the [sitemaps protocol specification](https://www.sitemaps.org/protocol.html).

## Options

| Option | Default | Values | Description |
| --- | --- | --- | --- |
| `kirby-extended.meta.defaults` | `[]` | array or function | You can use `$kirby`, `$site` and `$page` (fixed order) within the closure arguments to refer to the given object.

## Credits

Forked from [getkirby.com meta plugin](https://github.com/getkirby/getkirby.com/tree/master/site/plugins/meta)
Author: Bastian Allgeier
Licence: MIT
