<?php namespace CnesMeteo\Data\Models;

use CnesMeteo\User\Models\User as CnesMeteoUser;
use CnesMeteo\User\Models\Organization as CnesMeteoOrganization;
use CnesMeteo\User\Models\Site as CnesMeteoSite;
use October\Rain\Database\Model;
use CnesMeteo\User\Helpers as CnesMeteoHelpers;

class Measurement extends Model
{
    /**
     * @var string The database table used by the model.
     */
    protected $table = 'cnesmeteo_data_measurements';
    protected $default_measured_at_datetime = 'Y-m-d 09:00:00';

    /**
     * Validation rules
     */
    public $rules = [
        'type' => 'required|in:temperature,pressure,humidity,snow,rainfall,sky,clouds,windforce,winddirection', // <-- enum
        'measured_at' => 'date_format:Y-m-d H:i:s',
        'comments' => 'max:140' // Comments max lenght
    ];

    /**
     * @var array Relations
     */
    public $belongsTo = [
        'user' => ['CnesMeteo\User\Models\User', 'table' => 'cnesmeteo_user_users'],
        'organization' => ['CnesMeteo\User\Models\Organization', 'table' => 'cnesmeteo_user_organizations'],
        'site' => ['CnesMeteo\User\Models\Site', 'table' => 'cnesmeteo_user_sites']
    ];
    public $hasOne = [
        'temperature' => ['CnesMeteo\Data\Models\Temperature', 'table' => 'cnesmeteo_data_temperatures'],
        'pressure' => ['CnesMeteo\Data\Models\Pressure', 'table' => 'cnesmeteo_data_pressures'],
        'snow' => ['CnesMeteo\Data\Models\Snow', 'table' => 'cnesmeteo_data_snows'],
        'precipitation' => ['CnesMeteo\Data\Models\Precipitation', 'table' => 'cnesmeteo_data_precipitations'],
        'humidity' => ['CnesMeteo\Data\Models\Humidity', 'table' => 'cnesmeteo_data_humidities'],
        'windforce' => ['CnesMeteo\Data\Models\Windforce', 'table' => 'cnesmeteo_data_windforces'],
        'winddirection' => ['CnesMeteo\Data\Models\Winddirection', 'table' => 'cnesmeteo_data_winddirections'],
        'sky' => ['CnesMeteo\Data\Models\Sky', 'table' => 'cnesmeteo_data_skies']
    ];
    public $belongsToMany = [
        'clouds' => ['CnesMeteo\Data\Models\Cloudtype', 'table' => 'cnesmeteo_data_measurements_cloudtypes',
                     'foreignKey' => 'cloudtype_id'],
        /*
        'skies' => ['CnesMeteo\Data\Models\Skytype', 'table' => 'cnesmeteo_data_measurements_skytypes',
                    'foreignKey' => 'skytype_id']
        */
    ];

    /*
     * Alternative code?
    public function skies()
    {
        return $this->belongsToMany('CnesMeteo\Data\Models\Sky', 'cnesmeteo_data_measurements_skytypes', 'measurement_id', 'skytype_id');
    }
    */


    public function getModelID()
    {
        return $this->id;
    }

    // User data:
    public function getUserAttribute()
    {
        $user_obj = null;
        $modelID = $this->getModelID();
        if ( (!empty($this)) && (!empty($modelID)) )
        {
            $user_id = $this->user_id;
            if (!empty($user_id)){
                $user_obj = CnesMeteoUser::findOrFail($user_id);
            }
        }
        return $user_obj;
    }
    public function getUserNameAttribute()
    {
        $name = '';
        $user_obj = $this->getUserAttribute();
        if (!empty($user_obj)){
            $name = CnesMeteoHelpers::formatUser_LastName_FirstName($user_obj);
        }
        return $name;
    }


    // Measured_at timestampt: 09:00AM; 12:00AM (Noon); Custom
    public function getMeasuredAtAttribute()
    {
        $dt = new \DateTime();
        $measurement_datetime_str =  $dt->format($this->default_measured_at_datetime);
        $modelID = $this->getModelID();

        if ( (!empty($this)) && (!empty($modelID))
            && (!empty($this->measured_at))
            && (strtotime($this->measured_at) > 0))
        {
            $measurement_datetime_str = $this->measured_at;
        }
        return $measurement_datetime_str;
    }

    public function getMeasuredDatetimeAttribute()
    {
        $measurement_datetime_str = 'Unknown';
        $modelID = $this->getModelID();

        if ( (!empty($this)) && (!empty($modelID))
            && !empty($this->measured_at))
        {
            $measurement_datetime_str = $this->measured_at;
        }
        return $measurement_datetime_str;
    }

    public function getCommentsAttribute()
    {
        $comments_str = '';
        $modelID = $this->getModelID();

        if ( (!empty($this)) && (!empty($modelID))
            && (!empty($this->attributes['comments'])) )
        {
            $comments_str = $this->attributes['comments'];
        }
        return $comments_str;
    }

}
