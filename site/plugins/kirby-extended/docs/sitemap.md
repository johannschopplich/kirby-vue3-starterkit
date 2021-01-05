## Sitemap

Auto-generate XML sitemaps to find and catalogue pages of your site. The plugin maps pages correctly in single-language as well as in multi-language sites.

## Configuration

Enable the sitemap route for your Kirby instance by setting `kirby-extended.sitemap.enable` to `true`. The generated sitemap will be available visiting `example.com/sitemap.xml`. It will be cached if you have enabled Kirby's pages cache.

### Add templates or pages

You have to opt-in page templates and/or page ids which should be included in the final sitemap. Head over to the [options](#options) for a list of available keys.

### Control template visibility with blueprint options

You can enable or disable templates on a blueprint-level as well. The `sitemap` option defines a template's visibility in the sitemap. It yields back to `false` by default.

```yaml
title: Article
options:
  sitemap: true
```

## Options

| Option | Default | Values | Description |
| --- | --- | --- | --- |
| `kirby-extended.sitemap.enable` | `[]` | array | List of template names to include in the generated sitemap. |
| `kirby-extended.sitemap.templatesInclude` | `[]` | array | List of template names to include in the generated sitemap. |
| `kirby-extended.sitemap.pagesInclude` | `[]` | array | List of page ids to include. |
| `kirby-extended.sitemap.pagesExclude` | `[]` | array | List of page ids to exclude. |

## Example

```php
// config.php
return [
    'kirby-extended.sitemap' => [
        'enable' => true,
        'templatesInclude' => [
            'article',
            'default',
            'home',
            'photography'
        ]
    ]
];
```

## Credits

Forked from [getkirby.com meta plugin](https://github.com/getkirby/getkirby.com/tree/master/site/plugins/meta)
Author: Bastian Allgeier
Licence: MIT
