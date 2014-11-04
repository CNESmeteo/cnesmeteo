<?php namespace CnesMeteo\User\Controllers;

use October\Rain\Support\Facade\Auth as Auth;;
use Input;
use Flash;
use BackendMenu;
use BackendAuth;
use Backend\Classes\Controller;

class Certifications extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    public $requiredPermissions = ['cnesmeteo.access_certifications'];

    public $bodyClass = 'compact-container';


    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('CnesMeteo.User', 'user', 'certifications');
    }


    public function listExtendModel($model, $definition = null){

        // Add custom properties to the model: "classrooms_count"
        //$model['classrooms_count'] = $model->classrooms_count;
        return $model;
    }

    /**
     * Called after the creation or updating form is saved.
     * @param Model
     */
    public function formAfterSave($model)
    {
        // Update the "checker_id" field with the current user:
        $currentUser = Auth::getUser();
        if ( (!empty($currentUser)) && (!empty($currentUser->id)) ){
            $model->checker_id = $currentUser->id;
            $model->save();
        }
        return $model;
    }
}