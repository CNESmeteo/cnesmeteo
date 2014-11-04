<?php namespace CnesMeteo\User\Controllers;

use CnesMeteo\User\Helpers as CnesMeteoHelpers;
use CnesMeteo\User\Modules\Backend\FormWidgets\OrgClassDropdown;
use CnesMeteo\User\Modules\Backend\FormWidgets\ManageLocation;
use CnesMeteo\User\Modules\Backend\FormWidgets\ManageCoordinates;
use Input;
use Flash;
use BackendMenu;
use BackendAuth;
use Backend\Classes\Controller;
use System\Classes\ApplicationException;
use CnesMeteo\User\Models\Site as SiteModel;

class Sites extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',
        'Backend.Behaviors.RelationController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';
    public $relationConfig = 'config_relation.yaml';

    public $requiredPermissions = ['cnesmeteo.access_sites'];
    public $currentUser_backendModel = null;
    public $currentUser_accessLevel = null;

    public $bodyClass = 'compact-container';

    // Widgets
    public $OrgClassWidget = null;
    public $ManageLocationWidget = null;
    public $ManageCoordinatesWidget = null;
    public $inputsGroupedBy = 'CnesMeteoSite';

    public function __construct()
    {
        parent::__construct();

        // Get Current Backend User
        $this->currentUser_backendModel = BackendAuth::getUser();
        // Set Access Level
        $this->currentUser_accessLevel = CnesMeteoHelpers::getUserAccessLevel($this->currentUser_backendModel);

        BackendMenu::setContext('CnesMeteo.User', 'user', 'sites');
    }


    /*
    public function index()
    {
        //
        // Do any custom code here
        //

        // Call the ListController behavior index() method
        $this->getClassExtension('Backend.Behaviors.ListController')->index();
    }
    */

    public function create()
    {
        // Widgets: (create and bind)
        $this->OrgClassWidget = new OrgClassDropdown($this);
        $this->OrgClassWidget->alias = 'dropdownorgclass';
        $this->OrgClassWidget->form_group = $this->inputsGroupedBy;
        $this->OrgClassWidget->bindToController();

        // Call the FormController behavior update() method
        return $this->getClassExtension('Backend.Behaviors.FormController')->create();
    }

    public function update($recordId, $context = null)
    {
        // Manage Location Widget:
        // -----------------------
        $this->ManageLocationWidget = new ManageLocation($this);
        $this->ManageLocationWidget->alias = 'dropdownsmanagelocation';
        $this->ManageLocationWidget->form_group = $this->inputsGroupedBy;
        $this->ManageLocationWidget->bindToController();

        // Manage Coordinates Widget:
        // -----------------------
        $this->ManageCoordinatesWidget = new ManageCoordinates($this);
        $this->ManageCoordinatesWidget->alias = 'inputsmanagecoordinates';
        $this->ManageCoordinatesWidget->form_group = $this->inputsGroupedBy;
        $this->ManageCoordinatesWidget->bindToController();

        $model = SiteModel::findOrFail($recordId);
        $this->InitUpdateSiteWidgets($model);

        // Call the FormController behavior update() method
        return $this->getClassExtension('Backend.Behaviors.FormController')->update($recordId, $context);
    }


    public function listExtendModel($model, $definition = null){

        // Add custom properties to the model: "classrooms_count"
        $model['classrooms_count'] = $model->classrooms_count;
        return $model;
    }


    // Add custom properties to the model:
    protected function InitUpdateSiteWidgets($model)
    {
        // Initial Location values:
        if (!empty($this->ManageLocationWidget)){
            $this->ManageLocationWidget->selected_country_id = $model->country_id;
            $this->ManageLocationWidget->selected_state_id = $model->state_id;
            $this->ManageLocationWidget->selected_province_id = $model->province_id;
        }

        // Initial Coordinates values:
        if (!empty($this->ManageCoordinatesWidget)){
            $this->ManageCoordinatesWidget->initial_altitude = $model->altitude;
            $this->ManageCoordinatesWidget->initial_latitude = $model->latitude;
            $this->ManageCoordinatesWidget->initial_longitude = $model->longitude;
        }
    }

    /**
     * Add fields to the Site->Update form.
     */
    protected function formExtendFields($host)
    {
        // To know when it's the "UPDATE" form: ($host->config->context == 'update')
    }


    /**
     * Called after the creation form is saved.
     * @param Model
     */
    public function formAfterCreate($model)
    {
        // Get the "classroom_id" field before it gets purged
        $classroom_id = intval(Input::get($this->inputsGroupedBy)['classroom_id']);
        $organization_id = intval(Input::get($this->inputsGroupedBy)['organization_id']);

        if (!empty($classroom_id)){
            // Create/Update the relationships with the Classrooms
            // ---------------------------------------------------
            $model->classrooms()->sync(array($classroom_id));
        }
        if (!empty($organization_id)){
            // Create/Update the relationships with the Classrooms
            // ---------------------------------------------------
            $model->organization_id = $organization_id;
            $model->save();
        }
        return $model;
    }

    /**
     * Called after the updating form is saved.
     * @param Model
     */
    public function formAfterUpdate($model)
    {
        // Get the Coordinates parameters
        $altitude = trim(Input::get($this->inputsGroupedBy)['altitude']);
        $latitude = trim(Input::get($this->inputsGroupedBy)['latitude']);
        $longitude = trim(Input::get($this->inputsGroupedBy)['longitude']);

        // Store them as NULL if they are empty
        $model->altitude = (empty($altitude) ? null : $altitude);
        $model->latitude = (empty($latitude) ? null : $latitude);
        $model->longitude = (empty($longitude) ? null : $longitude);

        $model->save(); // save changes into the model
        return $model;
    }

    /**
     * Add an existing related model to the primary model
     */
    public function onRelationManageSync()
    {
        $modelID = intval($this->params[0]);
        $model = SiteModel::findOrFail($modelID);

        if (($checkedIds = post('checked')) && is_array($checkedIds)) {
            $model->classrooms()->sync($checkedIds);
        }

        $this->initRelation($model);
        $relatedModel = $this->getClassExtension('Backend.Behaviors.RelationController');

        //$div_to_update_ID = $relatedModel->relationGetId('view');
        $div_to_update_ID = "Sites-update-RelationController-classrooms";

        return ['#'.$div_to_update_ID => $relatedModel->relationRender('classrooms')];
    }

    /*
     * Filter list by the User access level
     */
    public function listExtendQuery($query, $definition = null){

        // EXAMPLE: $query->where('assigned_to', '=', $user->id);
        $site_ids = [];

        switch($this->currentUser_accessLevel)
        {
            case 1: //                      TEACHERS
                // -----------------------------------------------------------------------
                //              Show only users in the same Classroom(s)
                // -----------------------------------------------------------------------

                // 1) Get ALL classrooms the current user belongs to
                $classrooms = CnesMeteoHelpers::getClassroomsRelated_for_Filtering($this->currentUser_backendModel);
                // 2) Get ALL site_id of all related classrooms
                $site_ids = CnesMeteoHelpers::getClassroomsSitesIDs_Filtered($classrooms);
                break;

            case 2: //                      MANAGERS
                // -----------------------------------------------------------------------
                //              Show only users in the same Organization(s)
                // -----------------------------------------------------------------------

                // 1) Get ALL organizations the current user belongs to
                $organizations = CnesMeteoHelpers::getOrganizationsRelated_for_Filtering($this->currentUser_backendModel);
                // 2) Get ALL classroom_ids of all related organizations
                $site_ids = CnesMeteoHelpers::getOrganizationsSitesIDs_Filtered($organizations);
                break;
        }

        if ($this->currentUser_accessLevel < 3)
        {
            // Because whereIn generates error with an empty array
            // https://github.com/laravel/framework/issues/5296
            if (empty($site_ids)) {
                $site_ids = ['0'];
            };
            // Filter the query for the list:
            $query->whereIn( 'id', $site_ids );
        }
    }
}