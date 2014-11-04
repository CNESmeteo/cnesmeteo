<?php namespace CnesMeteo\User\Controllers;

use CnesMeteo\User\Modules\Backend\FormWidgets\OrgClassDropdown;
use CnesMeteo\User\Modules\Backend\FormWidgets\ManageLocation;
use CnesMeteo\User\Modules\Backend\FormWidgets\ManageCoordinates;
use Input;
use Flash;
use BackendMenu;
use BackendAuth;
use Mail;
use Config;
use Backend\Classes\Controller;
use CnesMeteo\User\Models\Organization as OrganizationModel;
use RainLab\User\Models\Settings as UserSettings;
use CnesMeteo\User\Helpers as CnesMeteoHelpers;

class Organizations extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',
        'Backend.Behaviors.RelationController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';
    public $relationConfig = 'config_relation.yaml';

    public $requiredPermissions = ['cnesmeteo.access_organizations'];
    public $currentUser_backendModel = null;
    public $currentUser_accessLevel = null;

    public $bodyClass = 'compact-container';

    // Widgets
    public $ManageLocationWidget = null;
    public $ManageCoordinatesWidget = null;
    public $inputsGroupedBy = 'Organization';

    public function __construct()
    {
        parent::__construct();

        // Get Current Backend User
        $this->currentUser_backendModel = BackendAuth::getUser();
        // Set Access Level
        $this->currentUser_accessLevel = CnesMeteoHelpers::getUserAccessLevel($this->currentUser_backendModel);

        BackendMenu::setContext('CnesMeteo.User', 'user', 'organizations');
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

        $model = OrganizationModel::findOrFail($recordId);
        $this->InitUpdateSiteWidgets($model);

        // Call the FormController behavior update() method
        return $this->getClassExtension('Backend.Behaviors.FormController')->update($recordId, $context);
    }

    public function listExtendModel($model, $definition = null){

        // Add custom properties to the model: "users_count" and "classrooms_count"
        $model['users_count'] = $model->users_count;
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

    public function formAfterCreate($model)
    {
        // Is activation necessary?
        $automaticActivation = UserSettings::get('auto_activation', true);

        /*
         * If activation is required, send the email
         */
        if (!$automaticActivation) {
            $this->sendActivationEmail($model);
        }
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
     * Manually activate a organization
     */
    public function update_onActivate($recordId = null)
    {
        $model = $this->formFindModelObject($recordId);

        if (!empty($model)){
            $model->attemptActivation($model->activation_code);

            Flash::success('Organization has been activated successfully!');

            if ($redirect = $this->makeRedirect('update', $model))
                return $redirect;
            else
                return null;
        }else
            return null;
    }


    /**
     * Sends the activation email to a user
     * @param  User $user
     * @return void
     */
    protected function sendActivationEmail($model)
    {
        $code = implode('!', [$model->id, $model->getActivationCode()]);
        $link = $this->currentPageUrl([
            $this->property('paramCode') => $code
        ]);

        $data = [
            'name' => $model->name,
            'link' => $link,
            'code' => $code
        ];

        Mail::send('cnesmeteo.user::mail.activate', $data, function($message) use ($model)
        {
            $admin_email = Config::get('mail.from'); // Array "address" and "name"
            if ( (!empty($admin_email)) && (!empty($admin_email['address'])) ){
                $admin_email = $admin_email['address'];
            }
            $message->to($admin_email, $model->name);
        });
    }


    /**
     * Add an existing related model to the primary model
     */
    /*
    public function onRelationManageSync()
    {
        $modelID = intval($this->params[0]);
        $model = OrganizationModel::findOrFail($modelID);

        if (($checkedIds = post('checked')) && is_array($checkedIds)) {
            $model->classrooms()->sync($checkedIds);
        }

        $this->initRelation($model);
        $relatedModel = $this->getClassExtension('Backend.Behaviors.RelationController');

        //$div_to_update_ID = $relatedModel->relationGetId('view');
        $div_to_update_ID = "Sites-update-RelationController-classrooms";

        return ['#'.$div_to_update_ID => $relatedModel->relationRender('classrooms')];
    }
    */


    /*
     * Filter list by the User access level
     */
    public function listExtendQuery($query, $definition = null){

        // EXAMPLE: $query->where('assigned_to', '=', $user->id);
        $organizations_ids = [];

        if($this->currentUser_accessLevel == 2)
        {
            //                              MANAGERS
            // -----------------------------------------------------------------------
            //              Show only users in the same Organization(s)
            // -----------------------------------------------------------------------

            // 1) Get ALL organizations_ids the current user belongs to
            $organizations_ids = CnesMeteoHelpers::getOrganizationsRelated_for_Filtering($this->currentUser_backendModel, true);
        }

        if ($this->currentUser_accessLevel < 3)
        {
            // Because whereIn generates error with an empty array
            // https://github.com/laravel/framework/issues/5296
            if (empty($organizations_ids)) {
                $organizations_ids = ['0'];
            };
            // Filter the query for the list:
            $query->whereIn( 'id', $organizations_ids );
        }
    }

}