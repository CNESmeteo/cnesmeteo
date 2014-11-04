<?php namespace CnesMeteo\User\Modules\Backend\FormWidgets;

use Backend\Classes\WidgetBase;
use CnesMeteo\Localization\Models\Country;
use CnesMeteo\Localization\Models\State;
use CnesMeteo\Localization\Models\Province;
use Input;
use Request;
use Cache;
use Lang;

class ManageLocation extends WidgetBase
{
    public $defaultAlias = "ManageLocationDropdowns";
    public $form_group = 'User'; // default
    public $selected_country_id = null;     // Set when creating the widget with the model values
    public $selected_state_id = null;       // Set when creating the widget with the model values
    public $selected_province_id = null;    // Set when creating the widget with the model values

    public function widgetDetails()
    {
        return [
            'name'        => 'Define location: Country->State->Province',
            'description' => 'Select the general location for the User/Organization/Site'
        ];
    }

    public function render(){
        $this->prepareVars();
        return $this->makePartial('ManageLocationDropdowns');
    }

    public function prepareVars(){
        $this->vars['selected_country_id'] = $this->selected_country_id;
        $this->vars['selected_state_id'] = $this->selected_state_id;
        $this->vars['selected_province_id'] = $this->selected_province_id;

        $this->vars['countries'] = $this->getCountryOptions();
        $this->vars['states'] = [];
        $this->vars['provinces'] = [];

        if (!empty($this->vars['countries'])){

            // Load initial states:
            $first_country_id = array_keys($this->vars['countries'])[0];
            if (!empty($this->selected_country_id)){
                $first_country_id = $this->selected_country_id;
            }

            $this->vars['states'] = $this->getStateOptions($first_country_id);

            if (!empty($this->vars['states'])){
                // Load initial provinces:
                $first_state_id = array_keys($this->vars['states'])[0];
                if (!empty($this->selected_country_id)){
                    $first_state_id = $this->selected_province_id;
                }

                $this->vars['provinces'] = $this->getProvinceOptions($first_state_id);
            }
        }
    }

    public function loadAssets(){
        //$this->addJs('js/jsFileName.js');
        //$this->addCss('js/cssFileName.css');
    }

    public function defineProperties()
    {
        return [
            'country' => [
                'title' => 'Country',
                'type' => 'dropdown',
                'placeholder' => 'Select a country'
            ],
            'state' => [
                'title' => 'State',
                'type' => 'dropdown',
                'placeholder' => 'Select a state',
                'depends' => ['country']
            ],
            'province' => [
                'title' => 'Province',
                'type' => 'dropdown',
                'placeholder' => 'Select a province',
                'depends' => ['state']
            ]
        ];
    }

    public function getCountryOptions()
    {
        return Country::where('is_enabled', '=', true)
                       ->lists('name', 'id');
    }

    public function getStateOptions($country_id = null)
    {
        $states = [];
        if (!empty($country_id)){
            $states = State::where('country_id', '=', $country_id)
                ->lists('name', 'id');
        }
        return $states;
    }

    public function getProvinceOptions($state_id = null)
    {
        $provinces = [];
        if (!empty($state_id)){
            $provinces = Province::where('state_id', '=', $state_id)
                ->lists('name', 'id');
        }
        return $provinces;
    }


    /*
     *  EVENTS
     */
    public function onCountryChange()
    {
        $this->vars['states'] = [];
        $country_id = intval(Input::get($this->form_group)['country_id']);

        if (!empty($country_id)){
            $this->vars['states'] = $this->getStateOptions($country_id);
        }

        // render HTML for the dropdown options using a Twig partial:
        $list = [ 'list' => $this->vars['states'] ];
        $rendered_html = $this->makePartial('dropdownOptions', $list);
        return ['#Form-form-field-'.$this->form_group.'-state_id' => $rendered_html];
    }

    public function onStateChange()
    {
        $this->vars['provinces'] = [];
        $state_id = intval(Input::get($this->form_group)['state_id']);

        if (!empty($state_id)){
            $this->vars['provinces'] = $this->getProvinceOptions($state_id);
        }

        // render HTML for the dropdown options using a Twig partial:
        $list = [ 'list' => $this->vars['provinces'] ];
        $rendered_html = $this->makePartial('dropdownOptions', $list);
        return ['#Form-form-field-'.$this->form_group.'-province_id' => $rendered_html];
    }
}