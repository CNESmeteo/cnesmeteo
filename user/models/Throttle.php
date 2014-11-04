<?php namespace CnesMeteo\User\Models;

use October\Rain\Auth\Models\Throttle as ThrottleBase;

class Throttle extends ThrottleBase
{
    /**
     * @var array Relations
     */
    public $belongsTo = [
        'user' => ['CnesMeteo\User\Models\User']
    ];
}
