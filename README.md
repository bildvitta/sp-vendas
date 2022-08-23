[![Latest Version on Packagist](https://img.shields.io/packagist/v/bildvitta/sp-vendas.svg?style=flat-square)](https://packagist.org/packages/bildvitta/sp-vendas)
[![Total Downloads](https://img.shields.io/packagist/dt/bildvitta/sp-vendas.svg?style=flat-square)](https://packagist.org/packages/bildvitta/sp-vendas)

## Introduction

The SP (Space Probe) package is responsible for collecting remote data updates for the module, keeping the data structure similar as possible, through the message broker.

## Installation

You can install the package via composer:

```bash 
composer require bildvitta/sp-vendas:dev-develop
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

With the configuration file sp-vendas.php published in your configuration folder it is necessary to create environment variables in your .env file:

```
MS_SP_VENDAS_TABLE_PREFIX="vendas_"
```
