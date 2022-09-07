[![Latest Version on Packagist](https://img.shields.io/packagist/v/bildvitta/sp-vendas.svg?style=flat-square)](https://packagist.org/packages/bildvitta/sp-vendas)
[![Total Downloads](https://img.shields.io/packagist/dt/bildvitta/sp-vendas.svg?style=flat-square)](https://packagist.org/packages/bildvitta/sp-vendas)

## Introduction

The SP (Space Probe) package is responsible for collecting remote data updates for the module, keeping the data structure similar as possible, through the message broker.

## Installation

You can install the package via composer:

```bash 
composer require bildvitta/sp-vendas
```

For everything to work perfectly in addition to having the settings file published in your application, run the command below:

```bash
php artisan sp:install
```

## Configuration

This is the contents of the published config file:

```php
return [
    'table_prefix' => env('MS_SP_VENDAS_TABLE_PREFIX', 'vendas_'),
    'db' => [
        'host' => env('VENDAS_DB_HOST', '127.0.0.1'),
        'port' => env('VENDAS_DB_PORT', '3306'),
        'database' => env('VENDAS_DB_DATABASE', 'forge'),
        'username' => env('VENDAS_DB_USERNAME', 'forge'),
        'password' => env('VENDAS_DB_PASSWORD', ''),
    ],
    'rabbitmq' => [
        'host' => env('RABBITMQ_HOST'),
        'port' => env('RABBITMQ_PORT', '5672'),
        'user' => env('RABBITMQ_USER'),
        'password' => env('RABBITMQ_PASSWORD'),
        'virtualhost' => env('RABBITMQ_VIRTUALHOST', '/'),
        'exchange' => [],
        'queue' => []
    ],
];
```

## Importing data

You can import initial data from the parent module by setting the database connection data in the configuration file. However, it will be necessary to import the data from the dependent modules first: sp-hub, sp-crm and sp-produto.

```bash
php artisan dataimport:vendas_sales
```

## Database seeder

You can seed your database with fake data to work with. However, it will be necessary to seed the other dependencies first: sp-hub, sp-crm and sp-produto.

```bash
php artisan db:seed --class=SpVendasSeeder
```

## Running the worker

After setting the message broker access data in the configuration file, you can run the worker to keep the data up to date.

```bash
php artisan rabbitmqworker:sales
```
