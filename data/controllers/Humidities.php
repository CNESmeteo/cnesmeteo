<?php namespace CnesMeteo\Data\Controllers;

use Input;
use Flash;
use BackendMenu;
use BackendAuth;

class Humidities extends MeasurementsControllerBase
{
    public $requiredPermissions = ['cnesmeteo.access_data_humidities'];

    // Widgets
    public $inputsGroupedBy = 'Humidities';
    public $inputGroupBaseName = 'Humidity';
    public $measurementType = 'humidity'; // enum for the "measurement_type" !!!

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('CnesMeteo.Data', 'data', 'humidities');
    }
}