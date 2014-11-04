<?php namespace CnesMeteo\Data\Models;

use Carbon\Carbon;
use CnesMeteo\User\Models\User as CnesMeteoUser;
use CnesMeteo\User\Models\Organization as CnesMeteoOrganization;
use CnesMeteo\User\Models\Site as CnesMeteoSite;
use October\Rain\Database\Model;
use CnesMeteo\User\Helpers as CnesMeteoHelpers;

class MeasurementsBase extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\Purgeable;
    public $timestamps = false; // Disable timestampts for this model

    protected $touches = array('measurement'); // Update "parent" model timestampts
    protected $purgeable = ['comments', 'measured_at'];
    protected $dates = ['measured_at'];
    protected $default_measured_at_datetime = 'Y-m-d 09:00:00';

    /**
     * @var array Relations
     */
    public $belongsTo = [
        'measurement' => ['CnesMeteo\Data\Models\Measurement', 'table' => 'cnesmeteo_data_measurements']
    ];


    public function getModelID()
    {
        $modelID = 0;
        if (!empty($this)) {
            if (!empty($this->measurement_id)) {
                $modelID = $this->measurement_id;
            }
            elseif (!empty($this->id)) {
                $modelID = $this->id;
            }
        }
        return $modelID;
    }



    // User data:
    public function getUserAttribute()
    {
        $user_obj = null;
        $modelID = $this->getModelID();
        if ( (!empty($this)) && (!empty($modelID)) )
        {
            $user_id = Measurement::find($modelID)->user_id;
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


    // Organization data:
    public function getOrganizationAttribute()
    {
        $organization_obj = null;
        $modelID = $this->getModelID();
        if ( (!empty($this)) && (!empty($modelID)) )
        {
            $organization_id = Measurement::find($modelID)->organization_id;
            if (!empty($organization_id)){
                $organization_obj = CnesMeteoOrganization::findOrFail($organization_id);
            }
        }
        return $organization_obj;
    }
    public function getOrganizationNameAttribute()
    {
        $name = '';
        $organization_obj = $this->getOrganizationAttribute();
        if ( (!empty($organization_obj)) && (!empty($organization_obj->name)) ){
            $name = $organization_obj->name;
        }
        return $name;
    }


    // Organization data:
    public function getSiteAttribute()
    {
        $site_obj = null;
        $modelID = $this->getModelID();
        if ( (!empty($this)) && (!empty($modelID)) )
        {
            $site_id = Measurement::find($modelID)->site_id;
            if (!empty($site_id)){
                $site_obj = CnesMeteoSite::findOrFail($site_id);
            }
        }
        return $site_obj;
    }
    public function getSiteNameAttribute()
    {
        $name = '';
        $site_obj = $this->getSiteAttribute();
        if ( (!empty($site_obj)) && (!empty($site_obj->name)) ){
            $name = $site_obj->name;
        }
        return $name;
    }


    // Measured_at timestampt: 09:00AM; 12:00AM (Noon); Custom
    public function getMeasuredAtAttribute()
    {
        $dt = new \DateTime();
        $measurement_datetime_str =  $dt->format($this->default_measured_at_datetime);
        $modelID = $this->getModelID();

        if ( (!empty($this)) && (!empty($modelID)) )
        {
            $measurement_obj = Measurement::find($modelID);
            if ( (!empty($measurement_obj->measured_at))
                && (strtotime($measurement_obj->measured_at) > 0) )
            {
                $measurement_datetime_str = $measurement_obj->measured_at;
            }
        }
        return $measurement_datetime_str;
    }

    public function getMeasuredDatetimeAttribute()
    {
        $measurement_datetime_str = 'Unknown';
        $modelID = $this->getModelID();

        if ( (!empty($this)) && (!empty($modelID)) )
        {
            $measurement_obj = Measurement::find($modelID);
            if (!empty($measurement_obj->measured_at))
            {
                $measurement_datetime_str = $measurement_obj->measured_at;
            }
        }
        return $measurement_datetime_str;
    }

    public function getCommentsAttribute()
    {
        $comments_str =  '';
        $modelID = $this->getModelID();

        if ( (!empty($this)) && (!empty($modelID)) )
        {
            $measurement_obj = Measurement::find($modelID);
            if ( (!empty($measurement_obj->comments)) )
            {
                $comments_str = $measurement_obj->comments;
            }
        }
        return $comments_str;
    }
}
