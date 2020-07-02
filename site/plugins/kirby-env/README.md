# Kirby Env

**Kirby Env** use the `vlucas/phpdotenv` package and enable their features for Kirby.
This package should be used if you want to store your project credentials or variables in a separate place from your code or if you want to have development and production access in different places.

## Installation

### Installation with composer

```ssh
composer require beebmx/kirby-env
```

## Usage

You don't need to do anything if your want to access in any $page, just use the page method:

```php
$page->env('VAR');
```

But if you want to set variables in your `config.php` file, first you need to load the object with:

```php
(new \Beebmx\KirbyEnv('main/path'))->load();
```

You need to have an `.env` file in your `main/path` directory.  
You can store any credentials or variables secure like:

```ssh
KIRBY_DEBUG=false

SECRET_KEY=my_secret_key
PUBLIC_KEY=my_public_key

FOO=BAR
BAZ=${FOO}
```


## Options

When you create an instance of `\Beebmx\KirbyEnv` you need to load the environment with:

```php
\Beebmx\KirbyEnv::load();
```

If you require the immutability provides by `vlucas/phpdotenv`, just:

```php
\Beebmx\KirbyEnv::overload();
```

## Example

Here's an example of a configuration in `config.php`file:

```php
<?php

\Beebmx\KirbyEnv::load('main/path');

return [
    'debug' => env('KIRBY_DEBUG', false),
    'SECRET' => env('SECRET_KEY'),
    'PUBLIC' => env('PUBLIC_KEY'),
];

```

## Usage note

It is important that you add to your `.gitignore` the `.env` file. 
The `main/path` is where the `.env` file is located.
