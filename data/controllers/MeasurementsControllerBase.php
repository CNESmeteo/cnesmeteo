<?php namespace CnesMeteo\Data\Controllers;

use CnesMeteo\User\Modules\Backend\FormWidgets\OrgUserSiteDropdown;
use Flash;
use BackendMenu;
use BackendAuth;
use Backend\Classes\Controller;
use CnesMeteo\User\Helpers as CnesMeteoHelpers;
use CnesMeteo\Data\Models\Measurement as MeasurementModel;

class MeasurementsControllerBase extends Controller
{
    //protected $primaryKey = 'measurement_id'; // extends the measurement info

    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    public $bodyClass = 'compact-container';

    // Widgets
    public $OrgUserSiteWidget = null;
    public $new_measurement_id = 0;


    public function listExtendModel($model, $definition = null){

        // Add custom properties to the model:
        $model['user_name'] = $model->user_name;
        $model['organization_name'] = $model->organization_name;
        $model['site_name'] = $model->site_name;

        return $model;
    }


    public function create()
    {
        // Widgets: (create and bind)
        $this->OrgUserSiteWidget = new OrgUserSiteDropdown($this);
        $this->OrgUserSiteWidget->alias = 'dropdownorgusersite';
        $this->OrgUserSiteWidget->form_group = $this->inputsGroupedBy;
        $this->OrgUserSiteWidget->bindToController();

        // Call the FormController behavior update() method
        return $this->getClassExtension('Backend.Behaviors.FormController')->create();
    }

    public function update($recordId, $context = null)
    {
        // User, Organization and Site selection Widget:
        // ---------------------------------------------
        $this->OrgUserSiteWidget = new OrgUserSiteDropdown($this);
        $this->OrgUserSiteWidget->alias = 'dropdownorgusersite';
        $this->OrgUserSiteWidget->form_group = $this->inputsGroupedBy;
        $this->OrgUserSiteWidget->bindToController();

        $measurement_model = MeasurementModel::findOrFail($recordId);
        if (!empty($measurement_model)){
            $this->InitUpdateOrgUserSiteWidget($measurement_model);
        }

        // Call the FormController behavior update() method
        return $this->getClassExtension('Backend.Behaviors.FormController')->update($recordId, $context);
    }

    // Add custom properties to the model:
    protected function InitUpdateOrgUserSiteWidget($measurement_model)
    {
        // Initial widget values:
        if (!empty($this->OrgUserSiteWidget)){
            $this->OrgUserSiteWidget->selected_user_id = $measurement_model->user_id;
            $this->OrgUserSiteWidget->selected_organization_id = $measurement_model->organization_id;
            $this->OrgUserSiteWidget->selected_site_id = $measurement_model->site_id;
        }
    }


    /**
     * Called before the creation form is saved.
     * @param Model
     */
    public function formBeforeCreate($model)
    {
        return CnesMeteoHelpers::processMeasurementForm_BeforeCreate($this, $model);
    }

    /**
     * Called after the creation form is saved.
     * @param Model
     */
    public function formAfterCreate($model)
    {
        return CnesMeteoHelpers::processMeasurementForm_AfterCreate($this, $model);
    }

    /**
     * Called before the updating form is saved.
     * @param Model
     */
    public function formBeforeUpdate($model)
    {
        return CnesMeteoHelpers::processMeasurementForm_BeforeUpdate($this, $model);
    }

    /**
     * Called after the form model is deleted.
     * @param Model
     */
    public function formAfterDelete($model)
    {
        return CnesMeteoHelpers::processMeasurementForm_AfterDelete($this, $model);
    }
}