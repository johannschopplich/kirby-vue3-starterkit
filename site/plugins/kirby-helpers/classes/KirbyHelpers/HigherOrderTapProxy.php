<?php

namespace KirbyHelpers;

class HigherOrderTapProxy
{
    /**
     * The target being tapped
     */
    public $target;

    /**
     * Create a new tap proxy instance
     */
    public function __construct($target)
    {
        $this->target = $target;
    }

    /**
     * Dynamically pass method calls to the target
     */
    public function __call(string $method, array $parameters)
    {
        $this->target->{$method}(...$parameters);

        return $this->target;
    }
}
