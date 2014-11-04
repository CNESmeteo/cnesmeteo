<?php namespace CnesMeteo\User\Models;

use Form;
use Log;
use October\Rain\Database\Model;
use CnesMeteo\User\Helpers as CnesMeteoHelpers;
use October\Rain\Auth\Models\User as UserBase; // For the "permissions" handle functions !!!

class Classroom extends UserBase
{
    // use \October\Rain\Database\Traits\Validation; // Commented because it's defined in the parent Model

    /**
     * @var string The database table used by the model.
     */
    protected $table = 'cnesmeteo_user_classrooms';

    /**
     * Validation rules
     */
    public $rules = [
        'academic_year_start' => 'required|integer|between:2000,2050',
        'name' => 'required'
    ];

    /**
     * @var array Relations
     */
    public $belongsTo = [
        'organization' => ['CnesMeteo\User\Models\Organization', 'table' => 'cnesmeteo_user_organizations']
    ];
    public $belongsToMany = [
        'sites' => ['CnesMeteo\User\Models\Site', 'table' => 'cnesmeteo_user_classrooms_sites'],
        'users' => ['CnesMeteo\User\Models\User', 'table' => 'cnesmeteo_user_classrooms_users']
    ];


    public $attachMany = [
        'featured_images' => ['System\Models\File']
    ];

    /**
     * @var array The attributes that are mass assignable.
     */
    protected $fillable = ['name', 'academic_year_start'];

    /*
     * -----------------------------------------------------------------------------------------------------------------
     */


    /// @return Nº Users belongs to this Classroom
    public function getUsersCountAttribute()
    {
        if (!empty($this->id))
            return $this->users()->count();
        else
            return 0;
    }

    public function listOrganizations($keyValue = null, $fieldName = null)
    {
        return CnesMeteoHelpers::getOrganizationsList();
    }

    /**
     * @var array Cache for nameList() method
     */
    protected static $nameList = [];

    public static function getNameList($organizationId)
    {
        $output = [];
        if (isset(self::$nameList[$organizationId])) {
            $output = self::$nameList[$organizationId];
        }
        elseif (!empty($organizationId)) {
            $output = self::$nameList[$organizationId] = self::whereOrganizationId($organizationId)->lists('name', 'id');
        }
        return $output;
    }

    public static function formSelect($name, $organizationId = null, $selectedValue = null, $options = [])
    {
        return Form::select($name, self::getNameList($organizationId), $selectedValue, $options);
    }

}
