<?php

namespace xnf4o\litres\Facades;

use Illuminate\Support\Facades\Facade;

class litres extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'litres';
    }
}
