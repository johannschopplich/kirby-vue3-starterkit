<?php

namespace KirbyExtended;

use InvalidArgumentException;
use Kirby\Cms\Url;
use Kirby\Exception\Exception;

class SpaAdapter {
    /**
     * Relative path to assets dir
     *
     * @var string
     */
    public static string $assetsDir;

    /**
     * API location for content
     *
     * @var string
     */
    public static string $apiLocation;

    /**
     * Global `site` data for the index template
     *
     * @var string
     */
    public static string $site;

    /**
     * Get and cache the `$assetsDir`
     *
     * @return string
     */
    public static function useAssetsDir(): string {
        return static::$assetsDir ??= Url::path(env('VITE_ASSETS_DIR'), true, true);
    }

    /**
     * Get and cache the `$apiLocation`
     *
     * @return string
     */
    public static function useApiLocation(): string {
        return static::$apiLocation ??= Url::path(env('CONTENT_API_SLUG'), true);
    }

    /**
     * Get and cache the `$site`
     *
     * @return array
     */
    public static function useSite(): string {
        return static::$site ??= require kirby()->roots()->snippets() . '/spa-adapter/site.php';
    }

    /**
     * Returns the filename for a build asset, e.g. `style.d4814c7a.css`
     *
     * @param string $pattern A pattern to be matched by `glob`
     * @return string
     * @throws Exception
     */
    public static function pathToAsset ($pattern): string {
        $filename = glob(kirby()->roots()->index() . static::useAssetsDir() . $pattern)[0] ?? null;
        if ($filename === null) {
            throw new Exception('No production assets found. You have to bundle the app first. Run `npm run build`.');
        }

        return static::useAssetsDir() . basename($filename);
    }

    /**
     * Preloads the JSON-encoded page data for a given page
     *
     * @param mixed $name Page id
     * @return void|string
     * @throws InvalidArgumentException
     */
    public static function jsonPreloadLink ($name) {
        if (empty($name)) return;
        return '<link rel="preload" href="' . static::useApiLocation() . '/' . $name . '.json" as="fetch" crossorigin>';
    }

    /**
     * Preloads the view module for a given page, e.g. `Home.e701bdef.js`
     *
     * @param mixed $name Page template name or other module name
     * @return void|string
     * @throws InvalidArgumentException
     */
    public static function modulePreloadLink ($name) {
      $filename = glob(kirby()->roots()->index() . static::useAssetsDir() . ucfirst($name) . '.*.js')[0] ?? null;
      if ($filename === null) return;
      return '<link rel="modulepreload" href="' . static::useAssetsDir() . basename($filename) . '">';
    }
}
