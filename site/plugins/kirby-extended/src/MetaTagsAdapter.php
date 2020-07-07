<?php

namespace KirbyExtended;

use Exception;
use Kirby\Cms\Field;
use Kirby\Cms\Page;
use Kirby\Exception\InvalidArgumentException;
use Kirby\Toolkit\A;
use KirbyExtended\MetaTags as Tags;

class MetaTagsAdapter
{
    public $tags;

    protected static array $instances = [];
    protected ?string $indentation;
    protected ?array $order;
    protected $page;
    protected $data;

    /**
     * Constructor
     *
     * @param \Kirby\Cms\Page $page
     * @return void
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function __construct(Page $page)
    {
        $this->indentation = option('kirby-extended.meta-tags.indentation', null);
        $this->order = option('kirby-extended.meta-tags.order', null);
        $this->tags = new Tags($this->indentation, $this->order);

        $site = site();
        $templates = option('kirby-extended.meta-tags.templates', []);
        $default = option('kirby-extended.meta-tags.default', [
            'title' => $page->isHomePage() ? $site->title()->value() : $page->title()->value(),
            'meta' => [
                'description' => $site->description()
            ],
            'link' => [
                'canonical' => $page->url()
            ],
            'og' => [
                'type' => 'website',
                'url' => $page->url(),
                'title' => $page->title()->value()
            ]
        ]);

        $this->page = $page;
        $this->data = is_callable($default) ? $default($page, $site) : $default;
        $templates = is_callable($templates) ? $templates($page, $site) : $templates;

        if (!is_array($this->data)) {
            throw new Exception('Option `kirby-extended.meta-tags.default` must return an array');
        }

        if (!is_array($templates)) {
            throw new Exception('Option `kirby-extended.meta-tags.templates` must return an array');
        }

        if (isset($templates[$page->template()->name()])) {
            $this->data = A::merge($this->data, $templates[$page->template()->name()]);
        }

        $this->addTagsFromTemplate();

        static::$instances[$page->id()] = $this;
    }

    /**
     * Return an existing instance or create a new one
     *
     * @param \Kirby\Cms\Page $page
     * @return self
     */
    public static function instance(Page $page)
    {
        return static::$instances[$page->id()] ?? new static($page);
    }

    /**
     * Render current tag list
     *
     * @param array|string|null $groups
     * @return string
     */
    public function render($groups = null): string
    {
        return $this->tags->render($groups);
    }

    /**
     * Add tags from template
     *
     * @return void
     */
    protected function addTagsFromTemplate(): void
    {
        foreach ($this->data as $group => $tags) {
            if ($group === 'title') {
                $this->addTag('title', $this->data[$group], $group);
                continue;
            }

            $this->addTagsFromGroup($group, $tags);
        }
    }

    /**
     * Add tags from group
     *
     * @param string $group
     * @param array $tags
     * @return void
     */
    protected function addTagsFromGroup(string $group, array $tags): void
    {
        foreach ($tags as $tag => $value) {
            $this->addTag($tag, $value, $group);
        }
    }

    /**
     * Add single tag
     *
     * @param string $tag
     * @param mixed $value
     * @param string $group
     * @return void
     */
    protected function addTag(string $tag, $value, string $group): void
    {
        if (is_callable($value)) {
            $value = $value($this->page, site());
        } elseif ($value instanceof Field && $value->isEmpty()) {
            $value = null;
        }

        if ($group === 'title') {
            $tag = $value;
        }

        if ($group === 'json-ld') {
            $this->addJsonld($tag, $value);
        } elseif (is_array($value)) {
            $this->addTagsArray($tag, $value, $group);
        } elseif (!empty($value)) {
            $this->tags->$group($tag, $value);
        }
    }

    /**
     * Add multiple tags
     *
     * @param string $tag
     * @param array $value
     * @param string $group
     * @return void
     */
    protected function addTagsArray(string $tag, array $value, string $group): void
    {
        foreach ($value as $key => $v) {
            if (strpos($tag, 'namespace:') === 0) {
                $prefix = str_replace('namespace:', '', $tag);
                $name = $prefix !== $key ? "{$prefix}:{$key}" : $key;

                $this->addTag($name, $v, $group);
            } else {
                if (is_numeric($key)) {
                    $this->addTag($tag, $v, $group);
                } else {
                    $this->tags->$group($tag, $value);
                    break;
                }
            }
        }
    }

    /**
     * Add JSON-LD
     *
     * @param string $type
     * @param array $schema
     * @return void
     */
    protected function addJsonld(string $type, array $schema): void
    {
        $schema = array_reverse($schema, true);

        if (!isset($schema['@type'])) {
            $schema['@type'] = ucfirst($type);
        }

        if (!isset($schema['@context'])) {
            $schema['@context'] = 'http://schema.org';
        }

        $this->tags->jsonld(array_reverse($schema, true));
    }

    /**
     * Calling magic
     *
     * @param mixed $method
     * @param mixed $arguments
     * @return mixed
     * @throws Exception
     */
    public function __call($method, $arguments)
    {
        if (method_exists($this->tags, $method)) {
            return call_user_func_array([$this->tags, $method], $arguments);
        } else {
            throw new Exception('Invalid method: ' . $method);
        }
    }
}
