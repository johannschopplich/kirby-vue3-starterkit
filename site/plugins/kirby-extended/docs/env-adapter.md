# Env Adapter

Loads environment variables from `.env` automatically. This helps to store project credentials or variables outside of your code or if you want to have development and production access in different locations.

The `.env` file is generally kept out of version control since it can contain sensitive information. A separate `.env.example` file is created with all the required environment variables defined except for the sensitive ones, which are either user-supplied for their own development environments or are communicated elsewhere to project collaborators.

The `EnvAdapter` class uses the `vlucas/phpdotenv` package and enables its features for Kirby.

> Forked from [kirby-env](https://github.com/beebmx/kirby-env) by Fernando Gutiérrez

**Notable Changes:**
- Upgrade to `vlucas/phpdotenv` v5
- Up-to-date Laravel `env` helper
- Optional `putenv` support
- Type hinting

## Usage

> It is important to hide your `.env` from the public. Make sure to add it to your `.gitignore` file.

### … in Templates, Snippets etc.

You can use the `$page` method to retrieve an environment variable from anywhere:

```php
$page->env('VARIABLE');
```

The `EnvAdapter` doesn't have to be initialized by yourself. It uses configurable defaults. Head over to [Options](#options) for more information about how to set them.

### … within `config.php`

If you want to use variables in your `config.php` file, you have to call the `EnvAdapter` manually to load the environment object before Kirby's finishes initializing.

Two optional arguments `path` and `filename` may be used to load an environment file from a custom location and with a name other than `.env`. 

```php
\KirbyExtended\EnvAdapter::load('path/to/env', '.env.other');
```

Head over to the [Example](#example) for usage in `config.php`. 

## Options

| Option | Default | Description |
| --- | --- | --- |
| `kirby-extended.env.path` | `kirby()->roots()->base()` | Path to where your default environment file is located.
| `kirby-extended.env.filename` | `.env` | Default environment filename to load.

## Example

```php
<?php

$base = dirname(__DIR__, 2);
\KirbyExtended\EnvAdapter::load($base);

return [
    'debug' => env('KIRBY_DEBUG', false),
    'SECRET' => env('SECRET_KEY'),
    'PUBLIC' => env('PUBLIC_KEY'),
];
```

With an `.env` file inside the `$base` directory in place, you can access securely stored credentials and variables. Here it is an example `.env`:

```ssh
KIRBY_DEBUG=false

SECRET_KEY=my_secret_key
PUBLIC_KEY=my_public_key

FOO=BAR
BAZ=${FOO}
```
