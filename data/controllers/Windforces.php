<?php namespace CnesMeteo\Data\Controllers;

use Input;
use Flash;
use BackendMenu;
use BackendAuth;

class Windforces extends MeasurementsControllerBase
{
    public $requiredPermissions = ['cnesmeteo.access_data_windforces'];

    // Widgets
    public $inputsGroupedBy = 'Windforces';
    public $inputGroupBaseName = 'Windforce';
    public $measurementType = 'windforce'; // enum for the "measurement_type" !!!

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('CnesMeteo.Data', 'data', 'windforces');
    }
}