<p align="center">
  <img src="./.github/icon.svg" alt="Logo of Kirby + Vue.js Starterkit" width="114" height="114">
</p>

<h3 align="center">Kirby + Vue.js Starterkit</h3>

<p align="center">
  SPA with Vue 3 and Kirby: SEO-friendly, automatic routing, i18n and more!<br>
  <a href="https://kirby-vue3-starterkit.jhnn.dev"><strong>Explore the starterkit live »</strong></a>
</p>

<br>

## Kirby + Vue.js Starterkit

### Key Features

- ⚡️ [Vue 3](https://github.com/vuejs/vue-next) & [Vite](https://vitejs.dev)
- 🛣 Automatic routing
- 📦 [On-demand components auto importing](./src/components/)
- 📑 [Nuxt-inspired module system](./src/modules/)
- 🔍 SEO-friendly: [server-side generated](https://github.com/johannschopplich/kirby-extended/blob/main/docs/meta.md) meta tags
- 🌐 [Multi-language](#multi-language) support
- ♿ Accessible frontend routing
- 💫 [Stale-while-revalidate](#stale-while-revalidate) page data

## Alternatives

- [kirby-vue-lightkit](https://github.com/johannschopplich/kirby-vue-lightkit): ⛺️ Minimal Kirby + Vue starter: File-based routing, UnoCSS, SEO & more
- [kirby-nuxt-starterkit](https://github.com/johannschopplich/kirby-nuxt-starterkit): 💚 Kirby's sample site – ported to Nuxt 3 and KirbyQL

## Introduction

> [Or jump right to the setup](#setup).

This boilerplate is a tight and comprehensive integration of [Vue.js](https://github.com/vuejs/vue-next) in the frontend and [Kirby](https://getkirby.com) as headless CMS. The content is provided as JSON through Kirby and fetched by the frontend.

![Lighthouse report](./.github/lighthouse-report.png)

### Folder Structure

Some notes about the folder structure with some additional comments on important files.

<details>
<summary><b>Expand folder tree</b></summary>

```sh
kirby-vue3-starterkit/
|
|   # Main entry point of the website, point your web server to this directory
├── public/
|   |
|   |   # Frontend assets generated by Vite (not tracked by Git)
|   ├── dist/
|   |
|   |   # Static images like icons
|   ├── img/
|   |
|   |   # Kirby's media folder for thumbnails and more (not tracked by Git)
|   └── media/
|
|   # Kirby's core folder containing templates, blueprints, etc.
├── site/
|   ├── config/
|   |   |
|   |   |   # General configuration settings for Kirby and plugins
|   |   ├── config.php
|   |   |
|   |   |   # Builds a JSON-encoded `site` object for the frontend
|   |   |   # Used by Vue Router to populate routes, but can be extended by commonly used data
|   |   └── app-site.php
|   |
|   |   # Only relevant in multi-language setups
|   ├── languages/
|   |
|   ├── plugins/kirby-vue-kit/
|   |   |
|   |   |   # Core of the Vite integration plugin, mainly registers routes
|   |   ├── index.php
|   |   |
|   |   |   # Routes to handle `.json` requests and serving the `index.php` snippet
|   |   └── routes.php
|   |
|   |   # Templates for JSON content representations fetched by frontend
|   |   # Contains also index page (`_app-index.php`)
|   └── templates/
|       |
|       |   # Handles build asset paths, inlines the `site` object, includes SEO meta tags, etc.
|       └── _app-index.php
|
|   # Includes all frontend-related sources
├── src/
|   |
|   |   # `Header`, `Footer`, `Intro` and other components (auto imported on-demand)
|   ├── components/
|   |
|   |   # Composables for common actions
|   ├── composables/
|   |   |
|   |   |   # Announces any useful information for screen readers
|   |   ├── useAnnouncer.js
|   |   |
|   |   |   # Provides information about the current language
|   |   ├── useLanguages.js
|   |   |
|   |   |   # Retrieves pages from the content API
|   |   ├── useKirbyApi.js
|   |   |
|   |   |   # Returns page data for the current path, similarly to Kirby's `$page` object
|   |   ├── usePage.js
|   |   |
|   |   |   # Returns a object corresponding to Kirby's global `$site`
|   |   └── useSite.js
|   |
|   |   # Modules system entries will be auto installed
|   ├── modules/
|   |   |
|   |   |   # Installs the `v-kirbytext` directive to handle internal page links inside KirbyText
|   |   ├── kirbytext.js
|   |   |
|   |   |   # Initializes the Vue Router
|   |   └── router.js
|   |
|   |   # Vue.js views corresponding to Kirby templates
|   |   # Routes are being automatically resolved
|   ├── views/
|   |
|   ├── App.vue
|   ├── index.css
|   └── index.js
|
|   # Contains everything content and user data related (not tracked by Git)
├── storage/
|   ├── accounts/
|   ├── cache/
|   ├── content/
|   ├── logs/
|   └── sessions/
|
|   # Kirby CMS and other PHP dependencies (handled by Composer)
├── vendor/
|
|   # Environment variables for both Kirby and Vite (to be duplicated as `.env`)
├── .env.example
|
|   # Configuration file for Vite
└── vite.config.js
```

</details>

## Caching

The frontend will store pages between individual routes/views. When the tab get reloaded, the data for each page is freshly fetched from the API once again.

![Caching for Kirby and Vue 3 starterkit](./.github/kirby-vue-3-cache-and-store.png)

## Stale-While-Revalidate

The stale-while-revalidate mechanism for the [`usePage`](src/composables/usePage.js) hook allows you to respond as quickly as possible with cached page data if available, falling back to the network request if it's not cached. The network request is then used to update the cached page data – which directly affects the view after lazily assigning changes (if any), thanks to Vue's reactivity.

## Prerequisites

- Node.js with npm (only required to build the frontend)
- PHP 8.0+

> Kirby is not a free software. You can try it for free on your local machine but in order to run Kirby on a public server you must purchase a [valid license](https://getkirby.com/buy).

## Setup

### Composer

Kirby-related dependencies are managed via [Composer](https://getcomposer.org) and located in the `vendor` directory. Install them with:

```bash
composer install
```

### Node Dependencies

Install npm dependencies:

```bash
npm ci
```

### Environment Variables

Duplicate the [`.env.development.example`](.env.development.example) as `.env`::

```bash
cp .env.development.example .env
```

Optionally, adapt it's values.

Vite will load .env files according to the [docs](https://vitejs.dev/guide/env-and-mode.html#env-files):

```.env # loaded in all cases
.env.local          # loaded in all cases, ignored by git
.env.[mode]         # only loaded in specified mode
.env.[mode].local   # only loaded in specified mode, ignored by git
```

Kirby will only load the main .env file

### Static assets

_During development_ Kirby can't access static files located in the `src` folder. Therefore it's necessary to create a symbolic link inside of the public folder:

```bash
ln -s $PWD/src/assets ./public/assets
```

## Usage

### Build Mode

During development a `.lock` file will be generated inside the `src` directory to let the backend now it runs in development mode. This file is deleted when running the build command.

> ℹ️ Alternatively, you can set a `KIRBY_MODE` env variable containing either `development` or `production` to set the app mode programmatically and overwrite the `.lock` file mechanism. This may ease setups with Docker.

### Development

You can start the development process with:

```bash
# Runs `npm run kirby` parallel to `vite`
npm run dev
```

Afterwards visit the app in your browser: [`http://127.0.0.1:8080`](http://127.0.0.1:8080)

> For Valet users: Of course you can use a virtual host alternatively!

Vite is used in combination with [backend integration](https://vitejs.dev/guide/backend-integration.html) and only serves frontend assets, not the whole app. Thus, `http://localhost:3000` won't be accessible.

The backend is served by the PHP built-in web server on `http://127.0.0.1:8080` by default, but you can adapt the location in your `.env` file.

### Production

Build optimized frontend assets to `public/dist`:

```bash
npm run build
```

Vite will generate a hashed version of all assets, including images and fonts saved inside `src/assets`. It will further create a `manifest.json` file with hash records etc.

### Deployment

> ℹ️ See [ploi-deploy.sh](./scripts/ploi-deploy.sh) for exemplary deployment instructions.

> ℹ️ Some hosting environments require to uncomment `RewriteBase /` in [`.htaccess`](public/.htaccess) to make site links work.

## Configuration

All development and production related configurations for both backend and frontend code are located in your `.env` file:

- `KIRBY_DEV_HOSTNAME` and `KIRBY_DEV_PORT` specify the address where you wish the Kirby backend to be served from. It is used by the frontend to fetch content data as JSON.
- Keys starting with `VITE_` are available in your code following the `import.meta.env.VITE_CUSTOM_VARIABLE` syntax.

For example, setting `KIRBY_CACHE` to `true` is useful in production environment.

### Content API Slug

To change the API slug to fetch JSON-encoded page data from, set

- `CONTENT_API_SLUG` to a value of your liking (defaults to `spa`). It can even be left empty to omit a slug altogether!

> You can't use Kirby's internal API slug (defaults to `api`). If you insist on using `api` for _your_ content endpoint, you can rename Kirby's by adding a `KIRBY_API_SLUG` key and set it to something other than `api`.

### Multi-Language

Multiple languages are supported. A comprehensive introduction about [multi-language setups](https://getkirby.com/docs/guide/languages/introduction) may be found on the Kirby website.

To enable language handling, you don't have to edit the [`config.php`](site/config/config.php) manually. Just set

- `KIRBY_MULTILANG` to `true`.
- `KIRBY_MULTILANG_DETECT` to `true` (optional but recommended).

Then, visit the panel and add new languages by your liking. The Panel **automatically renames all existing content** and file meta data files and includes the language extension.

Language data is provided by the global `site` object, which can be accessed via the `useSite()` hook.

### Stale-While-Revalidating

To keep page data fresh with **stale-while-revalidate**, set:

- `VITE_STALE_WHILE_REVALIDATE` to `true`

## Credits

- Huge thanks to [arnoson](https://github.com/arnoson) for his [Kirby Vite Plugin](https://github.com/arnoson/kirby-vite).
- Thanks to Jakub Medvecký Heretik for his inspirational work on [kirby-vue-starterkit](https://github.com/jmheretik/kirby-vue-starterkit) which got me starting to build my own Kirby Vue integration.

## License

[MIT](./LICENSE) License © 2020-2022 [Johann Schopplich](https://github.com/johannschopplich)
