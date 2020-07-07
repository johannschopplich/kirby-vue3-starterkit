# Kirby Extended

This package extends Kirby's base capabilities. It is built mostly upon existing packages, but unifies them under one namespace and further updates their original functionalities which includes fixing open issues.

## Included Adapters

## Env

> Forked from [kirby-env](https://github.com/beebmx/kirby-env) by Fernando Gutiérrez

Loads environment variables from `.env` automatically. This helps to store project credentials or variables outside of your code or if you want to have development and production access in different locations.

The `.env` file is generally kept out of version control since it can contain sensitive information. A separate `.env.example` file is created with all the required environment variables defined except for the sensitive ones, which are either user-supplied for their own development environments or are communicated elsewhere to project collaborators.

The `EnvAdapter` class uses the `vlucas/phpdotenv` package and enables its features for Kirby.

[👉 Full documentation](docs/env-adapter.md)

## Meta Tags

> Forked from [kirby-meta-tags](https://github.com/pedroborges/kirby-meta-tags/) by Pedro Borges

A HTML meta tags for Kirby. Supports [Open Graph](http://ogp.me), [Twitter Cards](https://dev.twitter.com/cards/overview), and [JSON Linked Data](https://json-ld.org) out of the box.

[👉 Full documentation](docs/meta-tags-adapter.md)

## Schema.org

A fluent builder for all Schema.org types and their properties and JSON-LD generator.

[👉 Full documentation](docs/schema-adapter.md)

## Requirements

- Kirby 3
- PHP 7.4+

## Installation

### Download

Download and copy this repository to `/site/plugins/kirby-extended`.

### Git submodule

```
git submodule add https://github.com/johannschopplich/kirby-extended.git site/plugins/kirby-extended
```

### Composer

```
composer require johannschopplich/kirby-extended
```

## License

[MIT](https://opensource.org/licenses/MIT)
