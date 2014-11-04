<?php namespace CnesMeteo\Data\Models;

use CnesMeteo\User\Helpers as CnesMeteoHelpers;

class Sky extends MeasurementsBase
{
    /**
     * @var string The database table used by the model.
     */
    protected $table = 'cnesmeteo_data_skies';

    protected $primaryKey = 'measurement_id'; // extends the measurement info

    protected $value_units = '';
    protected $default_measured_at_datetime = 'Y-m-d 09:00:00';

    /**
     * Purge attributes from data set.
     */
    //protected $purgeable = ['[Skies]user_id', '[Skies]organization_id', '[Skies]site_id'];


    /**
     * @var array Relations
     */
    public $belongsTo = [
        'measurement' => ['CnesMeteo\Data\Models\Measurement', 'table' => 'cnesmeteo_data_measurements'],
        'skytype' => ['CnesMeteo\Data\Models\Skytype', 'table' => 'cnesmeteo_data_skytypes']
    ];


    /**
     * Validation rules
     */
    public $rules = [
        //'photometer_id' => 'required|exists:cnesmeteo_data_skytypes',
        'value' => 'required|min:-40.0|max:80.0', // Value range (ºC)
        'measured_at' => 'date_format:Y-m-d H:i:s',
        'comments' => 'max:140' // Comments max lenght
    ];

    // Format value data:
    public function getValueFormattedAttribute()
    {
        // return $this->skytype->name;

        /*
         * To display the SkyType Enum translated Name:
         * --------------------------------------------
         */
        $current_value = $this->skytype->type;
        return CnesMeteoHelpers::getSkyTypeTranslatedEnumName($current_value);
    }

    public function listSkyTypeValues()
    {
        return CnesMeteoHelpers::getSkyTypeValuesList();
    }

    // Thumb sky image:
    public function getImageThumbAttribute()
    {
        $image_link = 'Image not available'; // TODO -> Translate

        $featuredImage = $this->skytype->featured_image->getThumb(80, 80, ['mode' => 'crop']);
        if (!empty($featuredImage)){
            $image_link = '<img src="'.$featuredImage.'" alt="Sky Image" />';
        }

        return $image_link;
    }

}
