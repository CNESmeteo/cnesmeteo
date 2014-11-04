<?php namespace CnesMeteo\User\Controllers;

use CnesMeteo\User\Models\Classroom;
use CnesMeteo\User\Models\User;
use CnesMeteo\User\Modules\Backend\FormWidgets\OrgClassDropdown;
use CnesMeteo\User\Modules\Backend\FormWidgets\ManageLocation;
use Input;
use Flash;
use BackendMenu;
use BackendAuth;
use Backend\Classes\Controller;
use CnesMeteo\User\Helpers as CnesMeteoHelpers;
use CnesMeteo\User\Models\User as UserModel;

class Users extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',
        'Backend.Behaviors.RelationController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';
    public $relationConfig = 'config_relation.yaml';

    public $requiredPermissions = ['cnesmeteo.access_users'];
    public $currentUser_backendModel = null;
    public $currentUser_accessLevel = null;

    public $bodyClass = 'compact-container';

    // Widgets
    public $OrgClassWidget = null;
    public $ManageLocationWidget = null;
    public $inputsGroupedBy = 'User';

    public function __construct()
    {
        parent::__construct();

        // Get Current Backend User
        $this->currentUser_backendModel = BackendAuth::getUser();
        // Set Access Level
        $this->currentUser_accessLevel = CnesMeteoHelpers::getUserAccessLevel($this->currentUser_backendModel);

        // Widgets: (create and bind)
        $this->OrgClassWidget = new OrgClassDropdown($this);
        $this->OrgClassWidget->alias = 'dropdownorgclass';
        $this->OrgClassWidget->form_group = $this->inputsGroupedBy;
        $this->OrgClassWidget->bindToController();

        BackendMenu::setContext('CnesMeteo.User', 'user', 'users');
    }



    public function update($recordId, $context = null)
    {
        // Manage Location Widget:
        // -----------------------
        $this->ManageLocationWidget = new ManageLocation($this);
        $this->ManageLocationWidget->alias = 'dropdownsmanagelocation';
        $this->ManageLocationWidget->form_group = $this->inputsGroupedBy;
        $this->ManageLocationWidget->bindToController();

        $model = UserModel::findOrFail($recordId);
        $this->InitUpdateSiteWidgets($model);

        // Call the FormController behavior update() method
        return $this->getClassExtension('Backend.Behaviors.FormController')->update($recordId, $context);
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
    }


    /**
     * Called after the creation form is saved.
     * @param Model
     */
    public function formAfterCreate($model)
    {
        // Get the "group_id", "organization_id", "classroom_id" fields before they gets purged:
        $organization_id = intval(Input::get($this->inputsGroupedBy)['organization_id']);
        $classroom_id = intval(Input::get($this->inputsGroupedBy)['classroom_id']);

        // ORGANIZATION
        if (!empty($organization_id)){
            // Create/Update the relationships:
            // --------------------------------
            $model->organizations()->attach($organization_id);
            //$model->organizations()->sync(array($organization_id));
        }
        // CLASSROOM
        if (!empty($classroom_id)){
            // Create/Update the relationships with the Classrooms
            // ---------------------------------------------------
            $model->classrooms()->attach($classroom_id);
            //$model->classrooms()->sync(array($classroom_id));
        }

        return $model;
    }

    /**
     * Called after the updating form is saved.
     * @param Model
     */
    public function formAfterUpdate($model)
    {
        //$model->save(); // save changes into the model
        $address = trim(Input::get($this->inputsGroupedBy)['address']);
        if (empty($address)){
            $model->address = null;
            $model->save();
        }

        return $model;
    }

    /**
     * Manually activate a user
     */
    public function update_onActivate($recordId = null)
    {
        $model = $this->formFindModelObject($recordId);

        if (!empty($model)){
            $model->attemptActivation($model->activation_code);

            Flash::success('User has been activated successfully!');

            if ($redirect = $this->makeRedirect('update', $model))
                return $redirect;
            else
                return null;
        }else
            return null;
    }

    /**
     * Add available permission fields to the User form.
     */
    protected function formExtendFields($host)
    {
        $permissionFields = [];
        foreach (BackendAuth::listPermissions() as $permission) {

            $fieldName = 'permissions['.$permission->code.']';

            if (CnesMeteoHelpers::startsWith(strtolower($permission->code), 'cnesmeteo')){
                $fieldConfig = [
                    'label' => $permission->label,
                    'comment' => $permission->comment,
                    'type' => 'checkbox', //  checkbox || switch
                ];

                if (isset($permission->tab))
                    $fieldConfig['tab'] = 'Permissions (not Inherited)'; // TODO: Text Localization!!!

                $permissionFields[$fieldName] = $fieldConfig;
            }
        }

        $host->addSecondaryTabFields($permissionFields);
    }

    /*
     * Filter list by the User access level
     */
    public function listExtendQuery($query, $definition = null)
    {
        // EXAMPLE: $query->where('assigned_to', '=', $user->id);
        $user_ids = [];

        switch($this->currentUser_accessLevel)
        {
            case 1: //                      TEACHERS
                // -----------------------------------------------------------------------
                //              Show only users in the same Classroom(s)
                // -----------------------------------------------------------------------

                // 1) Get ALL classrooms the current user belongs to
                $classrooms = CnesMeteoHelpers::getClassroomsRelated_for_Filtering($this->currentUser_backendModel);
                // 2) Get ALL user_id of all related classrooms filtered by the current user access level
                $user_ids = CnesMeteoHelpers::getClassroomsUserIDs_Filtered($classrooms,
                                            $this->currentUser_backendModel->id,
                                            $this->currentUser_accessLevel);
                break;

            case 2: //                      MANAGERS
                // -----------------------------------------------------------------------
                //              Show only users in the same Organization(s)
                // -----------------------------------------------------------------------

                // 1) Get ALL organizations the current user belongs to
                $organizations = CnesMeteoHelpers::getOrganizationsRelated_for_Filtering($this->currentUser_backendModel);
                // 2) Get ALL user_id of all related organizations filtered by the current user access level
                $user_ids = CnesMeteoHelpers::getOrganizationUserIDs_Filtered($organizations,
                                            $this->currentUser_backendModel->id,
                                            $this->currentUser_accessLevel);
                break;
        }

        if ($this->currentUser_accessLevel < 3)
        {
            // Because whereIn generates error with an empty array
            // https://github.com/laravel/framework/issues/5296
            if (empty($user_ids)) {
                $user_ids = ['0'];
            };
            // Filter the query for the list:
            $query->whereIn( 'id', $user_ids );
        }
    }
}