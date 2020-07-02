<?php

return function ($field, array $options = []) {
    return Kirby\Editor\Blocks::factory($field->value(), $field->parent(), $options);
};
