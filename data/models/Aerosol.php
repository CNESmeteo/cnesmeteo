<?php namespace CnesMeteo\Data\Models;

use CnesMeteo\User\Models\User as CnesMeteoUser;
use CnesMeteo\User\Models\Organization as CnesMeteoOrganization;
use CnesMeteo\User\Models\Site as CnesMeteoSite;
use October\Rain\Database\Model;
use CnesMeteo\User\Helpers as CnesMeteoHelpers;

class Aerosol extends MeasurementsBase
{
    /**
     * @var string The database table used by the model.
     */
    protected $table = 'cnesmeteo_data_aerosols';
    protected $primaryKey = 'measurement_id'; // extends the measurement info

    protected $value_units = '';

    // TODO -> purgeable AOT || Voltage values --> Make sure are NULL values if empty !!!!!!


    /**
     * @var array Relations
     */
    public $belongsTo = [
        'measurement' => ['CnesMeteo\Data\Models\Measurement', 'table' => 'cnesmeteo_data_measurements'],
        'photometer' => ['CnesMeteo\Data\Models\Photometer', 'table' => 'cnesmeteo_data_photometers']
    ];

    /**
     * Validation rules
     */
    public $rules = [
        //'photometer_id' => 'required|exists:cnesmeteo_data_photometers',
        'aot_red' => 'numeric|min:0.0|max:4.0',
        'aot_green' => 'numeric|min:0.0|max:4.0',
        'aot_blue' => 'numeric|min:0.0|max:4.0',
        'measured_at' => 'date_format:Y-m-d H:i:s',
        'comments' => 'max:140' // Comments max length
    ];

    // Format value data:
    public function getValueFormattedAttribute()
    {
        // Generate a string with the AOT values || Voltage values
        $value = '';

        if ( (!empty($this->attributes['aot_red']))
            || (!empty($this->attributes['aot_green']))
            || (!empty($this->attributes['aot_blue'])) )
        {
            if (!empty($this->attributes['aot_red'])) {
                $value .= 'AOT-RED = '.$this->format_Values( $this->attributes['aot_red'] ).'<br>';
            }
            if (!empty($this->attributes['aot_green'])) {
                $value .= 'AOT-GREEN = '.$this->format_Values( $this->attributes['aot_green'] ).'<br>';
            }
            if (!empty($this->attributes['aot_blue'])) {
                $value .= 'AOT-BLUE = '.$this->format_Values( $this->attributes['aot_blue'] ).'<br>';
            }
        }
        elseif( (!empty($this->attributes['voltage_temperature']))
            || (!empty($this->attributes['voltage_light']))
            || (!empty($this->attributes['voltage_dark'])) )
        {
            if (!empty($this->attributes['voltage_temperature'])) {
                $value .= 'Temperature = '.$this->format_Values( $this->attributes['voltage_temperature'] ).'<br>';
            }
            if (!empty($this->attributes['voltage_light'])) {
                $value .= 'Voltage Light = '.$this->format_Values( $this->attributes['voltage_light'] ).'<br>';
            }
            if (!empty($this->attributes['voltage_dark'])) {
                $value .= 'Voltage Dark = '.$this->format_Values( $this->attributes['voltage_dark'] ).'<br>';
            }
        }

        return $value;
    }

    private function format_Values($value){
        $value_separator = ','; // TODO -> Localizate this by user language settings !!!!!!!!!!
        return number_format($value, 2, $value_separator, '').' '.$this->value_units;
    }


    /**
     * List for all dropdowns
     */
    public function listObservedSkyColors($keyValue = null, $fieldName = null)
    {
        return CnesMeteoHelpers::getObservedSkyColorsList();
    }

    public function listObservedSkyClarity($keyValue = null, $fieldName = null)
    {
        return CnesMeteoHelpers::getObservedSkyClarityList();
    }

    // Thumb photometer image:
    public function getImageThumbAttribute()
    {
        $image_link = 'Image not available'; // TODO -> Translate

        $featuredImage = $this->photometer->featured_image->getThumb(80, 80, ['mode' => 'crop']);
        if (!empty($featuredImage)){
            $image_link = '<img src="'.$featuredImage.'" alt="Photometer Image" />';
        }

        return $image_link;
    }
}
