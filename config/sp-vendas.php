<?php

use App\Models\User;
use BildVitta\Hub\Entities\HubCompany;

return [
    'table_prefix' => env('MS_SP_VENDAS_TABLE_PREFIX', 'vendas_'),

    'model_user' => env('MS_SP_VENDAS_USER_MODEL', User::class),
    'model_company' => env('MS_SP_VENDAS_COMPANY_MODEL', HubCompany::class),

    'db' => [
        'host' => env('VENDAS_DB_HOST'),
        'port' => env('VENDAS_DB_PORT'),
        'database' => env('VENDAS_DB_DATABASE'),
        'username' => env('VENDAS_DB_USERNAME'),
        'password' => env('VENDAS_DB_PASSWORD'),
    ],

    'rabbitmq' => [
        'host' => env('RABBITMQ_HOST'),
        'port' => env('RABBITMQ_PORT'),
        'user' => env('RABBITMQ_USER'),
        'password' => env('RABBITMQ_PASSWORD'),
        'virtualhost' => env('RABBITMQ_VIRTUALHOST', '/'),
        'exchange' => [
            'sales' => env('RABBITMQ_EXCHANGE_SALES', 'sales'),
        ],
        'queue' => [
            'sales' => env('RABBITMQ_QUEUE_SALES', 'sales.juridico'),
        ]
    ],
];
