<?php namespace CnesMeteo\Data\Models;

use October\Rain\Database\Model;
use CnesMeteo\User\Helpers as CnesMeteoHelpers;

class Snow extends MeasurementsBase
{
    /**
     * @var string The database table used by the model.
     */
    protected $table = 'cnesmeteo_data_snows';

    protected $primaryKey = 'measurement_id'; // extends the measurement info

    protected $value_units = 'm';
    protected $default_measured_at_datetime = 'Y-m-d 09:00:00';

    /**
     * Validation rules
     */
    public $rules = [
        'value' => 'required|numeric|min:0.0|max:200.0', // Value range (meters)
        'measured_at' => 'date_format:Y-m-d H:i:s',
        'comments' => 'max:140' // Comments max lenght
    ];

    // Format value data:
    public function getValueFormattedAttribute()
    {
        $current_value = $this->attributes['value'];
        $value_separator = ','; // TODO -> Localizate this by user language settings !!!!!!!!!!
        return number_format($current_value, 1, $value_separator, '').' '.$this->value_units;
    }
}
