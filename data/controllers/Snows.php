<?php namespace CnesMeteo\Data\Controllers;

use Input;
use Flash;
use BackendMenu;
use BackendAuth;

class Snows extends MeasurementsControllerBase
{
    public $requiredPermissions = ['cnesmeteo.access_data_snows'];

    // Widgets
    public $inputsGroupedBy = 'Snows';
    public $inputGroupBaseName = 'Snow';
    public $measurementType = 'snow'; // enum for the "measurement_type" !!!

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('CnesMeteo.Data', 'data', 'snows');
    }
}