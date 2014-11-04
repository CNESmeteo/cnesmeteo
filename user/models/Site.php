<?php namespace CnesMeteo\User\Models;

use Illuminate\Http\Request;
use October\Rain\Database\Model;
use CnesMeteo\User\Helpers as CnesMeteoHelpers;
use October\Rain\Database\Traits\Purgeable;
use Input;

class Site extends Model
{
    /**
     * @var string The database table used by the model.
     */
    protected $table = 'cnesmeteo_user_sites';

    /**
     * Validation rules
     */
    public $rules = [
        'address' => 'required|min:2',
        'name' => 'required|min:2'
    ];

    /**
     * @var array Relations
     */
    public $belongsTo = [
        'organization' => ['CnesMeteo\User\Models\Organization', 'table' => 'cnesmeteo_user_organizations']
    ];
    public $belongsToMany = [
        'classrooms' => ['CnesMeteo\User\Models\Classroom', 'table' => 'cnesmeteo_user_classrooms_sites']
    ];
    public $attachMany = [
        'featured_images' => ['System\Models\File']
    ];

    /**
     * @var array The attributes that are mass assignable.
     */
    protected $fillable = ['name', 'address', 'organization_id'];

    /**
     * Purge attributes from data set.
     */
    protected $purgeable = ['classroom_id'];



    /// @return Nº Classrooms belongs to this Site
    public function getClassroomsCountAttribute()
    {
        // Pivot table
        if (!empty($this->id))
            return $this->classrooms()->count();
        else
            return 0;
    }

    public function listOrganizations($keyValue = null, $fieldName = null)
    {
        return CnesMeteoHelpers::getOrganizationsList();
    }


}
