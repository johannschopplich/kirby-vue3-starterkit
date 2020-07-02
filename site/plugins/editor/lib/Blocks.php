<?php

namespace Kirby\Editor;

use Closure;
use Kirby\Cms\Collection;
use Kirby\Data\Json;
use Kirby\Toolkit\Str;
use Throwable;

class Blocks extends Collection
{
    /**
     * Return HTML when the collection is
     * converted to a string
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->html();
    }

    /**
     * Converts the blocks to HTML and then
     * uses the Str::excerpt method to create
     * a non-formatted, shortened excerpt from it
     *
     * @param mixed ...$args
     * @return string
     */
    public function excerpt(...$args)
    {
        return Str::excerpt($this->html(), ...$args);
    }

    /**
     * Creates a new block collection from a
     * JSON string
     *
     * @param string|array $value
     * @param Page|File|User|Site $parent
     * @param array $options
     * @return Kirby\Editor\Blocks
     */
    public static function factory($blocks, $parent, array $options = [])
    {
        if (empty($blocks) === true) {
            return new static();
        }

        if (is_array($blocks) === false) {
            try {
                $blocks = Json::decode((string)$blocks);
            } catch (Throwable $e) {
                $blocks = Parser::parse($blocks, true, $parent);
            }
        }

        if (!is_array($blocks) === true) {
            $blocks = [];
        }

        // inject ids, if they are missing
        $blocks = array_map(function ($block) {
            if (empty($block['id']) === true) {
                $block['id'] = '_' . Str::random(9);
            }
            return $block;
        }, $blocks);

        // create a new collection of blocks
        $collection = new static();

        foreach ($blocks as $params) {
            $params['parent']  = $parent;
            $params['options'] = $options;
            $block = Block::factory($params, $collection);
            $collection->append($block->id(), $block);
        }

        return $collection;
    }

    /**
     * Convert all blocks to HTML
     *
     * @return string
     */
    public function html(): string
    {
        $html = [];

        foreach ($this->data as $block) {
            $html[] = $block->html();
        }

        return implode($html);
    }

    /**
     * Converts the collection to markdown
     *
     * @return string
     */
    public function markdown(): string
    {
        $md = [];

        foreach ($this->data as $block) {
            $md[] = $block->markdown();
        }

        return implode($md);
    }

    /**
     * Convert the blocks to an array
     *
     * @return array
     */
    public function toArray(Closure $map = null): array
    {
        return array_values(parent::toArray($map));
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
     * Alias for markdown()
     *
     * @return string
     */
    public function toMarkdown(): string
    {
        return $this->markdown();
    }

    /**
     * Prepare the blocks to be stored
     *
     * @return array
     */
    public function toStorage(): array
    {
        $blocks = [];

        foreach ($this->data as $block) {
            $blocks[] = $block->toStorage();
        }

        return $blocks;
    }
}
