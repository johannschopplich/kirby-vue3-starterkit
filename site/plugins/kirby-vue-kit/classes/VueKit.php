<?php

namespace JohannSchopplich\VueKit;

use Exception;
use Kirby\Data\Data;
use Kirby\Http\Url;
use Kirby\Filesystem\F;
use Kirby\Toolkit\Html;

class VueKit
{
    protected static \JohannSchopplich\VueKit\VueKit $instance;
    protected static array $site;
    protected static array $manifest;

    /**
     * Checks for development mode by either `KIRBY_MODE` env var or
     * if a `.lock` file in `/src` exists
     */
    protected function isDev(): bool
    {
        if (env('KIRBY_MODE') === 'development') {
            return true;
        }

        $lockFile = kirby()->root('base') . '/src/.lock';
        return F::exists($lockFile);
    }

    /**
     * Gets the site data
     */
    public function getSite(): array
    {
        return static::$site ??= require kirby()->root('config') . '/app-site.php';
    }

    /**
     * Reads and parses the manifest file created by Vite
     *
     * @throws Exception
     */
    protected function getManifest(): array|null
    {
        if (isset(static::$manifest)) {
            return static::$manifest;
        }

        $manifestFile = kirby()->root('index') . '/' . option('johannschopplich.kirby-vue-kit.outDir') . '/manifest.json';

        if (!F::exists($manifestFile)) {
            if (option('debug')) {
                throw new Exception('manifest.json not found. Run `npm run build` first.');
            }

            return [];
        }

        return static::$manifest = Data::read($manifestFile);
    }

    /**
     * Gets the URL for the specified file in development mode
     */
    protected function assetDev(string $file): string
    {
        return option('johannschopplich.kirby-vue-kit.devServer') . '/' . $file;
    }

    /**
     * Gets the URL for the specified file in production mode
     */
    protected function assetProd(string $file): string
    {
        return '/' . option('johannschopplich.kirby-vue-kit.outDir') . '/' . $file;
    }

    /**
     * Includes the CSS file for the specified entry in production mode
     *
     * @throws Exception
     */
    public function css(string $entry = 'main.js')
    {
        if (!$this->isDev()) {
            return css($this->assetProd($this->getManifest()[$entry]['css'][0]));
        }
    }

    /**
     * Includes the JS file for the specified entry
     *
     * @throws Exception
     */
    public function js(string $entry = 'main.js'): string|null
    {
        $file = $this->isDev()
            ? $this->assetDev($entry)
            : $this->assetProd($this->getManifest()[$entry]['file']);

        return js($file, ['type' => 'module']);
    }

    /**
     * Preloads the JSON-encoded page data for a given page
     */
    public function preloadJson(string $name): string
    {
        $base = kirby()->multilang() ? '/' . kirby()->languageCode() : '';

        return Html::tag('link', '', [
            'rel' => 'preload',
            'href' => $base . '/' . Url::path(env('KIRBY_CONTENT_API_SLUG')) . '/' . $name . '.json',
            'as' => 'fetch',
            'type' => 'application/json',
            'crossorigin' => 'anonymous'
        ]);
    }

    /**
     * Preloads the view module for a given page, e.g. `Home.e701bdef.js`
     */
    public function preloadModule(string $name): string|null
    {
        if ($this->isDev()) {
            return null;
        }

        $match = array_filter(
            $this->getManifest(),
            fn ($i) => str_ends_with($i, ucfirst($name) . '.vue'),
            ARRAY_FILTER_USE_KEY
        );

        return !empty($match)
            ? Html::tag('link', '', [
                'rel' => 'modulepreload',
                'href' => '/' . option('johannschopplich.kirby-vue-kit.outDir') . '/' . array_values($match)[0]['file']
            ])
            : null;
    }

    /**
     * Gets the instance via lazy initialization
     */
    public static function instance(): \JohannSchopplich\VueKit\VueKit
    {
        return static::$instance ??= new static();
    }
}
