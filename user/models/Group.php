<?php namespace CnesMeteo\User\Models;

use October\Rain\Database\Model;
use October\Rain\Auth\Models\Group as GroupBase;
use CnesMeteo\User\Helpers as CnesMeteoHelpers;


class Group extends GroupBase
{
    public $sessionKey = 'cnesmeteo_auth';

    public $userModel = 'CnesMeteo\User\Models\User';

    public $groupModel = 'CnesMeteo\User\Models\Group';

    public $throttleModel = 'CnesMeteo\User\Models\Throttle';


    /**
     * @var string The database table used by the model.
     */
    protected $table = 'backend_user_groups';

    /**
     * @var array Validation rules
     */
    public $rules = [
        'name' => 'required|between:4,32|unique:backend_user_groups',
    ];

    /**
     * @var array Relations
     */
    /*
    public $hasMany = [
        'users' => ['CnesMeteo\User\Models\User', 'table' => 'backend_users_groups']
    ];
    */
    public $belongsToMany = [
        'users' => ['CnesMeteo\User\Models\User', 'table' => 'backend_users_groups']
    ];

}