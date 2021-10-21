# Sitemap

Auto-generate XML sitemaps to find and catalogue pages of your site. The plugin maps pages correctly in single-language as well as in multi-language sites.

## Configuration

Enable the sitemap route for your Kirby instance by setting `kirby-extended.sitemap.enable` to `true`. The generated sitemap will be available visiting `example.com/sitemap.xml`. It will be cached if you have enabled Kirby's pages cache.

### Add templates or pages

All templates (and thus pages) are included in the final sitemap by default. You can opt-out templates and pages – head over to the [options](#options) to find out how.

### Control template visibility with blueprint options

You can disable templates on a blueprint-level as well. The `sitemap` option defines a template's visibility in the sitemap. It yields to `true` by default.

```yaml
title: Article
options:
  sitemap: false
```

## Options

| Option                                     | Default | Values | Description                                                   |
| ------------------------------------------ | ------- | ------ | ------------------------------------------------------------- |
| `kirby-extended.sitemap.enable`            | `[]`    | array  | List of template names to include in the generated sitemap.   |
| `kirby-extended.sitemap.exclude.templates` | `[]`    | array  | List of template names to exclude from the generated sitemap. |
| `kirby-extended.sitemap.exclude.pages`     | `[]`    | array  | List of page ids to exclude.                                  |

## Example

```php
// config.php
return [
    'kirby-extended.sitemap' => [
        'enable' => true,
        'exclude' => [
            'templates' => [
                'archive',
                'internal'
            ]
        ]
    ]
];
```

## Credits

Forked from [getkirby.com meta plugin](https://github.com/getkirby/getkirby.com/tree/master/site/plugins/meta)

Author: Bastian Allgeier

Licence: MIT
