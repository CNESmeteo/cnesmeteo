<?php namespace CnesMeteo\Data\Controllers;

use Input;
use Flash;
use BackendMenu;
use BackendAuth;

class Precipitations extends MeasurementsControllerBase
{
    public $requiredPermissions = ['cnesmeteo.access_data_precipitations'];

    // Widgets
    public $inputsGroupedBy = 'Precipitations';
    public $inputGroupBaseName = 'Precipitation';
    public $measurementType = 'rainfall'; // enum for the "measurement_type" !!!

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('CnesMeteo.Data', 'data', 'precipitations');
    }
}