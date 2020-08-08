<?php

namespace Kirby\Kql;

use Exception;

class Interceptor
{
    public static function replace($object)
    {
        if (is_object($object) === false) {
            throw new Exception('Unsupported value: ' . gettype($object));
        }

        $className   = get_class($object);
        $interceptor = str_replace('Kirby\\', 'Kirby\\Kql\\Interceptors\\', $className);

        if (is_a($object, 'Kirby\\Kql\\Interceptors\\Interceptor') === true) {
            return $object;
        }

        if ($className !== $interceptor && class_exists($interceptor) === true) {
            return new $interceptor($object);
        }

        foreach (class_parents($object) as $parent) {
            $interceptor = str_replace('Kirby\\', 'Kirby\\Kql\\Interceptors\\', $parent);

            if (class_exists($interceptor) === true) {
                return new $interceptor($object);
            }
        }

        throw new Exception('Unsupported object: ' . $className);
    }
}
