<?php

namespace KirbyVue;

use Exception;
use Kirby\Data\Data;
use Kirby\Http\Url;
use Kirby\Toolkit\F;
use Kirby\Toolkit\Html;

class Vite
{
    protected static \KirbyVue\Vite $instance;
    protected static string $apiLocation;
    protected static array $site;
    protected static array $manifest;

    /**
     * Checks for `.lock` file in `/src` as indicator for development mode
     *
     * @return bool
     */
    protected function isDev(): bool
    {
        $lockFile = kirby()->root('base') . '/src/.lock';

        return F::exists($lockFile);
    }

    /**
     * Gets the content api path
     *
     * @return string
     */
    public function useApiLocation(): string
    {
        return static::$apiLocation ??= Url::path(env('CONTENT_API_SLUG'), true);
    }

    /**
     * Gets the site data
     *
     * @return array
     */
    public function useSite(): array
    {
        return static::$site ??= require kirby()->root('config') . '/app-site.php';
    }

    /**
     * Read and parse manifest file created by Vite
     *
     * @return array|null
     * @throws Exception
     */
    protected function useManifest(): ?array
    {
        if (isset(static::$manifest)) {
            return static::$manifest;
        }

        $manifestFile = kirby()->root('index') . '/' . option('johannschopplich.kirby-vue.outDir', 'dist') . '/manifest.json';

        if (!F::exists($manifestFile)) {
            if (option('debug')) {
                throw new Exception('The `manifest.json` does not exist. Run `npm run build`.');
            }

            return [];
        }

        return static::$manifest = Data::read($manifestFile);
    }

    /**
     * Gets a value of a manifest property for a specific entry
     *
     * @param string $entry
     * @param string $key
     * @return string|void
     * @throws Exception
     */
    protected function getManifestProperty(string $entry, $key = 'file')
    {
        $manifestEntry = $this->useManifest()[$entry] ?? null;
        if (!$manifestEntry) {
            if (option('debug')) {
                throw new Exception("`$entry` is not a manifest entry.");
            }

            return;
        }

        $value = $manifestEntry[$key] ?? null;
        if (!$value) {
            if (option('debug')) {
                throw new Exception("Manifest entry `$entry` does not have property `$key`.");
            }

            return;
        }

        return $value;
    }

    /**
     * Gets the URL for the specified file for development mode
     *
     * @param string $file
     * @return string
     */
    protected function assetDev(string $file): string
    {
        return option('johannschopplich.kirby-vue.devServer', 'http://localhost:3000') . "/$file";
    }

    /**
     * Gets the URL for the specified file for production mode
     *
     * @param string $file
     * @return string
     */
    protected function assetProd(string $file): string
    {
        return kirby()->url('index') . '/' . option('johannschopplich.kirby-vue.outDir', 'dist') . "/$file";
    }

    /**
     * Includes Vite's client in development mode
     *
     * @return string|null
     */
    public function client(): ?string
    {
        return $this->isDev()
            ? js($this->assetDev('@vite/client'), ['type' => 'module'])
            : null;
    }

    /**
     * Includes the CSS file for the specified entry in production mode
     *
     * @param string|null $entry
     * @param array|null $options
     * @return string|null
     * @throws Exception
     */
    public function css(string $entry = null, array $options = []): ?string
    {
        $entry ??= option('johannschopplich.kirby-vue.entry', 'index.js');

        return !$this->isDev()
            ? css(
                    $this->assetProd($this->getManifestProperty($entry, 'css')[0]),
                    $options
                )
            : null;
    }

    /**
     * Includes the JS file for the specified entry
     *
     * @param string|null $entry
     * @param array $options
     * @return string|null
     * @throws Exception
     */
    public function js(string $entry = null, array $options = []): ?string
    {
        $entry ??= option('johannschopplich.kirby-vue.entry', 'index.js');

        $file = $this->isDev()
            ? $this->assetDev($entry)
            : $this->assetProd($this->getManifestProperty($entry, 'file'));

        $options = array_merge(['type' => 'module'], $options);

        return js($file, $options);
    }

    /**
     * Preloads the JSON-encoded page data for a given page
     *
     * @param string $name Page id
     * @return string
     */
    public function preloadJson(string $name): string
    {
        $base = kirby()->multilang() ? '/' . kirby()->languageCode() : '';

        return Html::tag('link', '', [
            'rel' => 'preload',
            'href' => $base . $this->useApiLocation() . '/' . $name . '.json',
            'as' => 'fetch',
            'type' => 'application/json',
            'crossorigin' => 'anonymous'
        ]);
    }

    /**
     * Preloads the view module for a given page, e.g. `Home.e701bdef.js`
     *
     * @param string $name Page template name or other module name
     * @return string|void
     */
    public function preloadModule(string $name)
    {
        $match = array_filter(
            $this->useManifest(),
            fn($i) => str_ends_with($i, ucfirst($name) . '.vue'),
            ARRAY_FILTER_USE_KEY
        );

        if (!empty($match)) {
            Html::tag('link', '', [
                'rel' => 'modulepreload',
                'href' => '/' . option('johannschopplich.kirby-vue.outDir', 'dist') . '/' . array_values($match)[0]['file']
            ]);
        }
    }

    /**
     * Gets the instance via lazy initialization
     *
     * @return \KirbyVue\Vite
     */
    public static function getInstance(): \KirbyVue\Vite
    {
        return static::$instance ??= new static;
    }
}
