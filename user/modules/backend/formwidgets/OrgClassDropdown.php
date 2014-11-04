<?php namespace CnesMeteo\User\Modules\Backend\FormWidgets;

use Backend\Classes\WidgetBase;
use CnesMeteo\User\Models\Organization;
use CnesMeteo\User\Models\Classroom;
use Input;
use Request;
use Cache;
use Lang;

class OrgClassDropdown extends WidgetBase
{
    public $defaultAlias = "OrgClassDropdown";
    public $form_group = 'User'; // default
    public $selected_organization_id = null;  // Set when creating the widget with the model values
    public $selected_classroom_id = null;     // Set when creating the widget with the model values

    public function widgetDetails()
    {
        return [
            'name'        => 'Dropdown Organization->Classroom',
            'description' => 'Select a organization and one of their associated classrooms'
        ];
    }

    public function render(){
        $this->prepareVars();
        return $this->makePartial('OrgClassDropdown');
    }

    public function prepareVars(){
        $this->vars['selected_organization_id'] = $this->selected_organization_id;
        $this->vars['selected_classroom_id'] = $this->selected_classroom_id;

        $this->vars['organizations'] = $this->getOrganizationOptions();

        $first_organization_id = null;
        if (!empty($this->vars['organizations'])){

            $first_organization_id = array_keys($this->vars['organizations'])[0];

            if (!empty($this->selected_organization_id)){
                $first_organization_id = $this->selected_organization_id;
            }

        }

        $this->vars['classrooms'] = $this->getClassroomOptions($first_organization_id);
    }

    public function loadAssets(){
        //$this->addJs('js/OrgClassDropdown.js');
    }

    public function defineProperties()
    {
        return [
            'organization' => [
                'title' => 'Organization',
                'type' => 'dropdown',
                'placeholder' => 'Select a organization'
            ],
            'classroom' => [
                'title' => 'Classroom',
                'type' => 'dropdown',
                'placeholder' => 'Select a classroom',
                'depends' => ['organization']
            ]
        ];
    }

    public function getOrganizationOptions()
    {
        return Organization::all()->lists('name', 'id');
    }

    public function getClassroomOptions($organization_id = null)
    {
        $classrooms = [];
        if (!empty($organization_id)){
            $classrooms = Classroom::where('organization_id', '=', $organization_id)->lists('name', 'id');
        }
        return $classrooms;
    }

    public function onOrganizationChange()
    {
        $this->vars['classrooms'] = [];
        $organization_id = intval(Input::get($this->form_group)['organization_id']);

        if (!empty($organization_id)){
            $this->vars['classrooms'] = $this->getClassroomOptions($organization_id);
        }

        // render HTML for the dropdown options using a Twig partial:
        $rendered_html = $this->makePartial('classroomsDropdownOptions');
        return ['#Form-form-field-'.$this->form_group.'-classroom_id' => $rendered_html];
    }
}