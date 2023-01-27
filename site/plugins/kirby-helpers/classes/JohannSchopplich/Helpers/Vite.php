<?php

namespace JohannSchopplich\Helpers;

use Kirby\Data\Data;
use Kirby\Http\Uri;

class Vite
{
    public array|null $manifest = null;
    protected static Vite|null $instance = null;

    public static function instance(): Vite
    {
        return static::$instance ??= new static();
    }

    public function __construct()
    {
        $path = implode(DIRECTORY_SEPARATOR, array_filter([
            kirby()->root(),
            option('johannschopplich.helpers.vite.build.outDir', 'dist'),
            'manifest.json'
        ], 'strlen'));

        try {
            $this->manifest = Data::read($path);
        } catch (\Throwable $t) {
            // Vite is running in development mode
        }
    }

    public function isDev(): bool
    {
        return $this->manifest === null;
    }

    public function devUrl(string $path): string
    {
        $uri = new Uri([
            'scheme' => option('johannschopplich.helpers.vite.server.https', false) ? 'https' : 'http',
            'host'   => option('johannschopplich.helpers.vite.server.host', 'localhost'),
            'port'   => option('johannschopplich.helpers.vite.server.port', 5173),
            'path'   => $path
        ]);

        return $uri->toString();
    }

    public function prodUrl(string $path): string
    {
        return implode('/', array_filter([
            kirby()->url(),
            option('johannschopplich.helpers.vite.build.outDir', 'dist'),
            $path
        ], 'strlen'));
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
}
