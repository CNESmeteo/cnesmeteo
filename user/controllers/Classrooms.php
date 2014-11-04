<?php namespace CnesMeteo\User\Controllers;

use CnesMeteo\User\Helpers as CnesMeteoHelpers;
use Input;
use Flash;
use BackendMenu;
use BackendAuth;
use Backend\Classes\Controller;
use CnesMeteo\User\Models\Classroom as ClassroomModel;

class Classrooms extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',
        'Backend.Behaviors.RelationController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';
    public $relationConfig = 'config_relation.yaml';

    public $requiredPermissions = ['cnesmeteo.access_classrooms'];
    public $currentUser_backendModel = null;
    public $currentUser_accessLevel = null;

    public $bodyClass = 'compact-container';

    public function __construct()
    {
        parent::__construct();

        // Get Current Backend User
        $this->currentUser_backendModel = BackendAuth::getUser();
        // Set Access Level
        $this->currentUser_accessLevel = CnesMeteoHelpers::getUserAccessLevel($this->currentUser_backendModel);

        BackendMenu::setContext('CnesMeteo.User', 'user', 'classrooms');
    }


    public function listExtendModel($model, $definition = null){

        // Add custom properties to the model: "users_count"
        $model['users_count'] = $model->users_count;

        return $model;
    }



    /**
     * Add an existing related model to the primary model
     */
    public function onRelationManageSync()
    {
        $modelID = intval($this->params[0]);
        $model = ClassroomModel::findOrFail($modelID);

        if (($checkedIds = post('checked')) && is_array($checkedIds)) {
            $model->sites()->sync($checkedIds);
        }

        $this->initRelation($model);
        $relatedModel = $this->getClassExtension('Backend.Behaviors.RelationController');

        //$div_to_update_ID = $relatedModel->relationGetId('view');
        $div_to_update_ID = "Classrooms-update-RelationController-sites";

        return ['#'.$div_to_update_ID => $relatedModel->relationRender('sites')];
    }



    /**
     * Add available permission fields to the Classroom form.
     */
    protected function formExtendFields($host)
    {
        $permissionFields = [];
        foreach (BackendAuth::listPermissions() as $permission) {

            $fieldName = 'permissions['.$permission->code.']';

            if ( (CnesMeteoHelpers::startsWith(strtolower($permission->code), 'cnesmeteo.access_data'))
                && (strtolower($permission->code) != 'cnesmeteo.access_data_measurements')
                && (strtolower($permission->code) != 'cnesmeteo.access_data_skytypes')
                && (strtolower($permission->code) != 'cnesmeteo.access_data_photometers')
                && (strtolower($permission->code) != 'cnesmeteo.access_data_globe') )
            {
                $fieldConfig = [
                    'label' => str_replace("Data - Manage ", "", $permission->label),
                    'comment' => $permission->comment,
                    'type' => 'switch' //  checkbox || switch
                ];

                if (isset($permission->tab))
                    $fieldConfig['tab'] = 'Permissions'; // TODO: Text Localization!!!

                $permissionFields[$fieldName] = $fieldConfig;
            }
        }

        $host->addSecondaryTabFields($permissionFields);
    }

    /*
     * Filter list by the User access level
     */
    public function listExtendQuery($query, $definition = null){

        // EXAMPLE: $query->where('assigned_to', '=', $user->id);
        $classrooms_ids = [];

        switch($this->currentUser_accessLevel)
        {
            case 1: //                      TEACHERS
                // -----------------------------------------------------------------------
                //              Show only users in the same Classroom(s)
                // -----------------------------------------------------------------------

                // 1) Get ALL classrooms the current user belongs to
                $classrooms_ids = CnesMeteoHelpers::getClassroomsRelated_for_Filtering($this->currentUser_backendModel, true);
                break;

            case 2: //                      MANAGERS
                // -----------------------------------------------------------------------
                //              Show only users in the same Organization(s)
                // -----------------------------------------------------------------------

                // 1) Get ALL organizations the current user belongs to
                $organizations = CnesMeteoHelpers::getOrganizationsRelated_for_Filtering($this->currentUser_backendModel);
                // 2) Get ALL classroom_ids of all related organizations
                $classrooms_ids = CnesMeteoHelpers::getOrganizationsClassroomsIDs_Filtered($organizations);
                break;
        }

        if ($this->currentUser_accessLevel < 3)
        {
            // Because whereIn generates error with an empty array
            // https://github.com/laravel/framework/issues/5296
            if (empty($classrooms_ids)) {
                $classrooms_ids = ['0'];
            };
            // Filter the query for the list:
            $query->whereIn( 'id', $classrooms_ids );
        }
    }

}