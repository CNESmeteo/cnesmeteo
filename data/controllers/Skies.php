<?php namespace CnesMeteo\Data\Controllers;

use Input;
use Flash;
use BackendMenu;
use BackendAuth;

class Skies extends MeasurementsControllerBase
{
    public $requiredPermissions = ['cnesmeteo.access_data_skies'];

    // Widgets
    public $inputsGroupedBy = 'Skies';
    public $inputGroupBaseName = 'Sky';
    public $measurementType = 'sky'; // enum for the "measurement_type" !!!

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('CnesMeteo.Data', 'data', 'skies');
    }
}