# Meta Tags

A HTML meta tags generator for PHP. Supports [Open Graph](http://ogp.me), [Twitter Cards](https://dev.twitter.com/cards/overview), and [JSON Linked Data](https://json-ld.org) out of the box.

> Forked from [meta-tags](https://github.com/pedroborges/meta-tags/) by Pedro Borges

**Notable Changes:**
- Cache one instance per page
- Prevent same tags being renderes multiple times
- Updated documentation
- Type hinting

## Basic Usage

Create a new `MetaTags` instance then you are ready to start adding meta tags:

```php
use KirbyExtended\MetaTags;

$tags = new MetaTags;

// <title>My Awesome Site</title>
$tags->title('My Awesome Site');

// <meta name="description" content="My site description">
$tags->meta('description', 'My site description');

// <link rel="canonical" href="https://pedroborg.es">
// <link rel="alternate" hreflang="en" href="https://en.pedroborg.es">
$tags->link('canonical', 'https://pedroborg.es');
$tags->link('alternate', [
    'hreflang' => 'en',
    'href' => 'https://en.pedroborg.es'
]);

// <meta property="og:title" content="The Title">
// <meta property="og:type" content="website">
// <meta property="og:url" content="https://pedroborg.es">
// <meta property="og:image" content="https://pedroborg.es/cover.jpg">
$tags->og('title', 'The title');
$tags->og('type', 'website');
$tags->og('url', 'https://pedroborg.es');
$tags->og('image', 'https://pedroborg.es/cover.jpg');

// <meta name="twitter:card" content="summary">
// <meta name="twitter:site" content="@pedroborg_es">
$tags->twitter('card', 'summary');
$tags->twitter('site', '@pedroborg_es');

// <script type="application/ld+json">
// {
//     "@context": "http://schema.org",
//     "@type": "Person",
//     "name": "Pedro Borges"
// }
// </script>
$tags->jsonld([
    '@context' => 'http://schema.org',
    '@type' => 'Person',
    'name' => 'Pedro Borges'
]);
```

When you are ready to output them, use the `render` method inside your template `<head>` element:

```php
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= $tags->render() ?>
```

You can also render only one tag:

```php
    <?= $tags->render('title') ?>
```

Or specify which tags to render:

```php
    <?= $tags->render(['og', 'twitter', 'json-ld']) ?>
```

By default Meta Tag will indent the tags with 4 spaces and use the following order:

1. `<title>`
1. `<meta>` (General)
1. `<meta property="og:*">` (Open Graph)
1. `<meta name="twitter:*">` (Twitter Cards)
1. `<link>`
1. `<script type="application/ld+json">` (JSON-LD)

You can change that when instantiating the `MetaTag` class:

```php
use KirbyExtended\MetaTags;

$tags = new MetaTags("\t", ['meta', 'title', 'link', 'og', 'twitter', 'json-ld']);
```
