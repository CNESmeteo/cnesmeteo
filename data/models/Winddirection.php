<?php namespace CnesMeteo\Data\Models;

use October\Rain\Database\Model;
use CnesMeteo\User\Helpers as CnesMeteoHelpers;

class Winddirection extends MeasurementsBase
{
    /**
     * @var string The database table used by the model.
     */
    protected $table = 'cnesmeteo_data_winddirections';

    protected $primaryKey = 'measurement_id'; // extends the measurement info

    protected $value_units = '';
    protected $default_measured_at_datetime = 'Y-m-d 09:00:00';


    /**
     * Validation rules
     */
    public $rules = [
        'value' => 'required|in:N,NE,E,SE,S,SW,W,NW', // Value range
        'measured_at' => 'date_format:Y-m-d H:i:s',
        'comments' => 'max:140' // Comments max lenght
    ];

    // Format value data:
    public function getValueFormattedAttribute()
    {
        $current_value = $this->attributes['value'];
        return CnesMeteoHelpers::getWindDirectionTranslatedName($current_value);
    }

    public function listWinddirections()
    {
        return CnesMeteoHelpers::getWindDirectionsList();
    }

}
