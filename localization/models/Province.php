<?php namespace CnesMeteo\Localization\Models;

use Form;
use Model;

/**
 * State Model
 */
class Province extends Model
{

    /**
     * @var string The database table used by the model.
     */
    public $table = 'cnesmeteo_localization_provinces';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = ['name', 'code'];

    /**
     * @var array Validation rules
     */
    public $rules = [
        'name' => 'required',
        'code' => 'required',
    ];

    /**
     * @var array Relations
     */
    public $belongsTo = [
        'state' => ['CnesMeteo\Localization\Models\State']
    ];

    /**
     * @var bool Indicates if the model should be timestamped.
     */
    public $timestamps = false;


    /**
     * @var array Cache for nameList() method
     */
    protected static $nameList = [];

    public static function getNameList($stateId)
    {
        if (isset(self::$nameList[$stateId]))
            return self::$nameList[$stateId];

        return self::$nameList[$stateId] = self::whereStateId($stateId)->lists('name', 'id');
    }

    public static function formSelect($name, $stateId = null, $selectedValue = null, $options = [])
    {
        return Form::select($name, self::getNameList($stateId), $selectedValue, $options);
    }

}