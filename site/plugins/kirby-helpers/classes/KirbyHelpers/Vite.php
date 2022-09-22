<?php

namespace KirbyHelpers;

use Kirby\Data\Data;
use Kirby\Http\Uri;

class Vite
{
    protected static $instance;

    public static function instance()
    {
        return static::$instance ??= new static();
    }

    public $manifest;

    public function __construct()
    {
        $path = implode(DIRECTORY_SEPARATOR, array_filter([
            kirby()->root(),
            option('kirby-helpers.vite.build.outDir', 'dist'),
            'manifest.json'
        ], 'strlen'));

        try {
            $this->manifest = Data::read($path);
        } catch (\Throwable $t) {
            // Vite is running in development mode.
        }
    }

    public function isDev(): bool
    {
        return $this->manifest === null;
    }

    public function prodUrl(string $path): string
    {
        return implode('/', array_filter([
            kirby()->url(),
            option('kirby-helpers.vite.build.outDir', 'dist'),
            $path
        ], 'strlen'));
    }

    public function devUrl(string $path): string
    {
        $uri = new Uri([
            'scheme' => option('kirby-helpers.vite.server.https', false) ? 'https' : 'http',
            'host'   => option('kirby-helpers.vite.server.host', 'localhost'),
            'port'   => option('kirby-helpers.vite.server.port', 5173),
            'path'   => $path
        ]);

        return $uri->toString();
    }

    /**
     * Output a `<script>` tag for an entry point
     *
     * @param string $entry e.g. `src/main.js`
     */
    public function js(string $entry): string
    {
        if (is_array($this->manifest)) {
            $url = $this->prodUrl($this->manifest[$entry]['file']);
        } else {
            $url = $this->devUrl($entry);
        }

        return js($url, ['type' => 'module']);
    }

    /**
     * Outputs `<link>` tags for each CSS file of an entry point
     *
     * @param string $entry The JavaScript entry point that includes your CSS
     */
    public function css(string $entry)
    {
        if (is_array($this->manifest)) {
            foreach ($this->manifest[$entry]['css'] as $file) {
                return css($this->prodUrl($file));
            }
        }
    }
}
