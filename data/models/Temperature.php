<?php namespace CnesMeteo\Data\Models;


class Temperature extends MeasurementsBase
{
    /**
     * @var string The database table used by the model.
     */
    protected $table = 'cnesmeteo_data_temperatures';

    protected $primaryKey = 'measurement_id'; // extends the measurement info

    protected $value_units = 'ºC';

    /**
     * Validation rules
     */
    public $rules = [
        'value' => 'required|numeric|min:-40.0|max:80.0', // Value range (ºC)
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
