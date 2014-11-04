<?php namespace CnesMeteo\User\Models;

use October\Rain\Database\Model;
use CnesMeteo\User\Helpers as CnesMeteoHelpers;

class Certification extends Model
{
    /**
     * @var string The database table used by the model.
     */
    protected $table = 'cnesmeteo_user_certifications';

    /**
     * Validation rules
     */
    public $rules = [
        'globeID' => 'required|min:8|max:8'
    ];

    /**
     * @var array Relations
     */
    public $belongsTo = [
        'user' => ['CnesMeteo\User\Models\User', 'table' => 'cnesmeteo_user_users', 'foreignKey' => 'user_id'],
        'checker' => ['CnesMeteo\User\Models\User', 'table' => 'cnesmeteo_user_users', 'foreignKey' => 'checker_id']
    ];


    /**
     * @var array The attributes that are mass assignable.
     */
    protected $fillable = ['globeID', 'certified'];


    // certified == true
    public function active(){
        return (bool)$this->certified;
    }

    /**
     * List all available users (not already certified, checked or not)
     */
    public function listUnCertificatedUsers($keyValue = null, $fieldName = null)
    {
        return CnesMeteoHelpers::getUnCertificatedUsersList();
    }

    public function getUserNameAttribute($keyValue = null, $fieldName = null)
    {
        return CnesMeteoHelpers::formatUser_LastName_FirstName($this->user);
    }

    public function getCheckerNameAttribute($keyValue = null, $fieldName = null)
    {
        return CnesMeteoHelpers::formatUser_LastName_FirstName($this->checker);
    }
}
