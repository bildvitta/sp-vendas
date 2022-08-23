<?php

namespace BildVitta\SpVendas\Tests;

use BildVitta\SpVendas\SpVendasServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            SpVendasServiceProvider::class,
        ];
    }
}
