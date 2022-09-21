# Redirects

Create redirect routes easily that only take over if no actual page/route has been matched. It uses the `go()` helper under the hood.

## Configuration

Redirects have to be defined in the `kirby-helpers.redirects` option key in an array with the old pattern as key and the target page/URL as value. Placeholders can be used in the key and referenced via `$1`, `$2` and so on in the target string.

Instead of a target string, a callback function returning that string may also be used.

## Options

| Option                    | Default | Values | Description        |
| ------------------------- | ------- | ------ | ------------------ |
| `kirby-helpers.redirects` | `false` | array  | List of redirects. |

## Example

```php
// config.php
return [
    'kirby-helpers.redirects' => [
        // Simple redirects
        'from/foo'                  => 'to/bar',
        'blog/article-(:any)'       => 'blog/articles/$1',
        'old/reference/(:all?)'     => 'new/reference/$1',

        // Redirects with logic
        'photography/(:any)/(:all)' => function ($category, $uid) {
            if ($page = page('photography')->grandChildren()->listed()->findBy('uid', $uid)) {
                return $page->url();
            }

            return 'error';
        }
    ]
];
```

## Credits

Forked from [`redirects` plugin](https://github.com/getkirby/getkirby.com/pull/1131).

## License

[MIT](../LICENSE) License © 2020-2022 [Nico Hoffmann](https://github.com/getkirby)

[MIT](../LICENSE) License © 2020-2022 [Johann Schopplich](https://github.com/johannschopplich)
