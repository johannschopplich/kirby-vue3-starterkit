# Meta Tags Adapter

A HTML meta tags for Kirby. Supports [Open Graph](http://ogp.me), [Twitter Cards](https://dev.twitter.com/cards/overview), and [JSON Linked Data](https://json-ld.org) out of the box.

> Forked from [kirby-meta-tags](https://github.com/pedroborges/kirby-meta-tags/) by Pedro Borges

## Basic Usage

Add the following one-liner to the `head` section of your template or the `header.php` snippet:

```diff
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
+   <?= $page->metaTags() ?>
```

By default the `metaTags` page method will render all tag groups at once. But you can also render only one tag at a time:

```php
<?= $page->metaTags('title') ?>
```

Or specify which tags to render:

```php
<?= $page->metaTags(['og', 'twitter', 'json-ld']) ?>
```

### Default

The plugin ships with some default meta tags enabled for your convenience:

```php
// `config.php`
return [
    'kirby-extended.meta-tags.default' => function ($page, $site) {
        return [
            'title' => $site->title()->value(),
            'meta' => [
                'description' => $site->description()->value()
            ],
            'link' => [
                'canonical' => $page->url()
            ],
            'og' => [
                'type' => 'website',
                'url' => $page->url(),
                'title' => $page->isHomePage()
                    ? $site->title()->value()
                    : $page->title()->value()
            ]
        ];
    }
]
```

**The `kirby-extended.meta-tags.default` option is applied to all pages on your Kirby site.** Of course you can change the defaults. In order to do that, just copy this example to your `site/config/config.php` file and tweak it to fit your website needs.

### Templates

Following the flexible spirit of Kirby, you also have the option to add template specific meta tags:

```php
// `config.php`
return [
    'kirby-extended.meta-tags.templates' => function ($page, $site) {
        return [
            'song' => [
                'og' => [
                    'type' => 'music.song',
                    'namespace:music' => [
                        'duration' => $page->duration()->value(),
                        'album' => $page->parent()->url(),
                        'musician' => $page->singer()->html()
                    ]
                ]
            ]
        ];
    }
]
```

In the example above, those settings will only be applied to pages which template is `song`.

For more information on all the `meta`, `link`, Open Graph and Twitter Card tags available, check out these resources:

- [`<head>` Cheat Sheet](http://gethead.info)
- [Open Graph](http://ogp.me)
- [Twitter Cards](https://dev.twitter.com/cards/overview)

## Options

Both the `kirby-extended.meta-tags.default` and `kirby-extended.meta-tags.templates` accept similar values:

### `kirby-extended.meta-tags.default`

It accepts an array containing any or all of the following keys: `title`, `meta`, `link`, `og`, and `twitter`. With the exception of `title`, all other groups must return an array of key-value pairs. Check out the [tag groups](#tag-groups) section to learn which value types are accepted by each key.

```php
// `config.php`
return [
    'kirby-extended.meta-tags.default' => function ($page, $site) {
        return [
            'title' => 'Site Name',
            'meta' => [ /* meta tags */ ],
            'link' => [ /* link tags */ ],
            'og' => [ /* Open Graph tags */ ],
            'twitter' => [ /* Twitter Card tags */ ],
            'json-ld' => [ /* Schema markup */ ],
        ];
    }
]
```

### `kirby-extended.meta-tags.templates`

This option allows you to define a template specific set of meta tags. It must return an array where each key corresponds to the template name you are targeting.

```php
// `config.php`
return [
    'kirby-extended.meta-tags.templates' => function ($page, $site) {
        return [
            'article' => [ /* tag groups */ ],
            'about' => [ /* tag groups */ ],
            'products' => [ /* tag groups */ ],
        ];
    }
]
```

When a key matches the current page template name, it is merged and overrides any repeating properties defined on the `kirby-extended.meta-tags.default` option so you don't have to repeat yourself.

## Tag Groups

These groups accept string, closure, or array as their values. Being so flexible, the sky is the limit to what you can do with Meta Tags!

### `title`

Corresponds to the HTML `<title>` element and accepts a `string` as value.

```php
'title' => $page->isHomePage() ? $site->title()->value() : $page->title()->value(),
```

> You can also pass it a `closure` that returns a `string` if the logic to generate the `title` is more complex.

### `meta`

The right place to put any generic HTML `<meta>` elements. It takes an `array` of key-value pairs. The returned value must be a `string` or `closure`.

```php
'meta' => [
    'description' => $site->description()->value(),
],
```

<details><summary>Show HTML</summary>

```html
<meta name="description" content="Website description">
```

</details>

### `link`

This tag group is used to render HTML `<link>` elements. It takes an `array` of key-value pairs. The returned value can be a `string`, `array`, or `closure`.

```php
'link' => [
    'stylesheet' => url('assets/css/main.css'),
    'icon' => [
        ['href' => url('assets/img/icons/favicon-192.png'), 'sizes' => '192x192', 'type' =>'image/png']
    ],
    'canonical' => $page->url(),
    'alternate' => function ($page) {
        $locales = [];

        foreach (kirby()->languages() as $language) {
            if ($language->code() === kirby()->language()->code()) continue;

            $locales[] = [
                'hreflang' => $language->code(),
                'href' => $page->urlForLanguage($language->code())
            ];
        }

        return $locales;
    }
],
```

<details><summary>Show HTML</summary>

```html
<link rel="stylesheet" href="https://pedroborg.es/assets/css/main.css">
<link rel="icon" href="https://pedroborg.es/assets/img/icons/favicon-192.png" sizes="192x192" type="image/png">
<link rel="canonical" href="https://pedroborg.es">
<link rel="alternate" hreflang="pt" href="https://pt.pedroborg.es">
<link rel="alternate" hreflang="de" href="https://de.pedroborg.es">
```

</details>

### `og`

Where you can define [Open Graph](http://ogp.me) `<meta>` elements.

```php
'og' => [
    'title' => $page->title()->value(),
    'type' => 'website',
    'url' => $page->url()
],
```

<details><summary>Show HTML</summary>

```html
<meta property="og:title" content="Passionate web developer">
<meta property="og:type" content="website">
<meta property="og:url" content="https://pedroborg.es">
```

</details>

Of course you can use Open Graph [structured objects](http://ogp.me/#structured). Let's see a blog post example:

```php
// `config.php`
return [
    'kirby-extended.meta-tags.templates' => function ($page, $site) {
        return [
            'article' => [ // template name
                'og' => [  // tags group name
                    'type' => 'article', // overrides the default
                    'namespace:article' => [
                        'author' => $page->author(),
                        'published_time' => $page->date('Y-m-d'),
                        'modified_time' => $page->modified('Y-m-d'),
                        'tag' => ['tech', 'web']
                    ],
                    'namespace:image' => function(Page $page) {
                        $image = $page->cover()->toFile();
        
                        return [
                            'image' => $image->url(),
                            'height' => $image->height(),
                            'width' => $image->width(),
                            'type' => $image->mime()
                        ];
                    }
                ]
            ]
        ];
    }
]
```

<details><summary>Show HTML</summary>

```html
<!-- Merged default definition -->
<title>Pedro Borges</title>
<meta name="description" content="Passionate web developer">
<meta property="og:title" content="How to make a Kirby plugin">
<meta property="og:url" content="https://pedroborg.es/blog/how-to-make-a-kirby-plugin">

<!-- Template definition -->
<meta property="og:type" content="article">
<meta property="og:article:author" content="Pedro Borges">
<meta property="og:article:published_time" content="2017-02-28">
<meta property="og:article:modified_time" content="2017-03-01">
<meta property="og:article:tag" content="tech">
<meta property="og:article:tag" content="web">
<meta property="og:image" content="https://pedroborg.es/content/blog/how-to-make-a-kirby-plugin/code.jpg">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:type" content="image/jpeg">
```

</details>

Use the `namespace:` prefix for structured properties:

- `author` inside `namespace:article` becomes `og:article:author`.
- `image` inside `namespace:image` becomes `og:image`.
- `width` inside `namespace:image` becomes `og:image:width`.

> When using Open Graph tags, you will want to add the `prefix` attribute to the `html` element as suggested on [their docs](http://ogp.me/#metadata): `<html prefix="og: http://ogp.me/ns#">`

### `twitter`

This tag group works just like the previous one, but it generates `<meta>` tags for [Twitter Cards](https://dev.twitter.com/cards/overview) instead.

```php
'twitter' => [
    'card' => 'summary',
    'site' => $site->twitter(),
    'title' => $page->title(),
    'namespace:image' => function ($page) {
        $image = $page->cover()->toFile();

        return [
            'image' => $image->url(),
            'alt' => $image->alt()
        ];
    }
]
```

<details><summary>Show HTML</summary>

```html
<meta name="twitter:card" content="summary">
<meta name="twitter:site" content="@pedroborg_es">
<meta name="twitter:title" content="My blog post title">
<meta name="twitter:image" content="https://pedroborg.es/content/blog/my-article/cover.jpg">
<meta name="twitter:image:alt" content="Article cover image">
```

</details>

### `json-ld`

Use this tag group to add [JSON Linked Data](https://json-ld.org) schemas to your website.

```php
'json-ld' => [
    'Organization' => [
        'name' => $site->title()->value(),
        'url' => $site->url(),
        'contactPoint' => [
            '@type' => 'ContactPoint',
            'telephone' => $site->phoneNumber()->value(),
            'contactType' => 'Customer Service'
        ]
    ]
]
```

> If you leave them out, `http://schema.org` will be added as `@context` and the array key will be added as `@type`.

<details><summary>Show HTML</summary>

```html
<script type="application/ld+json">
{
    "@context": "http://schema.org",
    "@type": "Organization",
    "name": "Example Co",
    "url": "https://example.com",
    "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "+1-401-555-1212",
        "contactType": "Sustomer Service"
    }
}
</script>
```

</details>
