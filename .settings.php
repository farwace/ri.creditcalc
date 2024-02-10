<?php

return [
    'controllers' => [
        'value' => [
            'defaultNamespace' => '\Ri\CreditCalc\Controllers',
        ],
        'readonly' => true,
    ],
    'services' => [
        'value' => [
            'ri.contragents' => [
                'className' => \RI\CreditCalc\Services\ContrAgentsService::class,
            ]
        ],
        'readonly' => true,
    ]
];
