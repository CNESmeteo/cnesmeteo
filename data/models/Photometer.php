<?php namespace CnesMeteo\Data\Models;

use October\Rain\Database\Model;
use CnesMeteo\User\Helpers as CnesMeteoHelpers;

class Photometer extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    protected $table = 'cnesmeteo_data_photometers';
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
        'measurements' => ['CnesMeteo\Data\Models\Aerosol', 'table' => 'cnesmeteo_data_aerosols']
    ];
    public $attachOne = [
        'featured_image' => ['System\Models\File']
    ];

    // Thumb sky image:
    public function getImageAttribute()
    {
        $image_link = 'Image not available'; // TODO -> Translate

        $featuredImage = $this->featured_image->getThumb(80, 80, ['mode' => 'crop']);
        if (!empty($featuredImage)){
            $image_link = '<img src="'.$featuredImage.'" alt="Cloud Image" />';
        }

        return $image_link;
    }

    public function  getListNames()
    {
        return $this->lists('name', 'id');
    }

}
