<?php

if (! function_exists('prefixTableName')) {

    /**
     * Set table name with prefix.
     * @param string $name
     * @return string
     */
    function prefixTableName(string $name): string
    {
        return sprintf('%s%s', config('sp-vendas.table_prefix'), $name);
    }
}
