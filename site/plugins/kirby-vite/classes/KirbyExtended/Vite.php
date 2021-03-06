<?php

namespace KirbyExtended;

use Exception;
use Kirby\Cms\Url;
use Kirby\Data\Data;
use Kirby\Toolkit\F;

class Vite
{
    protected static \KirbyExtended\Vite $instance;
    protected static string $apiLocation;
    protected static array $site;
    protected static array $manifest;

    /**
     * Gets the content api path
     *
     * @return string
     */
    public static function useApiLocation(): string
    {
        return static::$apiLocation ??= Url::path(env('CONTENT_API_SLUG'), true);
    }

    /**
     * Gets the site data
     *
     * @return array
     */
    public static function useSite(): array
    {
        return static::$site ??= require kirby()->root('config') . '/spa-site.php';
    }

    /**
     * Read and parse manifest file created by Vite
     *
     * @return array
     * @throws Exception
     */
    public static function useManifest(): array
    {
        if (isset(static::$manifest)) {
            return static::$manifest;
        }

        $manifestFile = kirby()->root() . '/dist/manifest.json';

        if (!F::exists($manifestFile)) {
            throw new Exception('The `manifest.json` does not exist. Run `npm run build`.');
        }

        return static::$manifest = Data::read($manifestFile);
    }

    /**
     * Creates a script tag for the main JavaScript module
     *
     * @return string
     */
    public function js(): string
    {
        $path = 'dist/' . static::useManifest()['index.html']['file'];
        return js($path, ['type' => 'module']);
    }

    /**
     * Creates a link tag for the main CSS stylesheet
     *
     * @return string
     */
    public function css(): string
    {
        $path = 'dist/' . static::useManifest()['index.html']['css'][0];
        return css($path);
    }

    /**
     * Preloads the JSON-encoded page data for a given page
     *
     * @param string $name Page id
     * @return string
     */
    public function jsonPreloadLink(string $name): string
    {
        $base = kirby()->multilang() ? '/' . kirby()->languageCode() : '';
        return '<link rel="preload" href="' . $base . static::useApiLocation() . '/' . $name . '.json" as="fetch" crossorigin>';
    }

    /**
     * Preloads the view module for a given page, e.g. `Home.e701bdef.js`
     *
     * @param string $name Page template name or other module name
     * @return string|void
     */
    public function modulePreloadLink(string $name)
    {
        $match = array_filter(
            static::useManifest(),
            fn($i) => str_ends_with($i, ucfirst($name) . '.vue'),
            ARRAY_FILTER_USE_KEY
        );

        if (!empty($match)) {
            return '<link rel="modulepreload" href="/dist/' . array_values($match)[0]['file'] . '">';
        }
    }

    /**
     * Gets the instance via lazy initialization
     *
     * @return \KirbyExtended\Vite
     */
    public static function getInstance(): \KirbyExtended\Vite
    {
        return static::$instance ??= new static;
    }
}
