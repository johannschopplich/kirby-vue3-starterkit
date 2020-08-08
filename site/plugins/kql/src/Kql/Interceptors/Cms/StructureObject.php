<?php

namespace Kirby\Kql\Interceptors\Cms;

class StructureObject extends Model
{
    const CLASS_ALIAS = 'structureItem';

    public function allowedMethods(): array
    {
        return array_merge(
            $this->allowedMethodsForSiblings(),
            [
                'content',
                'id',
                'parent',
            ]
        );
    }
}
