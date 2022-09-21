# Dotenv

Loads environment variables from a local `.env` file automatically. This helps to store project credentials or variables outside of your code or if you want to have development and production access in different locations.

## Configuration

Create a `.env` file in Kirby's base directory. You can change the default filename to look for with the `kirby-helpers.env.filename` option key.

> It is important to hide your `.env` from the public. Make sure to add it to your `.gitignore` file.

## Usage

### Inside Templates, Snippets and More

You can use the `$site` method `$site->env()` to retrieve an environment variable from anywhere:

```php
$site->env('VARIABLE');
```

### Within Kirby’s Configuration

If you want to use variables in your `config.php` file, you have to call the `Env` class manually to load the environment object before Kirby finishes initializing.

THe first argument `path` is required. The second one (`filename`) is optional and may be used to load an environment file called something other than `.env`.

```php
\KirbyHelpers\Env::load('path/to/env', '.env');
```

### Options

| Option                       | Default                 | Values | Description                                |
| ---------------------------- | ----------------------- | ------ | ------------------------------------------ |
| `kirby-helpers.env.path`     | `kirby()->root('base')` | string | Path from which the file should be loaded. |
| `kirby-helpers.env.filename` | `.env`                  | string | Environment filename to load.              |

## Example

```
# .env
KIRBY_DEBUG=false

SECRET_KEY=my_secret_key
PUBLIC_KEY=my_public_key

FOO=BAR
BAZ=${FOO}
```

With a `.env` file inside the `$base` directory in place, you can access your securely stored credentials and variables:

```php
// config.php
$base = dirname(__DIR__, 2);
\KirbyHelpers\Env::load($base);

return [
    'debug' => env('KIRBY_DEBUG', false),
    'SECRET' => env('SECRET_KEY'),
    'PUBLIC' => env('PUBLIC_KEY')
];
```

## License

[MIT](../LICENSE) License © 2020-2022 [Johann Schopplich](https://github.com/johannschopplich)
