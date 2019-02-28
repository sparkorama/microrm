<?php

namespace sparkorama\microrm\Facades;

use Illuminate\Support\Facades\Facade;

class microrm extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'microrm';
    }
}
