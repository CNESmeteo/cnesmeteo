<?php namespace CnesMeteo\Data\Controllers;

use Input;
use Flash;
use BackendMenu;
use BackendAuth;

class Pressures extends MeasurementsControllerBase
{
    public $requiredPermissions = ['cnesmeteo.access_data_pressures'];

    // Widgets
    public $inputsGroupedBy = 'Pressures';
    public $inputGroupBaseName = 'Pressure';
    public $measurementType = 'pressure'; // enum for the "measurement_type" !!!

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('CnesMeteo.Data', 'data', 'pressures');
    }
}