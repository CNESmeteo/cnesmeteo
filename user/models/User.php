<?php namespace CnesMeteo\User\Models;

use October\Rain\Database\Model;
use Illuminate\Http\Request;
use October\Rain\Auth\Models\Group as GroupBase;
use October\Rain\Auth\Models\User as UserBase;
use RainLab\User\Models\Settings as UserSettings;
use CnesMeteo\User\Models\Group as Group;
use CnesMeteo\User\Helpers as CnesMeteoHelpers;
use CnesMeteo\Localization\Models\Country as Country;
use CnesMeteo\Localization\Models\State as State;
use CnesMeteo\Localization\Models\Province as Province;

class User extends UserBase
{
    /**
     * @var string The database table used by the model.
     */
    protected $table = 'backend_users';

    /**
     * Validation rules
     */
    public $rules = [
        'email' => 'required|between:6,128|email|unique:backend_users',
        'password' => 'required:create|between:6,128|confirmed',
        'password_confirmation' => 'required_with:password|between:6,128'
    ];

    /**
     * @var array Relations
     */
    public $belongsTo = [
        'language' => ['CnesMeteo\Localization\Models\Language', 'table' => 'cnesmeteo_localization_languages'],
        'country' => ['CnesMeteo\Localization\Models\Country', 'table' => 'rainlab_user_countries'],
        'state' => ['CnesMeteo\Localization\Models\State', 'table' => 'rainlab_user_states'],
        'province' => ['CnesMeteo\Localization\Models\Province', 'table' => 'cnesmeteo_localization_provinces']
    ];

    public $belongsToMany = [
        'groups' => ['CnesMeteo\User\Models\Group', 'table' => 'backend_users_groups', 'primaryKey' => 'user_id', 'foreignKey' => 'user_group_id'],
        'organizations' => ['CnesMeteo\User\Models\Organization', 'table' => 'cnesmeteo_user_organizations_users'],
        'classrooms' => ['CnesMeteo\User\Models\Classroom', 'table' => 'cnesmeteo_user_classrooms_users']
    ];

    public $hasOne  = [
        'certification' => ['CnesMeteo\User\Models\Certification', 'table' => 'cnesmeteo_user_certifications']
    ];

    public $attachOne = [
        'avatar' => ['System\Models\File']
    ];

    /**
     * @var array The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'login',
        'email',
        'password',
        'password_confirmation',
        'country',
        'state'
    ];

    /**
     * Purge attributes from data set.
     */
    protected $purgeable = ['password_confirmation', 'organization_id', 'classroom_id'];

    protected static $loginAttribute = 'login'; // 'email' or 'login'


    /**
     * Sends the confirmation email to a user, after activating
     * @param  string $code
     * @return void
     */
    public function attemptActivation($code)
    {
        $result = parent::attemptActivation($code);
        if ($result === false)
            return false;

        if (!$mailTemplate = UserSettings::get('welcome_template'))
            return;

        $data = [
            'name' => $this->name,
            'email' => $this->email
        ];

        Mail::send($mailTemplate, $data, function($message)
        {
            $message->to($this->email, $this->name);
        });
    }



    /**
     * Returns the public image file path to this user's avatar.
     */
    public function getAvatarThumb($size = 25, $default = null)
    {
        if ($this->avatar)
            return $this->avatar->getThumb($size, $size);
        else
            return '//www.gravatar.com/avatar/' . md5(strtolower(trim($this->email))) . '?s='.$size.'&d='.urlencode($default);
    }



    /**
     * Custom getters
     */

    public function getCountryOptions()
    {
        return Country::getNameList();
    }

    public function getStateOptions()
    {
        return State::getNameList($this->country_id);
    }

    public function getProvinceOptions()
    {
        return Province::getNameList($this->state_id);
    }


    public function getUserNameAttribute($keyValue = null, $fieldName = null)
    {
        return CnesMeteoHelpers::formatUser_LastName_FirstName($this);
    }
    public function getOrganizationNameAttribute($keyValue = null, $fieldName = null)
    {
        $result = 'None'; // TODO: Translate

        if (!empty($this->id)){
            $classlist = implode(",", $this->organizations()->lists('name'));
            if (!empty($classlist)){
                $result = $classlist;
            }
        }

        return $result;
    }
    public function getGroupsNameAttribute($keyValue = null, $fieldName = null)
    {
        $result = 'None'; // TODO: Translate

        if (!empty($this->id)){
            $classlist = implode(",", $this->groups()->lists('name'));
            if (!empty($classlist)){
                $result = $classlist;
            }
        }

        return $result;
    }

    // Get all classrooms the user belongs to and display it as a string
    // "ClassName 1, ClassName 2, ClassName 3, ..., ClassName N"
    public function getClassroomListAttribute()
    {
        $result = 'None'; // TODO: Translate

        if (!empty($this->id)){
            $classlist = implode(",", $this->classrooms()->lists('name'));
            if (!empty($classlist)){
                $result = $classlist;
            }
        }

        return $result;
    }

    /**
     * Checks if the user HAS (checked or not) a GLOBE certification
     */
    public function hasCertification()
    {
        // TODO -> PROBAR!!!! (probar "count($this->certification);"
        return count($this->certification()->get());
    }

    /**
     * Checks if the user don't have any GLOBE certification
     */
    public function dontHaveCertification()
    {
        // TODO -> PROBAR!!!!
        return count($this->certification()->get()) === 0;
    }

    /**
     * Checks if the user HAS and it's CHECKED a GLOBE certification
     */
    public function isCertified()
    {
        // TODO -> PROBAR!!!! (improve query!!!)
        return count($this->certification()->get()->active());
    }


    public function listGroups($keyValue = null, $fieldName = null)
    {
        //dd($keyValue, $fieldName, $this->groups()->first()->id);
        return CnesMeteoHelpers::getGroupsList();
    }

}
