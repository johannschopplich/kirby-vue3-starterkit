<?php

namespace KirbyExtended;

use Kirby\Cms\Url;
use Kirby\Data\Json;
use Kirby\Exception\Exception;
use Kirby\Toolkit\F;

class SpaAdapter
{
    protected static string $apiLocation;
    protected static array $site;
    protected static array $manifest;

    /**
     * Get path to content api
     *
     * @return string
     */
    public static function useApiLocation(): string
    {
        return static::$apiLocation ??= Url::path(env('CONTENT_API_SLUG'), true);
    }

    /**
     * Get site data for the index template
     *
     * @return array
     */
    public static function useSite(): array
    {
        return static::$site ??= require kirby()->root('config') . '/spa-site.php';
    }

    /**
     * Get Vite manifest to render links with hashed filenames
     *
     * @return array
     * @throws Exception
     */
    public static function useManifest(): array
    {
        if (isset(static::$manifest)) {
            return static::$manifest;
        }

        $manifestPath = kirby()->root() . '/assets/manifest.json';
        if (!F::exists($manifestPath)) {
            throw new Exception('Build asset manifest.json not found. You have to build the app first: `npm run build`.');
        }

        $deserializedManifest = F::read($manifestPath);
        static::$manifest = Json::decode($deserializedManifest);
        return static::$manifest;
    }

    /**
     * Creates a script tag for the main JavaScript module
     *
     * @return string
     */
    public static function js(): string
    {
        $path = static::useManifest()['index.html']['file'];
        return js($path, ['type' => 'module']);
    }


    /**
     * Creates a link tag for the main CSS stylesheet
     *
     * @return string
     */
    public static function css(): string
    {
        $path = static::useManifest()['index.html']['css'][0];
        return css($path);
    }

    /**
     * Preloads the JSON-encoded page data for a given page
     *
     * @param string $name Page id
     * @return string
     */
    public static function jsonPreloadLink(string $name): string
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
    public static function modulePreloadLink(string $name)
    {
        $match = array_filter(
            static::useManifest(),
            fn($i) => str_ends_with($i, ucfirst($name) . '.vue'),
            ARRAY_FILTER_USE_KEY
        );

        if (!empty($match)) {
            return '<link rel="modulepreload" href="/' . array_values($match)[0]['file'] . '">';
        }
    }
}
