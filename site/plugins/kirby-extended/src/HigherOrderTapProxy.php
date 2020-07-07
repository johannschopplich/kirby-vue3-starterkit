<?php

namespace KirbyExtended;

class HigherOrderTapProxy
{
    /**
     * The target being tapped
     *
     * @var mixed
     */
    public $target;

    /**
     * Create a new tap proxy instance
     *
     * @param mixed $target
     * @return void
     */
    public function __construct($target)
    {
        $this->target = $target;
    }

    /**
     * Dynamically pass method calls to the target
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        $this->target->{$method}(...$parameters);

        return $this->target;
    }
}
