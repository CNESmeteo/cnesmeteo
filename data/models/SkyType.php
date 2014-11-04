<?php namespace CnesMeteo\Data\Models;

use October\Rain\Database\Model;
use CnesMeteo\User\Helpers as CnesMeteoHelpers;

class Skytype extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    protected $table = 'cnesmeteo_data_skytypes';
    public $timestamps = false; // Disable timestampts for this model

    /**
     * Validation rules
     */
    public $rules = [
        'name' => 'required|min:3'
    ];

    /**
     * @var array Relations
     */
    public $hasMany = [
        'measurements' => ['CnesMeteo\Data\Models\Sky', 'table' => 'cnesmeteo_data_skies']
    ];
    public $attachOne = [
        'featured_image' => ['System\Models\File']
    ];


    public function getTypeTranslatedAttribute()
    {
        $current_value = $this->attributes['type'];
        return CnesMeteoHelpers::getSkyTypeTranslatedEnumName($current_value);
    }

    public function listSkyTypeEnumNames()
    {
        return CnesMeteoHelpers::getSkyTypeEnumNamesList();
    }


    // Thumb sky image:
    public function getImageAttribute()
    {
        $image_link = 'Image not available'; // TODO -> Translate

        $featuredImage = $this->featured_image->getThumb(80, 80, ['mode' => 'crop']);
        if (!empty($featuredImage)){
            $image_link = '<img src="'.$featuredImage.'" alt="Sky Image" />';
        }

        return $image_link;
    }

}
