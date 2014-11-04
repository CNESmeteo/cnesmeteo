<?php namespace CnesMeteo\User\Modules\Backend\FormWidgets;

use Backend\Classes\WidgetBase;
use Input;
use Request;
use Cache;
use Lang;

class ManageCoordinates extends WidgetBase
{
    public $defaultAlias = "ManageCoordinatesInput";
    public $form_group = 'User'; // default
    public $initial_latitude = null;     // Set when creating the widget with the model values
    public $initial_longitude = null;    // Set when creating the widget with the model values
    public $initial_altitude = null;     // Set when creating the widget with the model values

    public function widgetDetails()
    {
        return [
            'name'        => 'Define GPS coordinates',
            'description' => 'Latitude, Longitude and Altitude'
        ];
    }

    public function render(){
        $this->prepareVars();
        return $this->makePartial('ManageCoordinatesInputs');
    }

    public function prepareVars(){
        $this->vars['initial_latitude']  = $this->initial_latitude;
        $this->vars['initial_longitude'] = $this->initial_longitude;
        $this->vars['initial_altitude']  = $this->initial_altitude;
    }

    public function loadAssets(){
        //$this->addJs('js/jsFileName.js');
        //$this->addCss('js/cssFileName.css');
    }

    public function defineProperties()
    {
        return [
            'latitude' => [
                'title' => 'Latitude',
                'type' => 'text'
            ],
            'longitude' => [
                'title' => 'Longitude',
                'type' => 'text'
            ],
            'altitude' => [
                'title' => 'Altitude',
                'type' => 'number'
            ]
        ];
    }


    /*
     *  EVENTS
     */
    public function onLongitudeChange()
    {

        $longitude = intval(Input::get($this->form_group)['longitude']);
        $latitude = intval(Input::get($this->form_group)['latitude']);

        if ( (!empty($longitude)) && (!empty($latitude)) ){
            // Autofill Timezone
        }

        /*
        // render HTML for the dropdown options using a Twig partial:
        $list = [ 'list' => $this->vars['states'] ];
        $rendered_html = $this->makePartial('dropdownOptions', $list);
        return ['#Form-form-field-'.$this->form_group.'-state_id' => $rendered_html];
        */
    }
}