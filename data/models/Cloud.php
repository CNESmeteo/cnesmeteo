<?php namespace CnesMeteo\Data\Models;

use CnesMeteo\User\Models\User as CnesMeteoUser;
use CnesMeteo\User\Models\Organization as CnesMeteoOrganization;
use CnesMeteo\User\Models\Site as CnesMeteoSite;
use October\Rain\Database\Model;
use CnesMeteo\User\Helpers as CnesMeteoHelpers;

class Cloud extends MeasurementsBase
{
    /**
     * @var string The database table used by the model.
     */
    protected $table = 'cnesmeteo_data_clouds';
    protected $primaryKey = 'measurement_id'; // extends the measurement info

    protected $jsonable = ['value'];

    /**
     * Validation rules
     */
    public $rules = [
        'value' => 'required', // json string
        'measured_at' => 'date_format:Y-m-d H:i:s',
        'comments' => 'max:140' // Comments max length
    ];

    // Format value data:
    public function getValueFormattedAttribute()
    {
        // TODO
        return 'Cloud types list'; // HTML use <br> !!!!
    }


    /**
     * List for all dropdowns
     */
    public function listSkyStates($keyValue = null, $fieldName = null)
    {
        return CnesMeteoHelpers::getSkyStatesList();
    }

    public function listCloudsCoverPercent($keyValue = null, $fieldName = null)
    {
        return CnesMeteoHelpers::getCloudsCoverPercentList();
    }

    public function listContrailsVisibility($keyValue = null, $fieldName = null)
    {
        return CnesMeteoHelpers::getContrailsVisibilityList();
    }

    public function listContrailsPercent($keyValue = null, $fieldName = null)
    {
        return CnesMeteoHelpers::getContrailsPercentList();
    }
}
