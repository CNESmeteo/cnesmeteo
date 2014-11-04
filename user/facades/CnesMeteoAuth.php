<?php namespace CnesMeteo\User\Facades;

use October\Rain\Support\Facade;

class CnesMeteoAuth extends Facade
{
    /**
     * Get the registered name of the component.
     * @return string
     */
    protected static function getFacadeAccessor() { return 'cnesmeteouser.auth'; }
}
