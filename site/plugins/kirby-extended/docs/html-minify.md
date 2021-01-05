# HTML Compressor and Minifier

Kirby HTML templates can be minified by removing extra whitespaces, comments and other unneeded characters without breaking the content structure. As a result pages become smaller in size and load faster. It will also prepare the HTML for better gzip results, by re-ranging (sort alphabetical) attributes and css-class-names.

## Configuration

Minifying HTML templates is disabled by default. To enable it, set `kirby-extended.html-minify.enable` to `true`.

## Options

| Option | Default | Values | Description |
| --- | --- | --- | --- |
| `kirby-extended.html-minify.enable` | `false` | boolean | Enable or disable HTML minification. |
| `kirby-extended.html-minify.options` | `[]` | array | Options to pass to HtmlMin. |

Head over to HtmlMin for a [list of all available options](https://github.com/voku/HtmlMin#options).

## Example

```php
// `config.php`
return [
    'kirby-extended.minify-html' => [
        'enable' => true,
        'options' => [
            'doOptimizeViaHtmlDomParser' => true, // optimize html via `HtmlDomParser()`
            'doRemoveComments' => false // remove default HTML comments (depends on `doOptimizeViaHtmlDomParser(true)`)
        ]
    ]
];
```

## Credits

Forked from [kirby-minify-html](https://github.com/afbora/kirby-minify-html)
Author: Ahmet Bora
Licence: MIT
