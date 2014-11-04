<?php namespace CnesMeteo\Localization\Models;


/**
 * State Model
 */
class State extends \RainLab\User\Models\State
{

    /**
     * @var array Relations
     */
    public $belongsTo = [
        'country' => ['CnesMeteo\Localization\Models\Country']
    ];
    public $hasMany = [
        'provinces' => ['CnesMeteo\Localization\Models\Province']
    ];

}