<?php

namespace KirbyExtended;

use Kirby\Cms\Field;
use Kirby\Cms\File;
use Kirby\Cms\Page;
use Kirby\Toolkit\A;
use Kirby\Toolkit\Html;

class PageMeta {
    protected Page $page;
    protected array $metadata = [];

    public function __construct($page) {
        $this->page = $page;

        $defaults = option('kirby-extended.meta.defaults', []);
        if (!empty($defaults)) {
            $this->metadata = is_callable($defaults) ? $defaults(kirby(), site(), $page) : $defaults;
        }

        if (method_exists($this->page, 'metadata')) {
            $this->metadata = A::merge($this->metadata, $this->page->metadata());
        }
    }

    public function __call($name, $arguments)
    {
        $name = strtolower($name);
        $prefix = 'hasown';

        if (strpos($name, $prefix) === 0) {
            return $this->get(substr($name, strlen($prefix)), false)->isNotEmpty();
        }

        return $this->get($name);
    }

    public function get(string $key, bool $fallback = true): Field
    {
        $key = strtolower($key);

        if (array_key_exists($key, $this->metadata)) {
            $value = $this->metadata[$key];

            if (is_callable($value) === true) {
                $result = $value->call($this->page);

                if (is_a($result, Field::class)) {
                    return $result;
                }

                return new Field($this->page, $key, $result);
            }

            return new Field($this->page, $key, $value);
        }

        $field = $this->page->content()->get($key);
        if ($field->exists() && $field->isNotEmpty()) {
            return $field;
        }

        if ($fallback) {
            $siteContent = site()->content();

            if ($siteContent->get($key)->exists()) {
                return $siteContent->get($key);
            }
        }

        return new Field($this->page, $key, null);
    }

    public function getFile(string $key, bool $fallback = true): ?File
    {
        $key = strtolower($key);

        if (array_key_exists($key, $this->metadata)) {
            $value = $this->metadata[$key];

            if (is_callable($value) === true) {
                $value = $value->call($this->page);
            }

            if (is_a($value, File::class)) {
                return $value;
            }

            if (is_a($value, Field::class)) {
                return $value->toFile();
            }

            if (is_string($value)) {
                return $this->page->file($value);
            }
        }

        $field = $this->page->content()->get($key);
        if ($field->exists() && ($file = $field->toFile())) {
            return $file;
        }

        if ($fallback) {
            return site()->content()->get($key)->toFile();
        }

        return null;
    }

    public function hasOwnThumbnail(): bool
    {
        return $this->getFile('thumbnail', false) !== null;
    }

    public function thumbnail(bool $fallback = true): ?File
    {
        return $this->getFile('thumbnail', $fallback);
    }

    public function jsonld(): string
    {
        $html = [];
        $jsonld = $this->get('jsonld', false);

        if ($jsonld->isNotEmpty()) {
            foreach ($jsonld->value() as $type => $schema) {
                $schema = array_reverse($schema, true);

                if (!isset($schema['@type'])) {
                    $schema['@type'] = ucfirst($type);
                }

                if (!isset($schema['@context'])) {
                    $schema['@context'] = 'http://schema.org';
                }

                $schema = array_reverse($schema, true);
                $html[] = '<script type="application/ld+json">';
                $html[] = json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
                $html[] = '</script>';
            }
        }

        return implode(PHP_EOL, $html) . PHP_EOL;
    }

    public function robots(): string
    {
        $html = [];
        $robots = $this->get('robots');

        if ($robots->isNotEmpty()) {
            $html[] = Html::tag('meta', null, [
                'name' => 'robots',
                'content' => $robots->value(),
            ]);
        }

        $html[] = Html::tag('link', null, [
            'rel' => 'canonical',
            'href' => $this->page->url(),
        ]);

        return implode(PHP_EOL, $html) . PHP_EOL;
    }

    public function social(): string
    {
        $html = [];
        $meta = [];
        $site = site();

        $customOpengraph = $this->get('opengraph', false)->value() ?? [];
        $customTwitter = $this->get('twitter', false)->value() ?? [];

        // Basic OpenGraph and Twitter tags
        $opengraph = [
            'site_name' => $site->title()->value(),
            'url'       => $this->page->url(),
            'type'      => 'website',
            'title'     => $this->page->customTitle()->or($this->page->title())->value()
        ];
        $twitter = [
            'url'       => $this->page->url(),
            'card'      => 'summary_large_image',
            'title'     => $this->page->customTitle()->or($this->page->title())->value()
        ];

        // Meta, OpenGraph and Twitter description
        $description = $this->get('description');
        if ($description->isNotEmpty()) {
            $meta['description'] = $description->value();
            $opengraph['description'] = $description->value();
            $twitter['description'] = $description->value();
        }

        // OpenGraph and Twitter image
        if ($thumbnail = $this->getFile('thumbnail')) {
            $opengraph['image'] = $thumbnail->url();
            $twitter['image'] = $thumbnail->url();

            if ($thumbnail->alt()->isNotEmpty()) {
                $opengraph['image:alt'] = $thumbnail->alt()->value();
                $twitter['image:alt'] = $thumbnail->alt()->value();
            }
        } else {
            if ($twitter['card'] === 'summary_large_image') {
                $twitter['card'] = 'summary';
            }
        }

        // Merge custom tags
        $opengraph = A::merge($opengraph, $customOpengraph);
        $twitter = A::merge($twitter, $customTwitter);

        // Generate meta tags
        foreach ($meta as $name => $content) {
            $html[] = Html::tag('meta', null, [
                'name'    => $name,
                'content' => $content,
            ]);
        }

        // Generate OpenGraph tags
        foreach ($opengraph as $prop => $content) {
            if (is_array($content)) {
                if (strpos($prop, 'namespace:') === 0) {
                    $prop = str_replace('namespace:', '', $prop);
                }

                foreach ($content as $typeProp => $typeContent)
                    $html[] = Html::tag('meta', null, [
                        'property' => "{$prop}:{$typeProp}",
                        'content'  => $typeContent,
                    ]);
            } else {
                $html[] = Html::tag('meta', null, [
                    'property' => "og:{$prop}",
                    'content'  => $content,
                ]);
            }
        }

        // Generate Twitter tags
        foreach ($twitter as $name => $content) {
            $html[] = Html::tag('meta', null, [
                'name'    => "twitter:{$name}",
                'content' => $content,
            ]);
        }

        return implode(PHP_EOL, $html) . PHP_EOL;
    }

    public function opensearch(): string
    {
        return Html::tag('link', null, [
            'rel' => 'search',
            'type' => 'application/opensearchdescription+xml',
            'title' => site()->title(),
            'href' => url('open-search.xml'),
        ]) . PHP_EOL;
    }

    public function priority(): float
    {
        $priority = $this->get('priority', false)->value();

        if (empty($priority)) {
            $priority = 0.5;
        }

        return (float) min(1, max(0, $priority));
    }
}
