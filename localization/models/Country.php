<?php namespace CnesMeteo\Localization\Models;


/**
 * Country Model
 */
class Country extends \RainLab\User\Models\Country
{
    /**
     * @var array Relations
     */
    public $hasMany = [
        'states' => ['CnesMeteo\Localization\Models\State']
    ];

}