<?php namespace CnesMeteo\User\Models;

use Log;
use Form;
use October\Rain\Database\Model;
use CnesMeteo\User\Helpers as CnesMeteoHelpers;
use CnesMeteo\Localization\Models\Language as CnesMeteoLanguage;


class Organization extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    protected $table = 'cnesmeteo_user_organizations';

    /**
     * Validation rules
     */
    public $rules = [
        'RNE' => 'between:6,8|unique:cnesmeteo_user_organizations',
        'name' => 'required|min:4',
        'email' => 'required|email',
        'website' => 'url'
    ];

    /**
     * @var array Relations
     */
    public $belongsTo = [
        'language' => ['CnesMeteo\Localization\Models\Language', 'table' => 'cnesmeteo_localization_languages'],
        'country' => ['CnesMeteo\Localization\Models\Country', 'table' => 'cnesmeteo_localization_countries'],
        'state' => ['CnesMeteo\Localization\Models\State', 'table' => 'cnesmeteo_localization_states'],
        'province' => ['CnesMeteo\Localization\Models\Province', 'table' => 'cnesmeteo_localization_provinces']
    ];

    public $hasMany = [
        'classrooms' => ['CnesMeteo\User\Models\Classroom', 'table' => 'cnesmeteo_user_classrooms'],
        'sites' => ['CnesMeteo\User\Models\Site', 'table' => 'cnesmeteo_user_sites']
    ];
    public $belongsToMany = [
        'users' => ['CnesMeteo\User\Models\User', 'table' => 'cnesmeteo_user_organizations_users']
    ];

    public $morphOne  = [
        'globe' => ['CnesMeteo\User\Models\Globe', 'name' => 'certifiable']
    ];
    /*
     * Put this in case the above $morphTo definition doesn't work !!!!!!
    public function globe(){
        return $this->morphOne('CnesMeteo\User\Models\Globe', 'certifiable');
    }
    */

    public $attachOne = [
        'avatar' => ['System\Models\File']
    ];
    public $attachMany = [
        'featured_images' => ['System\Models\File']
    ];

    /**
     * @var array The attributes that are mass assignable.
     */
    protected $fillable = ['name', 'email', 'address', 'phone', 'location'];

    /**
     * Returns the public image file path to this user's avatar.
     */
    public function getAvatarThumb($size = 25, $default = null)
    {
        if ($this->avatar)
            return $this->avatar->getThumb($size, $size);
        else
            return null;
    }

    /**
     * List all available organization types (mysql enum)
     */
    public static  function listOrganizationTypes($keyValue = null, $fieldName = null)
    {
        return CnesMeteoHelpers::getOrganizationsTypes();
    }

    public static function listLanguages($keyValue = null, $fieldName = null)
    {
        return CnesMeteoHelpers::getLanguagesList();
    }


    public function getUsersCountAttribute()
    {
        if (!empty($this->id))
            return $this->users()->count();
        else
            return 0;
    }

    public function getClassroomsCountAttribute()
    {
        if (!empty($this->id))
            return $this->classrooms()->count();
            // return $this->hasMany('CnesMeteo\User\Models\Classroom')->whereOrganizationId($this->id)->count();
            // return $this->classrooms()->where('organization_id', '=', $this->id)->count();
        else
            return 0;
    }

    /*
     * @return true if Organization has NO users of the given userGroup name
     */
    public function hasOrganization_any_UserInGroup($groups = ['Teacher'])
    {
        $result = false;

        $users = $this->users();

        if ($users->count() == 0) {
            Log::info('hasOrganization_any_UserInGroup - result - By user count');
            $result = true;
        } else {
            foreach($users as $user) {
                foreach($groups as $group) {
                    if ($user->inGroup($group)) {
                        Log::info('hasOrganization_any_UserInGroup - result - By user in group', [$group, $groups]);
                        $result = true;
                        break;
                    }
                }
                if ($result === true) break;
            }
        }
        return $result;
    }

    /**
     * Attempts to activate the given organization by checking the activate code. If the organization is activated already, an Exception is thrown.
     * @param string $activationCode
     * @return bool
     */
    public function attemptActivation($activationCode)
    {
        if ($this->is_activated)
            throw new Exception('Organization is already active!');

        if ($activationCode == $this->activation_code) {
            $this->activation_code = null;
            $this->is_activated = true;
            $this->activated_at = $this->freshTimestamp();
            return $this->forceSave();
        }

        return false;
    }

    /**
     * Get an activation code for the given organization.
     * @return string
     */
    public function getActivationCode()
    {
        $this->activation_code = $activationCode = CnesMeteoHelpers::getRandomString();

        $this->forceSave();

        return $activationCode;
    }


    /**
     * @var array Cache for nameList() method
     */
    protected static $nameList = null;

    public static function getNameList()
    {
        if (self::$nameList)
            return self::$nameList;

        return self::$nameList = self::isEnabled()->lists('name', 'id');
    }

    public static function formSelect($name, $selectedValue = null, $options = [])
    {
        return Form::select($name, self::getNameList(), $selectedValue, $options);
    }

    public function scopeIsEnabled($query)
    {
        return $query->where('is_activated', true);
    }

}
