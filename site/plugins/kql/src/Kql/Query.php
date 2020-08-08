<?php

namespace Kirby\Kql;

use Kirby\Toolkit\Query as BaseQuery;

class Query extends BaseQuery
{
    protected function interceptor($object)
    {
        return Interceptor::replace($object);
    }

    /**
     * Resolves the query if anything
     * can be found. Otherwise returns null.
     *
     * @param string $query
     * @return mixed
     */
    protected function resolve(string $query)
    {
        // direct key access in arrays
        if (is_array($this->data) === true && array_key_exists($query, $this->data) === true) {
            $value = $this->data[$query];

            // closure resolver
            if (is_a($value, 'Closure') === true) {
                $value = $value();
            }

            return $this->interceptor($value);
        }

        $parts = $this->parts($query);
        $data  = $this->data;
        $value = null;

        while (count($parts)) {
            $part   = array_shift($parts);
            $info   = $this->part($part);
            $method = $info['method'];
            $value  = null;

            if (is_array($data)) {
                $value = $data[$method] ?? null;
            } elseif (is_object($data)) {
                $data = $this->interceptor($data);

                if (method_exists($data, $method) || method_exists($data, '__call')) {
                    $value = $data->$method(...$info['args']);
                }
            } elseif (is_scalar($data)) {
                return $data;
            } else {
                return null;
            }

            if (is_a($value, 'Closure') === true) {
                $value = $value(...$info['args']);
            }

            if (is_array($value) === true) {
                $data = $value;
            } elseif (is_object($value) === true) {
                $data = $this->interceptor($value);
            }
        }

        return $value;
    }
}
