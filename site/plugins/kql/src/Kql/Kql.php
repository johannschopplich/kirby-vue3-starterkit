<?php

namespace Kirby\Kql;

use Kirby\Cms\Collection;
use Kirby\Toolkit\Str;

class Kql
{
    public static function help($object)
    {
        return Help::for($object);
    }

    public static function run($input, $model = null)
    {
        // string queries
        if (is_string($input) === true) {
            $result = static::query($input, $model);
            return static::render($result);
        }

        // multiple queries
        if (isset($input['queries']) === true) {
            $result = [];

            foreach ($input['queries'] as $name => $query) {
                $result[$name] = static::run($query);
            }

            return $result;
        }

        $query   = $input['query'] ?? 'site';
        $select  = $input['select'] ?? null;
        $options = [
            'pagination' => $input['pagination'] ?? null,
        ];

        $result = static::query($query, $model);

        return static::select($result, $select, $options);
    }

    public static function fetch($model, $key, $selection)
    {
        // simple key/value
        if ($selection === true) {
            return static::render($model->$key());
        }

        // selection without additional query
        if (is_array($selection) === true && empty($selection['query']) === true) {
            return static::select($model->$key(), $selection['select'] ?? null, $selection['options'] ?? []);
        }

        // nested queries
        return static::run($selection, $model);
    }

    public static function query($query, $model = null)
    {
        $kirby = kirby();
        $site  = $kirby->site();
        $model = $model ?? $site;

        $query = new Query($query, [
            'kirby' => $kirby,
            'file'  => function ($id) use ($kirby) {
                return $kirby->file($id);
            },
            'page'  => function ($id) use ($site) {
                return $site->find($id);
            },
            'site'  => $site,
            'user'  => function ($id = null) use ($kirby) {
                return $kirby->user($id);
            },
            $model::CLASS_ALIAS => $model
        ]);

        return $query->result();
    }

    public static function render($value)
    {
        if (is_object($value) === true) {
            return Interceptor::replace($value)->toResponse();
        }

        return $value;
    }

    public static function select($data, $select, array $options = [])
    {
        if ($select === null) {
            return static::render($data);
        }

        if ($select === '?') {
            return static::help($data);
        }

        if (is_a($data, 'Kirby\Cms\Collection') === true) {
            return static::selectFromCollection($data, $select, $options);
        }

        if (is_object($data) === true) {
            return static::selectFromObject($data, $select, $options);
        }

        if (is_array($data) === true) {
            return static::selectFromArray($data, $select, $options);
        }
    }

    public static function selectFromArray($array, $select, array $options = [])
    {
        $result = [];

        foreach ($select as $key => $selection) {
            if ($selection === false) {
                continue;
            }

            if (is_int($key) === true) {
                $key       = $selection;
                $selection = true;
            }

            $result[$key] = $array[$key] ?? null;
        }

        return $result;
    }

    public static function selectFromCollection(Collection $collection, $select, array $options = [])
    {
        if ($options['pagination'] ?? false) {
            $collection = $collection->paginate($options['pagination']);
        }

        $data = [];

        foreach ($collection as $model) {
            $data[] = static::selectFromObject($model, $select);
        }

        if ($pagination = $collection->pagination()) {
            return [
                'data' => $data,
                'pagination' => [
                    'page'   => $pagination->page(),
                    'pages'  => $pagination->pages(),
                    'offset' => $pagination->offset(),
                    'limit'  => $pagination->limit(),
                    'total'  => $pagination->total(),
                ],
            ];
        }

        return $data;
    }

    public static function selectFromObject($object, $select, array $options = [])
    {
        $object = Interceptor::replace($object);
        $result = [];

        if (is_string($select) === true) {
            $select = Str::split($select);
        }

        foreach ($select as $key => $selection) {
            if ($selection === false) {
                continue;
            }

            if (is_int($key) === true) {
                $key       = $selection;
                $selection = true;
            }

            $result[$key] = static::fetch($object, $key, $selection);
        }

        return $result;
    }
}
