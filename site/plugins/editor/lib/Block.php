<?php

namespace Kirby\Editor;

use InvalidArgumentException;
use Kirby\Cms\Content;
use Kirby\Cms\Field;
use Kirby\Cms\HasSiblings;

/**
 * Represents a single block
 * from the editor, which can
 * be inspected further or
 * converted to HTML
 */
class Block
{
    use HasSiblings;

    /**
     * @var \Kirby\Cms\Content
     */
    protected $attrs;

    /**
     * @var \Kirby\Cms\Field
     */
    protected $content;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var array
     */
    protected $params;

    /**
     * @var \Kirby\Cms\Page|\Kirby\Cms\Site|\Kirby\Cms\User|\Kirby\Cms\File
     */
    protected $parent;

    /**
     * @var \Kirby\Editor\Blocks
     */
    protected $siblings;

    /**
     * @var string
     */
    protected $type;

    /**
     * Creates a new block object
     *
     * @param array $params
     * @param \Kirby\Editor\Blocks $siblings
     */
    public function __construct(array $params, Blocks $siblings = null)
    {
        if (isset($params['type']) === false) {
            throw new InvalidArgumentException('The block type is missing');
        }

        if (isset($params['id']) === false) {
            throw new InvalidArgumentException('The block id is missing');
        }

        $this->attrs    = $params['attrs'] ?? [];
        $this->content  = $params['content'] ?? '';
        $this->id       = $params['id'];
        $this->options  = $params['options'] ?? [];
        $this->parent   = $params['parent'] ?? null;
        $this->siblings = $siblings ?? new Blocks();
        $this->type     = $params['type'];

        // create content and attrs objects
        $this->attrs    = new Content($this->attrs, $this->parent);
        $this->content  = new Field($this->parent, 'content', $this->content);
    }

    /**
     * Converts the object to a string
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->html();
    }

    /**
     * Returns the attrs object
     *
     * @return \Kirby\Cms\Content
     */
    public function attrs()
    {
        return $this->attrs;
    }

    /**
     * Returns the content object
     *
     * @return \Kirby\Cms\Field
     */
    public function content()
    {
        return $this->content;
    }

    /**
     * Controller for the block snippet
     *
     * @return array
     */
    public function controller(): array
    {
        return [
            'block'   => $this,
            'content' => $this->content(),
            'id'      => $this->id(),
            'attrs'   => $this->attrs(),
            'prev'    => $this->prev(),
            'next'    => $this->next()
        ];
    }

    /**
     * @return \Kirby\Editor\Block
     */
    public static function factory(array $params, Blocks $blocks)
    {
        if (isset($params['type']) === false) {
            throw new InvalidArgumentException('The block type is missing');
        }

        $name       = str_replace(['.', '-', '_'], '', $params['type']);
        $customName = 'Kirby\\Editor\\' . $name . 'Block';
        $className  = class_exists($customName) ? $customName : 'Kirby\\Editor\\Block';

        return new $className($params, $blocks);
    }

    /**
     * Converts the block to HTML
     *
     * @return string
     */
    public function html(): string
    {
        return snippet('editor/' . $this->type(), $this->controller(), true);
    }

    /**
     * Convert inline html to markdown
     *
     * @param string $html
     * @return string
     */
    public function htmlToMarkdown(string $html = null): string
    {
        $replace = [
            '<code>'    => '`',
            '</code>'   => '`',
            '<strong>'  => '**',
            '</strong>' => '**',
            '<b>'       => '**',
            '</b>'      => '**',
            '<em>'      => '*',
            '</em>'     => '*',
            '<i>'       => '*',
            '</i>'      => '*',
        ];

        $html = str_replace(array_keys($replace), array_values($replace), $html);
        $html = preg_replace_callback('!<a.*?href="(.*?)".*>(.*?)</a>!', function ($matches) {
            $href = $matches[1] ?? '/';
            $text = $matches[2] ?? $href;

            return '[' . $text . '](' . url($href) . ')';
        }, $html);

        return $html;
    }

    /**
     * Returns the block id
     *
     * @return string
     */
    public function id(): string
    {
        return $this->id;
    }

    /**
     * Compares the block to another one
     *
     * @param \Kirby\Editor\Block $block
     * @return bool
     */
    public function is(Block $block): bool
    {
        return $this->id() === $block->id();
    }

    /**
     * Checks if the block is empty
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->content()->isEmpty();
    }

    /**
     * Checks if the block is not empty
     *
     * @return bool
     */
    public function isNotEmpty(): bool
    {
        return $this->isEmpty() === false;
    }

    /**
     * Returns the Kirby instance
     *
     * @return \Kirby\Cms\App
     */
    public function kirby()
    {
        return $this->parent()->kirby();
    }

    /**
     * Markdown generator
     *
     * @return string
     */
    public function markdown(): string
    {
        return $this->htmlToMarkdown($this->content()) . PHP_EOL . PHP_EOL;
    }

    /**
     * Returns all blog options
     *
     * @return array
     */
    public function options(): array
    {
        return $this->options;
    }

    /**
     * Returns the parent model
     *
     * @return \Kirby\Cms\Page | \Kirby\Cms\Site | \Kirby\Cms\File | \Kirby\Cms\User
     */
    public function parent()
    {
        return $this->parent;
    }

    /**
     * Returns the sibling collection
     * This is required by the HasSiblings trait
     *
     * @return \Kirby\Editor\Blocks
     */
    protected function siblingsCollection()
    {
        return $this->siblings;
    }

    /**
     * Returns the block type
     *
     * @return string
     */
    public function type(): string
    {
        return $this->type;
    }

    /**
     * The result is being sent to the editor
     * via the API in the panel
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'attrs'   => $this->attrs()->toArray(),
            'content' => $this->content()->value(),
            'id'      => $this->id(),
            'type'    => $this->type(),
        ];
    }

    /**
     * Converts the block to html first
     * and then places that inside a field
     * object. This can be used further
     * with all available field methods
     *
     * @return \Kirby\Cms\Field;
     */
    public function toField()
    {
        return new Field($this->parent, $this->id, $this->html());
    }

    /**
     * Alias for html()
     *
     * @return string
     */
    public function toHtml(): string
    {
        return $this->html();
    }

    /**
     * Converts the block to markdown
     *
     * @return string
     */
    public function toMarkdown(): string
    {
        return $this->markdown();
    }

    /**
     * Prepare the block to be stored
     *
     * @return array
     */
    public function toStorage(): array
    {
        return $this->toArray();
    }
}
