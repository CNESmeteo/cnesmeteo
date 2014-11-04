<?php namespace CnesMeteo\Data\Controllers;

use Illuminate\Support\Facades\Log;
use Input;
use Flash;
use BackendMenu;
use BackendAuth;
use CnesMeteo\User\Helpers as CnesMeteoHelpers;
use CnesMeteo\Data\Models\Measurement as MeasurementModel;

class Aerosols extends MeasurementsControllerBase
{
    public $requiredPermissions = ['cnesmeteo.access_data_aerosols'];

    // Widgets
    public $inputsGroupedBy = 'Aerosols';
    public $inputGroupBaseName = 'Aerosol';
    public $measurementType = 'aerosols'; // enum for the "measurement_type" !!!

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('CnesMeteo.Data', 'data', 'aerosols');
    }

    /**
     * Called after the creation or updating form is saved.
     * @param Model
     */
    public function formAfterSave($model)
    {
        // Get AOT input data, remove the empty values by changing '' for NULL
        // --------------------------------------------------------------------
        $AOT_RED = Input::get($this->inputGroupBaseName)['aot_red'];
        $AOT_GREEN = Input::get($this->inputGroupBaseName)['aot_green'];
        $AOT_BLUE = Input::get($this->inputGroupBaseName)['aot_blue'];

        if (empty($AOT_RED)){ $model->aot_red = null; }
        if (empty($AOT_GREEN)){ $model->aot_green = null; }
        if (empty($AOT_BLUE)){ $model->aot_blue = null; }

        if ( (empty($AOT_RED)) || (empty($AOT_GREEN)) || (empty($AOT_BLUE))){
            $model->save();
        }

        return $model;
    }


}