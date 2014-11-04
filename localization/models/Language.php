<?php namespace CnesMeteo\Localization\Models;

use Model;

/**
 * Country Model
 */
class Language extends Model
{

    /**
     * @var string The database table used by the model.
     */
    public $table = 'cnesmeteo_localization_languages';

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
        'code' => 'unique:cnesmeteo_localization_languages',
    ];

    /**
     * @var bool Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    public function getListEnabled(){
        return $this;
    }

}