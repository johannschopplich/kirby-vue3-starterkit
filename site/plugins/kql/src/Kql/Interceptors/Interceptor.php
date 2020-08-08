<?php

namespace Kirby\Kql\Interceptors;

use Exception;
use Kirby\Kql\Help;
use Kirby\Kql\Kql;

abstract class Interceptor
{
    const CLASS_ALIAS = null;

    protected $object;
    protected $toArray = [];

    public function __construct($object)
    {
        $this->object = $object;
    }

    public function __call($method, array $args = [])
    {
        if ($this->isAllowedMethod($method) === true) {
            return $this->object->$method(...$args);
        }

        $this->forbiddenMethod($method);
    }

    public function allowedMethods(): array
    {
        return [];
    }

    protected function forbiddenMethod(string $method)
    {
        $className = get_class($this->object);
        throw new Exception('The method "' . $className . '::' . $method . '()" is not allowed in the API context');
    }

    protected function isAllowedMethod($method)
    {
        return in_array($method, $this->allowedMethods()) === true;
    }

    public function __debugInfo(): array
    {
        return [
            'type'    => $this::CLASS_ALIAS,
            'methods' => Help::forMethods($this->object, $this->allowedMethods()),
            'value'   => $this->toArray()
        ];
    }

    public function toArray(): ?array
    {
        return Kql::select($this, $this->toArray);
    }

    public function toResponse()
    {
        return $this->toArray();
    }
}
