<?php

namespace KirbyExtended;

use Kirby\Cms\Url;
use Kirby\Data\Json;
use Kirby\Exception\Exception;
use Kirby\Toolkit\F;

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
     * @var array
     */
    public static array $site;

    /**
     * Vite manifest to render links with hashed filenames
     *
     * @var array|null
     */
    public static ?array $manifest = null;

    /**
     * Get and cache `$assetsDir`
     *
     * @return string
     */
    public static function useAssetsDir(): string {
        return static::$assetsDir ??= Url::path(env('VITE_ASSETS_DIR'), true);
    }

    /**
     * Get and cache `$apiLocation`
     *
     * @return string
     */
    public static function useApiLocation(): string {
        return static::$apiLocation ??= Url::path(env('CONTENT_API_SLUG'), true);
    }

    /**
     * Get and cache `$site`
     *
     * @return array
     */
    public static function useSite(): array {
        return static::$site ??= require kirby()->root('config') . '/spa-site.php';
    }

    /**
     * Get and cache `$manifest`
     *
     * @return array
     * @throws Exception
     */
    public static function useManifest(): array {
        if (static::$manifest !== null) {
            return static::$manifest;
        }

        $manifestPath = kirby()->root() . static::useAssetsDir() . '/manifest.json';
        if (!F::exists($manifestPath)) {
            throw new Exception('No build asset manifest.json found. You have to build the app first: `npm run build`.');
        }

        $deserializedManifest = F::read($manifestPath);
        return static::$manifest ??= Json::decode($deserializedManifest);
    }

    /**
     * Returns the filename for a build asset, e.g. `style.d4814c7a.css`
     *
     * @param string $filename Asset filename to get its hashed filename for
     * @return string
     * @throws Exception
     */
    public static function pathToAsset (string $filename): string {
        $hashedFilename = static::useManifest()[$filename] ?? null;
        if ($hashedFilename === null) {
            throw new Exception("No hashed build asset found for {$filename}. Make sure it's bundled by Vite.");
        }

        return static::useAssetsDir() . '/' . $hashedFilename;
    }

    /**
     * Preloads the JSON-encoded page data for a given page
     *
     * @param string $name Page id
     * @return string
     */
    public static function jsonPreloadLink (string $name): string {
        return '<link rel="preload" href="' . static::useApiLocation() . '/' . $name . '.json" as="fetch" crossorigin>';
    }

    /**
     * Preloads the view module for a given page, e.g. `Home.e701bdef.js`
     *
     * @param string $name Page template name or other module name
     * @return string|void
     */
    public static function modulePreloadLink (string $pattern) {
        $hashedFilename = static::useManifest()[ucfirst($pattern) . '.js'] ?? null;
        if ($hashedFilename) {
            return '<link rel="modulepreload" href="' . static::useAssetsDir() . '/' . $hashedFilename . '">';
        }
    }
}
