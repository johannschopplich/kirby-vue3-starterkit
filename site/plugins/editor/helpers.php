<?php

function kirbyTagMaker(array $attrs = [])
{
    array_walk($attrs, function (&$attr, $key) {
        $attr = (string)$attr;

        if ($attr === null || $attr === false || $attr === '') {
            $attr = null;
        } else {
            $attr = $key . ': ' . $attr;
        }
    });

    $attrs = array_filter($attrs);

    if (empty($attrs)) {
        return null;
    }

    return '(' . implode(' ', $attrs) . ')';
}
