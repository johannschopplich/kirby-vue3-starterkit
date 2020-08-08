<?php

@include_once __DIR__ . '/vendor/autoload.php';

class_alias('Kirby\Kql\Kql', 'Kql');

function kql($input, $model = null)
{
    return Kql::run($input, $model);
}

Kirby::plugin('getkirby/kql', [
    'api' => [
        'routes' => [
            [
                'pattern' => 'query',
                'method' => 'POST|GET',
                'action' => function () {
                    $result = Kql::run([
                        'query'      => get('query'),
                        'select'     => get('select'),
                        'pagination' => [
                            'page'  => get('page', 1),
                            'limit' => get('limit', 100)
                        ]
                    ]);

                    return [
                        'code'   => 200,
                        'result' => $result,
                        'status' => 'ok',
                    ];
                }
            ]
        ]
    ]
]);
