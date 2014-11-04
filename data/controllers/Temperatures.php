<?php namespace CnesMeteo\Data\Controllers;

use Illuminate\Support\Facades\Log;
use Input;
use Flash;
use BackendMenu;
use BackendAuth;
use CnesMeteo\Data\Models\Measurement as MeasurementModel;

class Temperatures extends MeasurementsControllerBase
{
    public $requiredPermissions = ['cnesmeteo.access_data_temperatures'];

    // Widgets
    public $inputsGroupedBy = 'Temperatures';
    public $inputGroupBaseName = 'Temperature';
    public $measurementType = 'temperature'; // enum for the "measurement_type" !!!

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('CnesMeteo.Data', 'data', 'temperatures');
    }
}