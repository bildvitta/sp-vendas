<?php

namespace BildVitta\SpVendas;

use Illuminate\Support\Facades\Facade;
use RuntimeException;

class SpVendasFacade extends Facade
{
    /**
     * @const string
     */
    private const FACADE_ACCESSOR = 'sp-vendas';

    /**
     * Get the registered name of the component.
     *
     * @return string
     *
     * @throws RuntimeException
     */
    protected static function getFacadeAccessor(): string
    {
        return self::FACADE_ACCESSOR;
    }
}
